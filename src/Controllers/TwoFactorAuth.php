<?php

class TwoFactorAuth {
    private static $instance = null;
    private $db;
    private $logger;
    private const SECRET_LENGTH = 16;
    private const VALID_WINDOW = 1; // Allow 30 seconds before/after
    
    private function __construct() {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
        
        // Require the PHP OATH library
        require_once __DIR__ . '/../vendor/autoload.php';
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Generate new 2FA secret for user
     */
    public function generateSecret($userId) {
        try {
            // Generate random secret
            $secret = $this->generateSecretKey();
            
            // Store in database
            $sql = "UPDATE users 
                   SET two_factor_secret = ?, 
                       two_factor_enabled = 0,
                       updated_at = CURRENT_TIMESTAMP 
                   WHERE id = ?";
            
            $this->db->executeQuery($sql, "si", [$secret, $userId]);
            
            return $secret;
        } catch (Exception $e) {
            $this->logger->error('2FA secret generation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Enable 2FA for user after verification
     */
    public function enable($userId, $code) {
        try {
            // Get user's secret
            $sql = "SELECT two_factor_secret FROM users WHERE id = ?";
            $stmt = $this->db->executeQuery($sql, "i", [$userId]);
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!$user || !$user['two_factor_secret']) {
                return false;
            }
            
            // Verify the code
            if ($this->verifyCode($user['two_factor_secret'], $code)) {
                // Enable 2FA
                $sql = "UPDATE users 
                       SET two_factor_enabled = 1,
                           updated_at = CURRENT_TIMESTAMP 
                       WHERE id = ?";
                
                $this->db->executeQuery($sql, "i", [$userId]);
                
                $this->logger->info('2FA enabled', ['user_id' => $userId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error('2FA enable failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Verify 2FA code
     */
    public function verify($userId, $code) {
        try {
            // Get user's secret
            $sql = "SELECT two_factor_secret FROM users WHERE id = ?";
            $stmt = $this->db->executeQuery($sql, "i", [$userId]);
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!$user || !$user['two_factor_secret']) {
                return false;
            }
            
            return $this->verifyCode($user['two_factor_secret'], $code);
        } catch (Exception $e) {
            $this->logger->error('2FA verification failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Disable 2FA for user
     */
    public function disable($userId, $code) {
        try {
            // Verify code before disabling
            if ($this->verify($userId, $code)) {
                $sql = "UPDATE users 
                       SET two_factor_enabled = 0,
                           two_factor_secret = NULL,
                           updated_at = CURRENT_TIMESTAMP 
                       WHERE id = ?";
                
                $this->db->executeQuery($sql, "i", [$userId]);
                
                $this->logger->info('2FA disabled', ['user_id' => $userId]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error('2FA disable failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Generate QR code for 2FA setup
     */
    public function getQRCode($userId, $email) {
        try {
            // Get user's secret
            $sql = "SELECT two_factor_secret FROM users WHERE id = ?";
            $stmt = $this->db->executeQuery($sql, "i", [$userId]);
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!$user || !$user['two_factor_secret']) {
                return false;
            }
            
            // Generate QR code URL
            $appName = urlencode("Fixas Bank");
            $secretKey = $user['two_factor_secret'];
            $email = urlencode($email);
            
            return "otpauth://totp/{$appName}:{$email}?secret={$secretKey}&issuer={$appName}";
        } catch (Exception $e) {
            $this->logger->error('QR code generation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Generate backup codes for user
     */
    public function generateBackupCodes($userId) {
        try {
            $codes = [];
            for ($i = 0; $i < 8; $i++) {
                $codes[] = $this->generateBackupCode();
            }
            
            // Store hashed backup codes
            $sql = "UPDATE users SET backup_codes = ? WHERE id = ?";
            $hashedCodes = password_hash(json_encode($codes), PASSWORD_DEFAULT);
            
            $this->db->executeQuery($sql, "si", [$hashedCodes, $userId]);
            
            return $codes;
        } catch (Exception $e) {
            $this->logger->error('Backup codes generation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    private function generateSecretKey() {
        return trim(Base32::encode(random_bytes(self::SECRET_LENGTH)), '=');
    }
    
    private function verifyCode($secret, $code) {
        $timestamp = floor(time() / 30);
        
        // Check valid window
        for ($i = -self::VALID_WINDOW; $i <= self::VALID_WINDOW; $i++) {
            $valid = hash_equals(
                $this->generateCode($secret, $timestamp + $i),
                $code
            );
            if ($valid) {
                return true;
            }
        }
        
        return false;
    }
    
    private function generateCode($secret, $timestamp) {
        $secret = Base32::decode($secret);
        
        // Pack timestamp into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timestamp);
        
        // Generate HMAC-SHA1 hash
        $hash = hash_hmac('SHA1', $time, $secret, true);
        
        // Get offset
        $offset = ord(substr($hash, -1)) & 0xF;
        
        // Generate 4-byte code
        $code = (
            ((ord($hash[$offset + 0]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        ) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }
    
    private function generateBackupCode() {
        return strtoupper(bin2hex(random_bytes(4)));
    }
}