<?php

return [
    'app' => [
        'name' => 'Fixas-Bank',
        'version' => '1.0.0',
        'url' => 'http://localhost/fixas-app',
        'debug' => false
    ],
    
    'security' => [
        'password_min_length' => 8,
        'session_lifetime' => 1800,
        'max_login_attempts' => 5,
        'lockout_time' => 900,
        'token_lifetime' => 3600,
        'allowed_hosts' => ['localhost', 'fixas-bank.com'],
        'cors_origins' => ['http://localhost', 'https://fixas-bank.com']
    ],
    
    'database' => [
        'max_connections' => 100,
        'timeout' => 30,
        'ssl_verify' => true
    ],
    
    'mail' => [
        'from_address' => 'noreply@fixas-bank.com',
        'from_name' => 'Fixas Bank',
        'smtp_host' => 'smtp.mailtrap.io',
        'smtp_port' => 587,
        'smtp_encryption' => 'tls'
    ],
    
    'logging' => [
        'path' => __DIR__ . '/../logs',
        'max_files' => 30,
        'level' => 'warning'
    ],
    
    'features' => [
        'registration' => true,
        'password_reset' => true,
        'two_factor_auth' => false,
        'api_access' => false
    ]
];