<?php
// config/db.php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "fyp_nisaa_db";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set charset for proper text handling (Malay names etc.)
$conn->set_charset("utf8mb4");
?>