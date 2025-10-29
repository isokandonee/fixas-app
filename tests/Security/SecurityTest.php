<?php

namespace Tests\Security;

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    private $db;
    
    protected function setUp(): void
    {
        session_start();
        require_once __DIR__ . '/../../controller/connect.php';
        $this->db = $conn;
    }
    
    public function testCSRFProtection()
    {
        // Test without CSRF token
        $_POST['email'] = 'test@example.com';
        $_POST['password'] = 'testpass123';
        
        ob_start();
        include __DIR__ . '/../../controller/login.php';
        ob_end_clean();
        
        $this->assertTrue(isset($_SESSION['error']));
        $this->assertStringContainsString('Invalid request', $_SESSION['error']);
    }
    
    public function testSQLInjectionPrevention()
    {
        $maliciousInput = "' OR '1'='1";
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$maliciousInput]);
        $result = $stmt->fetch();
        
        $this->assertFalse($result);
    }
    
    public function testPasswordHashing()
    {
        $password = 'testpass123';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Verify password hash
        $this->assertTrue(password_verify($password, $hashedPassword));
        
        // Verify different password fails
        $this->assertFalse(password_verify('wrongpass', $hashedPassword));
    }
    
    public function testXSSPrevention()
    {
        $maliciousInput = '<script>alert("XSS")</script>';
        $safeOutput = htmlspecialchars($maliciousInput, ENT_QUOTES, 'UTF-8');
        
        $this->assertStringNotContainsString('<script>', $safeOutput);
        $this->assertStringContainsString('&lt;script&gt;', $safeOutput);
    }
    
    public function testSessionSecurity()
    {
        // Test session fixation prevention
        $oldSessionId = session_id();
        $_SESSION['email'] = 'test@example.com';
        session_regenerate_id(true);
        $newSessionId = session_id();
        
        $this->assertNotEquals($oldSessionId, $newSessionId);
        $this->assertEquals('test@example.com', $_SESSION['email']);
    }
    
    protected function tearDown(): void
    {
        session_destroy();
    }
}