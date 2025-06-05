<?php
session_start();
require_once "connect.php";

class LoginController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function login($email, $password) {
        try {
            // Input validation
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            $stmt = $this->db->prepare("SELECT * FROM user_tb WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session with limited data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = htmlspecialchars($user['first_name']);
                $_SESSION['last_name'] = htmlspecialchars($user['last_name']);
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                header("Location: ../dashboard/index.php?login=success");
                exit();
            } else {
                throw new Exception("Invalid credentials");
            }
        } catch(Exception $e) {
            error_log("Login error: " . $e->getMessage());
            header("Location: ../login.php?error=invaliddetails");
            exit();
        }
    }
}

if (isset($_POST['token'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=emptyfields");
        exit();
    }

    $login = new LoginController();
    $login->login($email, $password);
} else {
    header("Location: ../login.php?error=invalidaccess");
    exit();
}