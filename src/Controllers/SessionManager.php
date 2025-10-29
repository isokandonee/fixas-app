<?php

class SessionManager {
    private static $instance = null;
    private const SESSION_LIFETIME = 1800; // 30 minutes
    private const REGENERATE_TIME = 300; // 5 minutes
    
    private function __construct() {
        $this->initializeSession();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize secure session
     */
    private function initializeSession() {
        // Set secure session parameters
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', self::SESSION_LIFETIME);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => self::SESSION_LIFETIME,
                'gc_maxlifetime' => self::SESSION_LIFETIME,
                'cookie_secure' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax'
            ]);
        }
        
        // Check if session needs regeneration
        if (!isset($_SESSION['last_regeneration'])) {
            $this->regenerateSession();
        } elseif (time() - $_SESSION['last_regeneration'] > self::REGENERATE_TIME) {
            $this->regenerateSession();
        }
    }
    
    /**
     * Regenerate session ID
     */
    public function regenerateSession() {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    /**
     * Set session data
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session data
     */
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Remove session data
     */
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Check if session data exists
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Destroy session
     */
    public function destroy() {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    /**
     * Validate user session
     */
    public function isValidSession() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['last_activity']) && 
               (time() - $_SESSION['last_activity'] < self::SESSION_LIFETIME);
    }
    
    /**
     * Update last activity time
     */
    public function updateActivity() {
        $_SESSION['last_activity'] = time();
    }
}