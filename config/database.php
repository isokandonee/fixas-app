<?php
return [
    'host' => getenv('DB_HOST'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'database' => getenv('DB_NAME'),
    'charset' => getenv('DB_CHARSET'),
    'connection_timeout' => getenv('DB_TIMEOUT'),
    'ssl' => [
        'verify_server_cert' => getenv('DB_SSL_VERIFY'),
        'options' => [
            'ssl_verify_server_cert' => true,
            'ssl_ca' => __DIR__ . '/../certificates/ca-cert.pem'
        ]
    ]
];
?>