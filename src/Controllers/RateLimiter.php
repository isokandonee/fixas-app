<?php

class RateLimiter {
    private static $instance = null;
    private $db;
    
    // Configuration
    private const MAX_ATTEMPTS = 5; // Maximum login attempts
    private const LOCKOUT_TIME = 900; // 15 minutes in seconds
    private const ATTEMPT_WINDOW = 300; // 5 minutes in seconds for counting attempts
    
    private function __construct() {
        $this->db = Database::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Create rate limiting table if it doesn't exist
     */
    public function initializeTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS rate_limits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                endpoint VARCHAR(50) NOT NULL,
                attempts INT DEFAULT 1,
                last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_blocked BOOLEAN DEFAULT FALSE,
                blocked_until TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ip_endpoint (ip_address, endpoint)
            )";
            
            $this->db->executeQuery($sql, "", []);
        } catch (Exception $e) {
            error_log("Failed to initialize rate limit table: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check if an IP is allowed to make a request
     */
    public function checkRequest($ip, $endpoint) {
        try {
            // Clean up old records first
            $this->cleanupOldRecords();
            
            // Check if IP is blocked
            if ($this->isIPBlocked($ip, $endpoint)) {
                return false;
            }
            
            // Get attempt count
            $attempts = $this->getAttemptCount($ip, $endpoint);
            
            // If exceeded limit, block the IP
            if ($attempts >= self::MAX_ATTEMPTS) {
                $this->blockIP($ip, $endpoint);
                return false;
            }
            
            // Record the attempt
            $this->recordAttempt($ip, $endpoint);
            
            return true;
        } catch (Exception $e) {
            error_log("Rate limit check failed: " . $e->getMessage());
            // If rate limiting fails, default to allowing the request
            return true;
        }
    }
    
    /**
     * Reset attempts for an IP after successful action
     */
    public function resetAttempts($ip, $endpoint) {
        try {
            $sql = "DELETE FROM rate_limits WHERE ip_address = ? AND endpoint = ?";
            $this->db->executeQuery($sql, "ss", [$ip, $endpoint]);
        } catch (Exception $e) {
            error_log("Failed to reset attempts: " . $e->getMessage());
        }
    }
    
    private function isIPBlocked($ip, $endpoint) {
        try {
            $sql = "SELECT blocked_until FROM rate_limits 
                   WHERE ip_address = ? AND endpoint = ? 
                   AND is_blocked = 1 
                   AND blocked_until > CURRENT_TIMESTAMP";
            
            $stmt = $this->db->executeQuery($sql, "ss", [$ip, $endpoint]);
            $result = $stmt->get_result();
            
            return $result->num_rows > 0;
        } catch (Exception $e) {
            error_log("Failed to check IP block status: " . $e->getMessage());
            return false;
        }
    }
    
    private function getAttemptCount($ip, $endpoint) {
        try {
            $sql = "SELECT COUNT(*) as attempt_count 
                   FROM rate_limits 
                   WHERE ip_address = ? 
                   AND endpoint = ? 
                   AND last_attempt > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL ? SECOND)";
            
            $stmt = $this->db->executeQuery($sql, "ssi", [$ip, $endpoint, self::ATTEMPT_WINDOW]);
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return (int)$row['attempt_count'];
        } catch (Exception $e) {
            error_log("Failed to get attempt count: " . $e->getMessage());
            return 0;
        }
    }
    
    private function blockIP($ip, $endpoint) {
        try {
            $sql = "UPDATE rate_limits 
                   SET is_blocked = 1, 
                       blocked_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL ? SECOND) 
                   WHERE ip_address = ? AND endpoint = ?";
            
            $this->db->executeQuery($sql, "iss", [self::LOCKOUT_TIME, $ip, $endpoint]);
        } catch (Exception $e) {
            error_log("Failed to block IP: " . $e->getMessage());
        }
    }
    
    private function recordAttempt($ip, $endpoint) {
        try {
            $sql = "INSERT INTO rate_limits (ip_address, endpoint, last_attempt) 
                   VALUES (?, ?, CURRENT_TIMESTAMP)";
            
            $this->db->executeQuery($sql, "ss", [$ip, $endpoint]);
        } catch (Exception $e) {
            error_log("Failed to record attempt: " . $e->getMessage());
        }
    }
    
    private function cleanupOldRecords() {
        try {
            // Remove old unblocked records
            $sql = "DELETE FROM rate_limits 
                   WHERE is_blocked = 0 
                   AND last_attempt < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL ? SECOND)";
            
            $this->db->executeQuery($sql, "i", [self::ATTEMPT_WINDOW]);
            
            // Remove expired blocks
            $sql = "UPDATE rate_limits 
                   SET is_blocked = 0 
                   WHERE is_blocked = 1 
                   AND blocked_until < CURRENT_TIMESTAMP";
            
            $this->db->executeQuery($sql, "", []);
        } catch (Exception $e) {
            error_log("Failed to cleanup old records: " . $e->getMessage());
        }
    }
}