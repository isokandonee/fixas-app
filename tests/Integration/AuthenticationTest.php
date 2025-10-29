<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    private $db;
    
    protected function setUp(): void
    {
        session_start();
        require_once __DIR__ . '/../../controller/connect.php';
        $this->db = $conn;
        
        // Create test user
        $password = password_hash('testpass123', PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO users (first_name, last_name, email, password) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['Test', 'User', 'test@example.com', $password]);
    }
    
    public function testSuccessfulLogin()
    {
        $_POST['email'] = 'test@example.com';
        $_POST['password'] = 'testpass123';
        $_POST['token'] = bin2hex(random_bytes(32));
        $_SESSION['token'] = $_POST['token'];
        
        // Include login controller
        ob_start();
        include __DIR__ . '/../../controller/login.php';
        ob_end_clean();
        
        $this->assertTrue(isset($_SESSION['email']));
        $this->assertEquals('test@example.com', $_SESSION['email']);
    }
    
    public function testInvalidLogin()
    {
        $_POST['email'] = 'test@example.com';
        $_POST['password'] = 'wrongpassword';
        $_POST['token'] = bin2hex(random_bytes(32));
        $_SESSION['token'] = $_POST['token'];
        
        // Include login controller
        ob_start();
        include __DIR__ . '/../../controller/login.php';
        ob_end_clean();
        
        $this->assertFalse(isset($_SESSION['email']));
        $this->assertTrue(isset($_SESSION['error']));
    }
    
    public function testLogout()
    {
        $_SESSION['email'] = 'test@example.com';
        
        // Include logout controller
        include __DIR__ . '/../../controller/logout.php';
        
        $this->assertFalse(isset($_SESSION['email']));
    }
    
    protected function tearDown(): void
    {
        // Clean up test data
        $this->db->exec("DELETE FROM users WHERE email = 'test@example.com'");
        session_destroy();
    }
}