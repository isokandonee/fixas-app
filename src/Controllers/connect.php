<?php
require_once __DIR__ . '/../config/Environment.php';

// Load environment variables
try {
    Environment::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    die('Environment configuration not found');
}

// Load configuration
$config = require_once __DIR__ . '/../config/database.php';

// Set error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Custom logging function
 */
function logDatabaseError($message, $error_level = 3) {
    $log_file = __DIR__ . '/../logs/database_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = sprintf("[%s] [Level %d]: %s\n", $timestamp, $error_level, $message);
    error_log($log_message, 3, $log_file);
}

try {
    // Create connection with timeout
    $conn = mysqli_init();
    
    // Set connection timeout
    if (!$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, $config['connection_timeout'])) {
        throw new Exception('Setting connection timeout failed');
    }
    
    // Set SSL options if enabled
    if ($config['ssl']['verify_server_cert']) {
        if (!$conn->ssl_set(
            NULL,
            NULL,
            $config['ssl']['options']['ssl_ca'],
            NULL,
            NULL
        )) {
            throw new Exception('Setting SSL options failed');
        }
    }

    // Establish connection
    if (!$conn->real_connect(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    )) {
        throw new Exception('Connection Error: ' . mysqli_connect_error());
    }

    // Set charset
    if (!$conn->set_charset($config['charset'])) {
        throw new Exception('Error setting charset: ' . $conn->error);
    }

    // Verify SSL connection if enabled
    if ($config['ssl']['verify_server_cert']) {
        $ssl_status = $conn->query("SHOW STATUS LIKE 'Ssl_cipher'");
        if ($ssl_status->fetch_array()[1] === '') {
            throw new Exception('SSL connection not established');
        }
    }

} catch (Exception $e) {
    // Log the detailed error
    logDatabaseError($e->getMessage());
    
    // Show a user-friendly message
    die('Sorry, there was a problem connecting to the database. Please try again later.');
}
