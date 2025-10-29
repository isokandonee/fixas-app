<?php

class TransactionApproval {
    private static $instance = null;
    private $db;
    private $logger;
    
    private const APPROVAL_THRESHOLD = 1000000; // Transactions above 1M require approval
    
    private function __construct() {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Check if transaction needs approval
     */
    public function requiresApproval($amount, $transactionType) {
        // Transactions above threshold need approval
        if ($amount >= self::APPROVAL_THRESHOLD) {
            return true;
        }
        
        // Certain transaction types always need approval
        $highRiskTypes = ['international', 'business', 'large_withdrawal'];
        return in_array($transactionType, $highRiskTypes);
    }
    
    /**
     * Create transaction pending approval
     */
    public function createPendingTransaction($data) {
        try {
            $this->db->getConnection()->begin_transaction();
            
            // Insert transaction
            $sql = "INSERT INTO transactions (
                       reference, transaction_type_id, source_account_id, 
                       destination_account_id, amount, currency,
                       status, description, metadata,
                       created_by
                   ) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?)";
            
            $reference = $this->generateReference();
            $metadata = json_encode([
                'requires_approval' => true,
                'approval_reason' => $this->getApprovalReason($data['amount'], $data['type']),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ]);
            
            $stmt = $this->db->executeQuery($sql, "siiiisssi", [
                $reference,
                $data['type_id'],
                $data['source_id'],
                $data['destination_id'],
                $data['amount'],
                $data['currency'],
                $data['description'],
                $metadata,
                $data['user_id']
            ]);
            
            // Log the pending transaction
            $this->logger->info('Transaction pending approval', [
                'reference' => $reference,
                'amount' => $data['amount'],
                'user_id' => $data['user_id']
            ]);
            
            $this->db->getConnection()->commit();
            return $reference;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            $this->logger->error('Failed to create pending transaction', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return false;
        }
    }
    
    /**
     * Approve transaction
     */
    public function approveTransaction($reference, $approverId) {
        try {
            $this->db->getConnection()->begin_transaction();
            
            // Get transaction details
            $transaction = $this->getTransaction($reference);
            if (!$transaction || $transaction['status'] !== 'pending') {
                throw new Exception('Invalid transaction');
            }
            
            // Update transaction status
            $sql = "UPDATE transactions 
                   SET status = 'completed',
                       approved_by = ?,
                       completed_at = CURRENT_TIMESTAMP
                   WHERE reference = ?";
            
            $this->db->executeQuery($sql, "is", [$approverId, $reference]);
            
            // Process the actual transfer
            $this->processTransfer($transaction);
            
            // Log approval
            $this->logger->info('Transaction approved', [
                'reference' => $reference,
                'approver_id' => $approverId
            ]);
            
            $this->db->getConnection()->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            $this->logger->error('Transaction approval failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Reject transaction
     */
    public function rejectTransaction($reference, $approverId, $reason) {
        try {
            $sql = "UPDATE transactions 
                   SET status = 'failed',
                       metadata = JSON_SET(metadata, '$.rejection_reason', ?, '$.rejected_by', ?),
                       completed_at = CURRENT_TIMESTAMP
                   WHERE reference = ?";
            
            $this->db->executeQuery($sql, "sis", [$reason, $approverId, $reference]);
            
            $this->logger->info('Transaction rejected', [
                'reference' => $reference,
                'approver_id' => $approverId,
                'reason' => $reason
            ]);
            
            return true;
        } catch (Exception $e) {
            $this->logger->error('Transaction rejection failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get pending transactions
     */
    public function getPendingTransactions() {
        try {
            $sql = "SELECT t.*, 
                          ua_source.account_number as source_account,
                          ua_dest.account_number as destination_account,
                          u.first_name, u.last_name
                   FROM transactions t
                   JOIN user_accounts ua_source ON t.source_account_id = ua_source.id
                   JOIN user_accounts ua_dest ON t.destination_account_id = ua_dest.id
                   JOIN users u ON t.created_by = u.id
                   WHERE t.status = 'pending'
                   ORDER BY t.created_at DESC";
            
            $stmt = $this->db->executeQuery($sql, "", []);
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            $this->logger->error('Failed to fetch pending transactions', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    private function generateReference() {
        return 'TXN' . date('Ymd') . strtoupper(uniqid());
    }
    
    private function getApprovalReason($amount, $type) {
        if ($amount >= self::APPROVAL_THRESHOLD) {
            return 'Amount exceeds automatic approval threshold';
        }
        return 'Transaction type requires manual approval';
    }
    
    private function getTransaction($reference) {
        $sql = "SELECT * FROM transactions WHERE reference = ?";
        $stmt = $this->db->executeQuery($sql, "s", [$reference]);
        return $stmt->get_result()->fetch_assoc();
    }
    
    private function processTransfer($transaction) {
        // Deduct from source account
        $sql = "UPDATE user_accounts 
               SET balance = balance - ?,
                   last_transaction_date = CURRENT_TIMESTAMP
               WHERE id = ?";
        
        $this->db->executeQuery($sql, "di", [
            $transaction['amount'],
            $transaction['source_account_id']
        ]);
        
        // Add to destination account
        $sql = "UPDATE user_accounts 
               SET balance = balance + ?,
                   last_transaction_date = CURRENT_TIMESTAMP
               WHERE id = ?";
        
        $this->db->executeQuery($sql, "di", [
            $transaction['amount'],
            $transaction['destination_account_id']
        ]);
    }
}