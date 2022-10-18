<?php
$db_host = "sql8.freemysqlhosting.net";
$db_user = "sql8527596";
$db_pass = "9qWHei1rDr";
$db_name = "sql8527596";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check if connection to db is successful
if (!$conn){
    die("Connection failed" .mysqli_connect_error());
}
