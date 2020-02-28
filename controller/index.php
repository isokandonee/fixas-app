<?php

require "connect.php";
if (isset($_POST['firstname'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $phone = $_POST['phone'];
        // $date =  date('j F Y h:i:s');

        // Check for empty fields
        if ( empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($cpassword) ) {
            header("Location: ../index.php?error=incorrectdetails&firstname=".$firstname."lastname=".$lastname."mail=".$email);
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

        // Phone number validation
        // elseif (!preg_match('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/', $phone)) {
        //     header("Location: ../index.php?error=incorrectphonenumber(+234)&firstname=".$firstname."lastname=".$lastname."mail=".$email);
        //     exit();
        // }

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
                    $hpassword = password_hash($password, PASSWORD_DEFAULT);
                    $insert = mysqli_query($conn, "INSERT INTO user.user_tb (first_name,last_name,email,password,phone,created_at) 
                    VALUES ($firstname,$lastname,$email,$hpassword,$phone,current_date())");
                    if ($insert) {
                        session_start();
                        header("Location: ../dashboard/index.php?signup=success");
                        exit();
                    }
                    else {
                        echo mysqli_error($conn);
                        // header("Location: ../index.php?signup=notsuccessful");
                        // exit();
                    }
                }
        mysqli_close($conn);

} else {
    header("Location: ../index.php");
    exit();
    }

