<?php
// Database connection
require_once 'config.php';

// Check connection
if ($conn === null) {
    die("Connection failed: Unable to connect to database");
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        // Redirect back to contact page with an error message
        header('Location: contact.php?error=Please fill in all fields');
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: contact.php?error=Invalid email format');
        exit();
    }

    try {
        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO contact (full_name, email, message, created_at) VALUES (:name, :email, :message, NOW())";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            // Log detailed prepare error
            $errorInfo = $conn->errorInfo();
            error_log("Prepare statement failed: " . print_r($errorInfo, true));
            throw new PDOException("Failed to prepare statement: " . $errorInfo[2]);
        }

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        
        // Execute the statement
        $executeResult = $stmt->execute();
        
        if ($executeResult) {
            // Successful submission
            header('Location: contact.php?success=Your message has been sent successfully');
        } else {
            // Log execute error
            $errorInfo = $stmt->errorInfo();
            error_log("Execute failed: " . print_r($errorInfo, true));
            
            // Database error
            header('Location: contact.php?error=Unable to send message. Please try again.');
        }
    } catch (PDOException $e) {
        // Log the error with full details
        error_log("Contact form submission error: " . $e->getMessage());
        error_log("Full exception: " . print_r($e, true));
        
        // Detailed error for debugging
        die("Database error: " . $e->getMessage());
    }

    exit();
} else {
    // If someone tries to access this file directly without POST
    header('Location: contact.php');
    exit();
}
?>