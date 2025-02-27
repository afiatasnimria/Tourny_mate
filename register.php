<?php
// Start the session to store error messages
session_start();

// Include the database connection file
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $user = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $fullName = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $role = htmlspecialchars(trim($_POST['role'] ?? 'user'));

    // Array to store validation errors
    $errors = [];

    // Validate inputs
    if (!$user) $errors[] = "Username is required.";
    if (!$email) $errors[] = "A valid email address is required.";
    if (!$password) $errors[] = "Password is required.";
    if (!$fullName) $errors[] = "Full name is required.";
    if (!$phone) $errors[] = "Phone number is required.";

    if (!empty($errors)) {
        // Store errors in session and redirect back to the form
        $_SESSION['errors'] = $errors;
        header("Location: register-form.php");
        exit();
    }

    try {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the insert statement
        $sql = "INSERT INTO userinfo (username, email, password_key, fullName, phone, role) 
                VALUES (:username, :email, :password_key, :fullName, :phone, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $user,
            ':email' => $email,
            ':password_key' => $hashedPassword,
            ':fullName' => $fullName,
            ':phone' => $phone,
            ':role' => $role
        ]);

        // Redirect to the login page upon successful registration
        $_SESSION['success'] = "You have successfully registered. Please login.";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        // Handle duplicate entry error (MySQL error code 1062)
        if ($e->getCode() == 23000) {
            $errors[] = "The username or email already exists. Please choose a different one.";
        } else {
            $errors[] = "An unexpected error occurred: " . $e->getMessage();
        }

        // Store errors in session and redirect back to the register form
        $_SESSION['errors'] = $errors;
        header("Location: register-form.php");
        exit();
    }
}
?>
