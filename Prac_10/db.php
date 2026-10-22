<?php
/**
 * Practical 10 - Database Connection
 * Connects to the local MySQL database 'studenthub' using MySQLi.
 */

$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $database);

// Verify connection
if ($conn->connect_error) {
    error_log("Practical 10 DB Connection Failed: " . $conn->connect_error);
    die("Database connection failed. Please ensure MySQL is running in XAMPP.");
}

// Ensure UTF-8 character encoding
$conn->set_charset("utf8mb4");
?>
