<?php
    if(isset($_POST['token'])){
    session_start();
    require "connect.php";
        
        $ac_type = $_POST['account'];
        if (!empty($ac_type)) {
        $id = $_SESSION['id'];
        $insert = mysqli_query($conn,"INSERT INTO user_new.user_tb (account_type,user_id,created_at)
            values('$ac_type','$id',current_date())");

        if (!$insert) {
                echo mysqli_error($conn);
                // header("Location: ../dashboard/account.php?error=sqlerror");
                exit();
            }
        else {
                header("Location: ../dashboard/deposit.php?success=accountcreated");
                exit();
            }
        }
    }


else {
    echo mysqli_error($conn);
        // header("Location: ../dashboard/account.php?error=accountnotcreated");
        exit();
    }