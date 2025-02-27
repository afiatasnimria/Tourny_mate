<?php
// db_connection.php

// Database connection settings
$host = "localhost";
$dbname = "tourny_mate";
$username = "root";
$password = "";

// Optional: Character set for better international support
$charset = "utf8mb4";

try {
    // Create a database connection using PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $conn = new PDO($dsn, $username, $password);

    // Set PDO attributes for better error handling and performance
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use native prepared statements

} catch (PDOException $e) {
    // Log the error message (optional: only in production environments)
    error_log("Database connection error: " . $e->getMessage(), 0);

    // Display a generic error message to the user
    die("Database connection failed. Please try again later.");
}
?>
