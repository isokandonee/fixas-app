<?php 
require 'connect.php';
session_start(); 
session_unset();
session_destroy();
header("Location: /fixas-bank/login.php");
?>