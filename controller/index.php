<?php

if (isset($_POST['token'])) {
        require "connect.php";
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $phone = $_POST['phone'];

        // Check for empty fields
        if ( empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($cpassword) ) {
            header("Location: ../index.php?error=emptyfields&firstname=".$firstname."lastname=".$lastname."mail=".$email);
            exit();
        }

        // Email validation
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../index.php?error=invalidmail&firstname=".$firstname."lastname=".$lastname);
            exit();
        }
        
        // First and Lastname validation
        elseif (!preg_match('/^[a-zA-Z]*$/', $firstname) && !preg_match('/^[a-zA-Z]*$/',$lastname)) {
            header("Location: ../index.php?error=incorrectdetails&mail=".$email);
            exit();
        }

        // password validation
        elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
            header("Location: ../index.php?error=weakpassword&firstname=".$firstname."lastname=".$lastname."mail=".$email);
            exit();
        }

        // Check if password and confirm password match
        elseif ($cpassword !== $password) {
            header("Location: ../index.php?error=passwordsdonotmatch&firstname=".$firstname."lastname=".$lastname."mail=".$email);
            exit();
        }
                else {
                    //inserting our values into db
                    $hpassword = password_hash($password, PASSWORD_DEFAULT);
                    $insert = mysqli_query($conn, "INSERT INTO user.user_tb (first_name,last_name,email,password,phone,created_at) 
                    VALUES ('$firstname','$lastname','$email','$hpassword','$phone',current_date())");
                    if ($insert) {
                        header("Location: ../login.php?signup=success");
                        exit();
                    }
                    else {
                        //Check for duplicate email
                        $sql="SELECT * FROM user.user_tb WHERE email='$email'";
                        $result=mysqli_query($conn,$sql);
                        $r = mysqli_fetch_assoc($result);
                        $em = $r['email'];
                        while ($em == $email) {
                        header("Location: ../index.php?error=emailaltaken&firstname=".$firstname."lastname=".$lastname);
                        exit();
                        }
                    }
                }
        mysqli_close($conn);

} else {
    header("Location: ../index.php?error=notsuccessful");
    exit();
    }

