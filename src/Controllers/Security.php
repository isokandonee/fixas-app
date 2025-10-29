<?php

class Security {
    private static $instance = null;
    private const CSRF_TOKEN_LENGTH = 32;
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Generate a secure CSRF token
     */
    public function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        $result = hash_equals($_SESSION['csrf_token'], $token);
        // Regenerate token after verification
        $this->generateCSRFToken();
        return $result;
    }
    
    /**
     * Sanitize input data
     */
    public function sanitizeInput($data, $conn) {
        if (is_array($data)) {
            return array_map(function($item) use ($conn) {
                return $this->sanitizeInput($item, $conn);
            }, $data);
        }
        
        if (is_string($data)) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            $data = mysqli_real_escape_string($conn, $data);
        }
        
        return $data;
    }
    
    /**
     * Prevent XSS attacks in output
     */
    public function escapeOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email with more strict rules
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) &&
               preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }
    
    /**
     * Enhanced password validation
     */
    public function validatePassword($password) {
        // At least 8 characters
        // At least one uppercase letter
        // At least one lowercase letter
        // At least one number
        // At least one special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }
}