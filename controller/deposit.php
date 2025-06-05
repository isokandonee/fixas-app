<?php
session_start();
require_once "connect.php";

class DepositController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function deposit($userId, $accountNumber, $amount) {
        try {
            if (!is_numeric($amount) || $amount <= 0) {
                throw new Exception("Invalid amount");
            }

            $this->db->beginTransaction();
            
            // Get account details
            $stmt = $this->db->prepare(
                "SELECT * FROM user_account 
                WHERE account_number = ? AND user_id = ? AND status_id = 1"
            );
            $stmt->execute([$accountNumber, $userId]);
            $account = $stmt->fetch();
            
            if (!$account) {
                throw new Exception("Invalid account");
            }
            
            // Record transaction
            $stmt = $this->db->prepare(
                "INSERT INTO transaction (transaction_type_id, source_id, destination_id, amount, created_at) 
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)"
            );
            $stmt->execute([2, $userId, $userId, $amount]);
            
            // Update balance
            $newBalance = $account['balance'] + $amount;
            $stmt = $this->db->prepare(
                "UPDATE user_account 
                SET balance = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE account_number = ?"
            );
            $stmt->execute([$newBalance, $accountNumber]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Deposit error: " . $e->getMessage());
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
    $accountNumber = filter_input(INPUT_POST, 'ac_no', FILTER_VALIDATE_INT);
    
    if (!$amount || $amount <= 0) {
        header("Location: ../dashboard/deposit.php?error=invalidamount");
        exit();
    }
    
    $deposit = new DepositController();
    if ($deposit->deposit($_SESSION['user_id'], $accountNumber, $amount)) {
        header("Location: ../dashboard/index.php?success=deposit");
    } else {
        header("Location: ../dashboard/deposit.php?error=failed");
    }
    exit();
}