<?php
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/PasswordReset.php';

$security = Security::getInstance();
$logger = Logger::getInstance();
$passwordReset = PasswordReset::getInstance();

try {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !$security->verifyCSRFToken($_POST['csrf_token'])) {
        throw new Exception('Invalid security token');
    }
    
    // Validate token
    $token = $security->sanitizeInput($_POST['token'] ?? '', $db->getConnection());
    if (!$token || !$passwordReset->validateToken($token)) {
        throw new Exception('invalidtoken');
    }
    
    // Validate password
    $password = $_POST['password'] ?? '';
    if (!$security->validatePassword($password)) {
        throw new Exception('weakpassword');
    }
    
    // Check if password was previously used
    $userData = $passwordReset->validateToken($token);
    if ($passwordReset->isPasswordPreviouslyUsed($userData['id'], $password)) {
        throw new Exception('previouslyused');
    }
    
    // Reset password
    if ($passwordReset->resetPassword($token, $password)) {
        $logger->info('Password reset successful', ['user_id' => $userData['id']]);
        header("Location: ../login.php?status=password_reset");
        exit();
    } else {
        throw new Exception('reset_failed');
    }
    
} catch (Exception $e) {
    $logger->error('Password reset failed', [
        'error' => $e->getMessage()
    ]);
    
    header("Location: ../new-password.php?token=" . urlencode($token) . "&error=" . $e->getMessage());
    exit();
}