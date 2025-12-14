<?php
$host = "localhost";
$dbname = "attendance_db";
$username = "root";
$password = "";

$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname",
    $username,
    $password
);
?>
