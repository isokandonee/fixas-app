<?php
$conn = mysqli_connect('localhost', 'root', '', 'user');

// Check if connection to db is successful
if (!$conn){
    die("Connection failed" .mysqli_connect_error());
}
