<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private $db;
    
    protected function setUp(): void
    {
        // Setup test database connection
        require_once __DIR__ . '/../../controller/connect.php';
        $this->db = $conn;
    }

    public function testCreateAccount()
    {
        // Test account creation with valid data
        $data = [
            'account_name' => 'Test Account',
            'account_type' => 'savings',
            'initial_deposit' => 1000.00
        ];

        $stmt = $this->db->prepare("INSERT INTO accounts (account_name, account_type, balance) VALUES (?, ?, ?)");
        $result = $stmt->execute([$data['account_name'], $data['account_type'], $data['initial_deposit']]);
        
        $this->assertTrue($result);
        
        // Verify account was created
        $stmt = $this->db->prepare("SELECT * FROM accounts WHERE account_name = ?");
        $stmt->execute([$data['account_name']]);
        $account = $stmt->fetch();
        
        $this->assertNotNull($account);
        $this->assertEquals($data['account_type'], $account['account_type']);
        $this->assertEquals($data['initial_deposit'], $account['balance']);
    }

    public function testInvalidAccountCreation()
    {
        // Test account creation with invalid data
        $this->expectException(\PDOException::class);
        
        $data = [
            'account_name' => '', // Invalid empty name
            'account_type' => 'invalid_type',
            'initial_deposit' => -1000.00 // Invalid negative amount
        ];

        $stmt = $this->db->prepare("INSERT INTO accounts (account_name, account_type, balance) VALUES (?, ?, ?)");
        $stmt->execute([$data['account_name'], $data['account_type'], $data['initial_deposit']]);
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->db->exec("DELETE FROM accounts WHERE account_name = 'Test Account'");
    }
}