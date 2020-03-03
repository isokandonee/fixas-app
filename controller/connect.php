<?php
$conn = mysqli_connect('localhost', 'root', '', 'users');

// Check if connection to db is successful
if (!$conn){
    die("Connection failed" .mysqli_connect_error());
}
