<?php

namespace App\Controllers;

class AccountController extends BaseController {
    public function index() {
        $accounts = $this->db->query("SELECT * FROM accounts")->fetchAll();
        $this->view('accounts/index', ['accounts' => $accounts]);
    }
    
    public function create() {
        $this->validateCSRF();
        
        $accountName = filter_input(INPUT_POST, 'account_name', FILTER_SANITIZE_STRING);
        $accountType = filter_input(INPUT_POST, 'account_type', FILTER_SANITIZE_STRING);
        $initialDeposit = filter_input(INPUT_POST, 'initial_deposit', FILTER_VALIDATE_FLOAT);
        
        if (!$accountName || !$accountType || $initialDeposit === false) {
            $this->json(['error' => 'Invalid input'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO accounts (account_name, account_type, balance) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$accountName, $accountType, $initialDeposit]);
            
            $this->json([
                'message' => 'Account created successfully',
                'id' => $this->db->lastInsertId()
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $this->json(['error' => 'Failed to create account'], 500);
        }
    }
}