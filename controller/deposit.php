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
            $this->db->beginTransaction();
            
            // Get account details
            $stmt = $this->db->prepare("SELECT * FROM user_account WHERE account_number = ? AND user_id = ?");
            $stmt->execute([$accountNumber, $userId]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$account) {
                throw new Exception("Invalid account");
            }
            
            // Record transaction
            $stmt = $this->db->prepare(
                "INSERT INTO transaction (transaction_type_id, source_id, destination_id, amount, created_at) 
                VALUES (?, ?, ?, ?, CURRENT_DATE())"
            );
            $stmt->execute([2, $userId, $userId, $amount]);
            
            // Update balance
            $newBalance = $account['balance'] + $amount;
            $stmt = $this->db->prepare(
                "UPDATE user_account SET balance = ?, updated_at = CURRENT_DATE() 
                WHERE account_number = ?"
            );
            $stmt->execute([$newBalance, $accountNumber]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}

if (isset($_POST['token'])) {
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