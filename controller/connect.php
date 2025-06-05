<?php
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $db_host = getenv('DB_HOST') ?: "sql8.freemysqlhosting.net";
        $db_user = getenv('DB_USER') ?: "sql8527596";
        $db_pass = getenv('DB_PASSWORD') ?: "9qWHei1rDr";
        $db_name = getenv('DB_NAME') ?: "sql8527596";

        try {
            $this->conn = new PDO(
                "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                $db_user,
                $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}