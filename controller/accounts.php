<?php
session_start();
require "connect.php";
$id = $_SESSION['id'];
$ac = $_POST['account'];
$am = $_POST['amount'];

$fetch = mysqli_query($conn,"SELECT id from user_tb where id = '$id'");
$r = mysqli_fetch_array($fetch);
// $id = $r['id'];
$ttid = 2;
$fetcha = mysqli_query($conn,"SELECT * from user_account where id = '$id'");
$m = mysqli_fetch_array($fetcha);
$actype =$m['account_type_id'];
$st = 1;
        $insert = mysqli_query($conn,"insert into transaction (transaction_type_id,source_id,destination_id,amount,created_at)
        values('$ttid','$id','$id','$am',current_date())");
        $inserts = mysqli_query($conn,"insert into user_account (user_id,account_type_id,status_id,created_at)
        values('$id','$ac','$st',current_date())");
        
if (!$insert) {
    # code...
    echo "<script>alert('Account creation not successful!')</script>";
    echo mysqli_error($conn);
    // echo "$id";

    // header('location:account.php');
}
if (!$inserts) {
    # code...
    echo "<script>alert('Account creation not successful!')</script>";
    echo mysqli_error($conn);
    // echo "$id";

    // header('location:account.php');
}
else {
        header('location:../pages/dashboard.php');
        echo "<script>alert('Account creation successful!')</script>";
}


?>