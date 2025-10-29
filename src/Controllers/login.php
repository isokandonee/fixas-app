<?php

if (isset($_POST['token'])) {
    require "connect.php";
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $password = '';

    // Check for empty fields
    if (empty($email) || empty($pass)) {
        header("Location: ../login.php?error=emptyfields&email=".$email);
        exit();
    }
    else {
        $sql="SELECT * FROM user.user_tb WHERE email='$email'";
        $result=mysqli_query($conn,$sql);
        $r = mysqli_fetch_assoc($result);
        $password = password_verify($pass, $r['password']);
        if ($password == false) {
            header("Location: ../login.php?error=invaliddetails");
            exit();
        }
        elseif ($password == true) {
            session_start();
            $_SESSION['email'] = $r['email'];
            $_SESSION['first_name'] = $r['first_name'];
            $_SESSION['last_name'] = $r['last_name'];
            $_SESSION['user_id'] = $r['id'];
            header("Location: ../dashboard/index.php?login=success");
            exit();

        }
        else {
            header("Location: ../login.php?error=loginnotsuccessful");
            exit();
        }

    }
}
else {
    header("Location: ../login.php?error=loginnotsuccessful");
    exit();
    }

