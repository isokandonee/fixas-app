<?php
require_once "Security.php";
require_once "Database.php";
require_once "Logger.php";
require_once "PasswordReset.php";

$security = Security::getInstance();
$logger = Logger::getInstance();
$passwordReset = PasswordReset::getInstance();

try {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !$security->verifyCSRFToken($_POST['csrf_token'])) {
        throw new Exception('Invalid security token');
    }
    
    // Validate email
    $email = $security->sanitizeInput($_POST['email'] ?? '', $db->getConnection());
    if (!$security->validateEmail($email)) {
        throw new Exception('invalid_email');
    }
    
    // Request password reset
    if ($passwordReset->requestReset($email)) {
        $logger->info('Password reset requested', ['email' => $email]);
        header("Location: ../reset-password.php?status=email_sent");
        exit();
    } else {
        throw new Exception('user_not_found');
    }
    
} catch (Exception $e) {
    $logger->error('Password reset request failed', [
        'error' => $e->getMessage(),
        'email' => $email ?? null
    ]);
    
    header("Location: ../reset-password.php?error=" . $e->getMessage());
    exit();
}