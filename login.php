<?php
// Start the session to store error messages
session_start();

// Include the database connection file
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Array to store validation errors
    $errors = [];

    // Validate inputs
    if (!$email) {
        $errors[] = "A valid email address is required.";
    }
    if (!$password) {
        $errors[] = "Password is required.";
    }

    // If validation errors exist, store them in the session and redirect back
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: login-form.php");
        exit();
    }

    try {
        // Prepare and execute the SQL statement to fetch user details
        $sql = "SELECT * FROM userinfo WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password_key'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullName'] = $user['fullName'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                $_SESSION['phone'] = $user['phone'];

                // Regenerate session ID for security
                session_regenerate_id(true);

                // Redirect based on the user role
                switch ($_SESSION['role']) {
                    case 'admin':
                        header("Location: ./dashboard/admin/dashboard.php");
                        break;
                    default:
                        header("Location: ./dashboard/user/dashboard.php");
                        break;
                }
                exit();
            } else {
                // Password doesn't match
                $_SESSION['errors'] = ["Invalid email or password."];
                header("Location: login-form.php");
                exit();
            }
        } else {
            // User not found
            $_SESSION['errors'] = ["Invalid email or password."];
            header("Location: login-form.php");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['errors'] = ["An error occurred. Please try again later."];
        error_log("Database error: " . $e->getMessage());
        header("Location: login-form.php");
        exit();
    }
} else {
    // If not a POST request, redirect to login form
    header("Location: login-form.php");
    exit();
}
