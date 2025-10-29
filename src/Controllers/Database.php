<?php

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        require_once 'connect.php';
        global $conn;
        $this->connection = $conn;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Prepare and execute a query with parameters
     */
    public function executeQuery($sql, $types, $params) {
        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Failed to prepare statement: ' . $this->connection->error);
        }
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute statement: ' . $stmt->error);
        }
        
        return $stmt;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        try {
            $stmt = $this->executeQuery(
                "SELECT email FROM user_tb WHERE email = ?",
                "s",
                [$email]
            );
            
            $result = $stmt->get_result();
            return $result->num_rows > 0;
            
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new user with prepared statement
     */
    public function createUser($firstname, $lastname, $email, $password, $phone) {
        try {
            $hpassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->executeQuery(
                "INSERT INTO user_tb (first_name, last_name, email, password, phone, created_at) VALUES (?, ?, ?, ?, ?, current_date())",
                "sssss",
                [$firstname, $lastname, $email, $hpassword, $phone]
            );
            
            return $stmt->affected_rows > 0;
            
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
}