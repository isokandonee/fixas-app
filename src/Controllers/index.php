<?php
session_start();

require_once "Security.php";
require_once "Database.php";
require_once "RateLimiter.php";
require_once "IPTracker.php";

try {
    $security = Security::getInstance();
    $db = Database::getInstance();
    $rateLimiter = RateLimiter::getInstance();
    $ipTracker = IPTracker::getInstance();
    
    // Initialize tables if they don't exist
    $rateLimiter->initializeTable();
    $ipTracker->initializeTable();
    
    // Get client IP
    $clientIP = $_SERVER['REMOTE_ADDR'];
    
    // Validate IP
    if (!$ipTracker->validateIP($clientIP)) {
        throw new Exception('blocked_ip');
    }
    
    // Check rate limit
    if (!$rateLimiter->checkRequest($clientIP, 'register')) {
        throw new Exception('too_many_attempts');
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !$security->verifyCSRFToken($_POST['csrf_token'])) {
        throw new Exception('Invalid security token');
    }
    
    // Sanitize all inputs
    $input = $security->sanitizeInput($_POST, $db->getConnection());
    
    $firstname = $input['firstname'] ?? '';
    $lastname = $input['lastname'] ?? '';
    $email = $input['email'] ?? '';
    $password = $_POST['password'] ?? ''; // Don't sanitize password
    $cpassword = $_POST['cpassword'] ?? ''; // Don't sanitize password
    $phone = $input['phone'] ?? '';
    
    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($cpassword)) {
        throw new Exception('emptyfields');
    }
    
    // Validate email
    if (!$security->validateEmail($email)) {
        throw new Exception('invalidmail');
    }
    
    // Validate names (allow letters, spaces, and hyphens)
    if (!preg_match('/^[a-zA-Z\s-]+$/', $firstname) || !preg_match('/^[a-zA-Z\s-]+$/', $lastname)) {
        throw new Exception('incorrectdetails');
    }
    
    // Validate password strength
    if (!$security->validatePassword($password)) {
        throw new Exception('weakpassword');
    }
    
    // Validate password match
    if ($password !== $cpassword) {
        throw new Exception('passwordsdonotmatch');
    }
    
    // Check if email exists
    if ($db->emailExists($email)) {
        throw new Exception('emailtaken');
    }
    
    // Create user
    if ($db->createUser($firstname, $lastname, $email, $password, $phone)) {
        // Reset rate limiting and tracking for successful registration
        $rateLimiter->resetAttempts($clientIP, 'register');
        
        // Start session and set user data
        $_SESSION['registration_success'] = true;
        header("Location: ../login.php?signup=success");
        exit();
    } else {
        throw new Exception('dberror');
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    
    // Log technical errors but don't show them to user
    if ($error === 'dberror') {
        error_log("Database error during registration: " . $e->getMessage());
        $error = 'notsuccessful';
    }
    
    // Redirect with error
    header("Location: ../index.php?error=" . $error);
    exit();
}

