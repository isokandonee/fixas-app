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
            $stmt = $this->db->prepare("SELECT * FROM user_tb WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['user_id'] = $user['id'];
                
                header("Location: ../dashboard/index.php?login=success");
                exit();
            } else {
                header("Location: ../login.php?error=invaliddetails");
                exit();
            }
        } catch(PDOException $e) {
            error_log($e->getMessage());
            header("Location: ../login.php?error=dberror");
            exit();
        }
    }
}

if (isset($_POST['token'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

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