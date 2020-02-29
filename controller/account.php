<?php
    if(isset($_POST['token'])){
    session_start();
    require "connect.php";
        $ac_type = $_POST['account'];
        // $ac_no = rand(5,15);
        $id = $_SESSION['id'];
        $status = 1;
        $insert = mysqli_query($conn,"INSERT INTO use.user_account (user_id,account_type_id,status_id,created_at)
            values('$id','$ac_type','$status',current_date())");

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


else {
        header("Location: ../dashboard/account.php?error=accountnotcreated");
        exit();
    }