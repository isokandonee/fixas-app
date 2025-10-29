<?php

class IPTracker {
    private static $instance = null;
    private $db;
    
    private const MAX_SUSPICIOUS_ACTIONS = 3;
    private const BLOCK_DURATION = 86400; // 24 hours in seconds
    
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
     * Initialize IP tracking table
     */
    public function initializeTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS ip_tracking (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                country_code VARCHAR(2),
                is_proxy BOOLEAN DEFAULT FALSE,
                suspicious_actions INT DEFAULT 0,
                blocked_until TIMESTAMP NULL,
                last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ip (ip_address)
            )";
            
            $this->db->executeQuery($sql, "", []);
        } catch (Exception $e) {
            error_log("Failed to initialize IP tracking table: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check if an IP is suspicious or blocked
     */
    public function validateIP($ip) {
        try {
            // Update or create IP record
            $this->trackIP($ip);
            
            // Check if IP is currently blocked
            if ($this->isIPBlocked($ip)) {
                return false;
            }
            
            // Check if IP is a known proxy/VPN
            if ($this->isProxy($ip)) {
                $this->incrementSuspiciousActions($ip);
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("IP validation failed: " . $e->getMessage());
            // If validation fails, default to allowing the request
            return true;
        }
    }
    
    /**
     * Record suspicious activity for an IP
     */
    public function recordSuspiciousActivity($ip) {
        try {
            $sql = "UPDATE ip_tracking 
                   SET suspicious_actions = suspicious_actions + 1,
                       last_seen = CURRENT_TIMESTAMP
                   WHERE ip_address = ?";
            
            $this->db->executeQuery($sql, "s", [$ip]);
            
            // Check if we should block the IP
            $this->checkAndBlockIP($ip);
        } catch (Exception $e) {
            error_log("Failed to record suspicious activity: " . $e->getMessage());
        }
    }
    
    private function trackIP($ip) {
        try {
            // Try to get country code and proxy status (you would implement actual checks here)
            $country_code = $this->getCountryCode($ip);
            $is_proxy = $this->checkIfProxy($ip);
            
            $sql = "INSERT INTO ip_tracking (ip_address, country_code, is_proxy, last_seen) 
                   VALUES (?, ?, ?, CURRENT_TIMESTAMP)
                   ON DUPLICATE KEY UPDATE 
                   last_seen = CURRENT_TIMESTAMP,
                   country_code = VALUES(country_code),
                   is_proxy = VALUES(is_proxy)";
            
            $this->db->executeQuery($sql, "ssi", [$ip, $country_code, $is_proxy]);
        } catch (Exception $e) {
            error_log("Failed to track IP: " . $e->getMessage());
        }
    }
    
    private function isIPBlocked($ip) {
        try {
            $sql = "SELECT blocked_until FROM ip_tracking 
                   WHERE ip_address = ? 
                   AND blocked_until > CURRENT_TIMESTAMP";
            
            $stmt = $this->db->executeQuery($sql, "s", [$ip]);
            $result = $stmt->get_result();
            
            return $result->num_rows > 0;
        } catch (Exception $e) {
            error_log("Failed to check IP block status: " . $e->getMessage());
            return false;
        }
    }
    
    private function checkAndBlockIP($ip) {
        try {
            $sql = "SELECT suspicious_actions FROM ip_tracking WHERE ip_address = ?";
            $stmt = $this->db->executeQuery($sql, "s", [$ip]);
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row && $row['suspicious_actions'] >= self::MAX_SUSPICIOUS_ACTIONS) {
                $this->blockIP($ip);
            }
        } catch (Exception $e) {
            error_log("Failed to check and block IP: " . $e->getMessage());
        }
    }
    
    private function blockIP($ip) {
        try {
            $sql = "UPDATE ip_tracking 
                   SET blocked_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL ? SECOND) 
                   WHERE ip_address = ?";
            
            $this->db->executeQuery($sql, "is", [self::BLOCK_DURATION, $ip]);
        } catch (Exception $e) {
            error_log("Failed to block IP: " . $e->getMessage());
        }
    }
    
    private function getCountryCode($ip) {
        // In a real implementation, you would use a GeoIP service
        // For now, returning a dummy value
        return "XX";
    }
    
    private function checkIfProxy($ip) {
        // In a real implementation, you would check against a proxy/VPN database
        // For now, returning false
        return false;
    }
    
    private function isProxy($ip) {
        try {
            $sql = "SELECT is_proxy FROM ip_tracking WHERE ip_address = ?";
            $stmt = $this->db->executeQuery($sql, "s", [$ip]);
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row && $row['is_proxy'];
        } catch (Exception $e) {
            error_log("Failed to check proxy status: " . $e->getMessage());
            return false;
        }
    }
    
    private function incrementSuspiciousActions($ip) {
        $this->recordSuspiciousActivity($ip);
    }
}