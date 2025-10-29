<?php

namespace App\Controllers;

use App\Models\Database;
use PDO;

abstract class BaseController {
    protected PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function view(string $template, array $data = []): void {
        extract($data);
        require dirname(__DIR__) . "/Views/{$template}.php";
    }
    
    protected function redirect(string $path): void {
        header("Location: {$path}");
        exit;
    }
    
    protected function json(array $data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
    
    protected function validateCSRF(): void {
        if (!isset($_POST['token']) || !isset($_SESSION['token']) || 
            !hash_equals($_SESSION['token'], $_POST['token'])) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }
    
    protected function generateToken(): string {
        $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = $token;
        return $token;
    }
}