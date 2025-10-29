<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    private $db;
    private $testAccountId;
    
    protected function setUp(): void
    {
        // Setup test database connection
        require_once __DIR__ . '/../../controller/connect.php';
        $this->db = $conn;
        
        // Create test account
        $stmt = $this->db->prepare("INSERT INTO accounts (account_name, account_type, balance) VALUES (?, ?, ?)");
        $stmt->execute(['Test Account', 'savings', 1000.00]);
        $this->testAccountId = $this->db->lastInsertId();
    }
    
    public function testDeposit()
    {
        $amount = 500.00;
        $initialBalance = 1000.00;
        
        // Perform deposit
        $stmt = $this->db->prepare("
            INSERT INTO transactions (account_id, type, amount) 
            VALUES (?, 'deposit', ?)
        ");
        $result = $stmt->execute([$this->testAccountId, $amount]);
        
        $this->assertTrue($result);
        
        // Update account balance
        $stmt = $this->db->prepare("
            UPDATE accounts 
            SET balance = balance + ? 
            WHERE id = ?
        ");
        $stmt->execute([$amount, $this->testAccountId]);
        
        // Verify new balance
        $stmt = $this->db->prepare("SELECT balance FROM accounts WHERE id = ?");
        $stmt->execute([$this->testAccountId]);
        $newBalance = $stmt->fetchColumn();
        
        $this->assertEquals($initialBalance + $amount, $newBalance);
    }
    
    public function testWithdraw()
    {
        $amount = 300.00;
        $initialBalance = 1000.00;
        
        // Perform withdrawal
        $stmt = $this->db->prepare("
            INSERT INTO transactions (account_id, type, amount) 
            VALUES (?, 'withdrawal', ?)
        ");
        $result = $stmt->execute([$this->testAccountId, $amount]);
        
        $this->assertTrue($result);
        
        // Update account balance
        $stmt = $this->db->prepare("
            UPDATE accounts 
            SET balance = balance - ? 
            WHERE id = ? AND balance >= ?
        ");
        $stmt->execute([$amount, $this->testAccountId, $amount]);
        
        // Verify new balance
        $stmt = $this->db->prepare("SELECT balance FROM accounts WHERE id = ?");
        $stmt->execute([$this->testAccountId]);
        $newBalance = $stmt->fetchColumn();
        
        $this->assertEquals($initialBalance - $amount, $newBalance);
    }
    
    public function testInsufficientFundsWithdrawal()
    {
        $amount = 2000.00; // More than available balance
        
        // Attempt withdrawal
        $stmt = $this->db->prepare("
            SELECT balance 
            FROM accounts 
            WHERE id = ? AND balance >= ?
        ");
        $stmt->execute([$this->testAccountId, $amount]);
        $sufficient = $stmt->fetch();
        
        $this->assertFalse((bool)$sufficient);
    }
    
    protected function tearDown(): void
    {
        // Clean up test data
        $this->db->exec("DELETE FROM transactions WHERE account_id = {$this->testAccountId}");
        $this->db->exec("DELETE FROM accounts WHERE id = {$this->testAccountId}");
    }
}