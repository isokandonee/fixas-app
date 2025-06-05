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
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
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