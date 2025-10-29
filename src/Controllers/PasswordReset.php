<?php

class PasswordReset {
    private static $instance = null;
    private $db;
    private $logger;
    private $mailer;
    
    private const TOKEN_EXPIRY = 3600; // 1 hour
    
    private function __construct() {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
        // Initialize mailer here
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Request password reset
     */
    public function requestReset($email) {
        try {
            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + self::TOKEN_EXPIRY);
            
            // Update user record
            $sql = "UPDATE users 
                   SET password_reset_token = ?, 
                       password_reset_expires = ? 
                   WHERE email = ? 
                   AND status = 'active'";
            
            $stmt = $this->db->executeQuery($sql, "sss", [$token, $expires, $email]);
            
            if ($stmt->affected_rows > 0) {
                // Send reset email
                $this->sendResetEmail($email, $token);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error('Password reset request failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Validate reset token
     */
    public function validateToken($token) {
        try {
            $sql = "SELECT id, email 
                   FROM users 
                   WHERE password_reset_token = ? 
                   AND password_reset_expires > NOW() 
                   AND status = 'active'";
            
            $stmt = $this->db->executeQuery($sql, "s", [$token]);
            $result = $stmt->get_result();
            
            return $result->num_rows > 0 ? $result->fetch_assoc() : false;
        } catch (Exception $e) {
            $this->logger->error('Token validation failed', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Reset password
     */
    public function resetPassword($token, $newPassword) {
        try {
            $userData = $this->validateToken($token);
            if (!$userData) {
                return false;
            }
            
            // Start transaction
            $this->db->getConnection()->begin_transaction();
            
            // Store old password in history
            $this->storePasswordHistory($userData['id'], $newPassword);
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users 
                   SET password = ?, 
                       password_reset_token = NULL, 
                       password_reset_expires = NULL, 
                       last_password_change = NOW() 
                   WHERE id = ?";
            
            $stmt = $this->db->executeQuery($sql, "si", [$hashedPassword, $userData['id']]);
            
            // Invalidate all active sessions
            $this->invalidateUserSessions($userData['id']);
            
            $this->db->getConnection()->commit();
            
            $this->logger->info('Password reset successful', [
                'user_id' => $userData['id']
            ]);
            
            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            $this->logger->error('Password reset failed', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Store password in history
     */
    private function storePasswordHistory($userId, $password) {
        $sql = "INSERT INTO password_history (user_id, password_hash) 
               VALUES (?, ?)";
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->db->executeQuery($sql, "is", [$userId, $hashedPassword]);
    }
    
    /**
     * Check if password was used before
     */
    public function isPasswordPreviouslyUsed($userId, $password) {
        $sql = "SELECT password_hash 
               FROM password_history 
               WHERE user_id = ? 
               ORDER BY created_at DESC 
               LIMIT 5";
        
        $stmt = $this->db->executeQuery($sql, "i", [$userId]);
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password_hash'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Invalidate all active sessions
     */
    private function invalidateUserSessions($userId) {
        $sql = "DELETE FROM session_tokens WHERE user_id = ?";
        $this->db->executeQuery($sql, "i", [$userId]);
    }
    
    /**
     * Send password reset email
     */
    private function sendResetEmail($email, $token) {
        // Implementation depends on your email service
        // This is a placeholder
        $resetLink = "https://yourapp.com/reset-password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: " . $resetLink;
        
        // Log email sending attempt
        $this->logger->info('Password reset email sent', [
            'email' => $email
        ]);
    }
}