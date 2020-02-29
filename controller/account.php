<?php
    if(isset($_POST['token'])){
    session_start();
    require "connect.php";
    $ac_type = $_POST['account'];
    if (empty($ac_type)) {
        header("Location: ../dashboard/account.php?error=emptyfields&accounttype=".$_POST['account']);
        exit();
    }
    else{
        $id = $_SESSION['id'];
        $status = 1;
        $insert = mysqli_query($conn,"INSERT INTO user.user_account (user_id,account_type_id,status_id,created_at)
            values('$id','$ac_type','$status',current_date())");

        if (!$insert) {
                header("Location: ../dashboard/account.php?error=sqlerror");
                exit();
            }
        else {
                header("Location: ../dashboard/deposit.php?success=accountcreated");
                exit();
            }
    }

}
else {
        header("Location: ../dashboard/account.php?error=accountnotcreated");
        exit();
    }