<?php
session_start();
require_once __DIR__ . '/connect.php';

class WithdrawController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function withdraw($userId, $amount) {
        try {
            if (!is_numeric($amount) || $amount <= 0) {
                throw new Exception("Invalid amount");
            }

            $this->db->beginTransaction();
            
            // Get account details
            $stmt = $this->db->prepare(
                "SELECT * FROM user_account 
                WHERE user_id = ? AND status_id = 1"
            );
            $stmt->execute([$userId]);
            $account = $stmt->fetch();
            
            if (!$account) {
                throw new Exception("Invalid account");
            }
            
            if ($account['balance'] < $amount) {
                throw new Exception("Insufficient funds");
            }
            
            // Record transaction
            $stmt = $this->db->prepare(
                "INSERT INTO transaction (transaction_type_id, source_id, destination_id, amount, created_at) 
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)"
            );
            $stmt->execute([1, $userId, $userId, $amount]);
            
            // Update balance
            $newBalance = $account['balance'] - $amount;
            $stmt = $this->db->prepare(
                "UPDATE user_account 
                SET balance = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?"
            );
            $stmt->execute([$newBalance, $userId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Withdrawal error: " . $e->getMessage());
            return false;
        }
    }
}

if (isset($_POST['token'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php?error=notloggedin");
        exit();
    }

    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    
    if (!$amount || $amount <= 0) {
        header("Location: ../dashboard/withdraw.php?error=invalidamount");
        exit();
    }
    
    $withdraw = new WithdrawController();
    if ($withdraw->withdraw($_SESSION['user_id'], $amount)) {
        header("Location: ../dashboard/index.php?success=withdrawal");
    } else {
        header("Location: ../dashboard/withdraw.php?error=failed");
    }
    exit();
}