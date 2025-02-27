<?php
session_start();
require_once 'config.php';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $admin_username = trim($_POST['username'] ?? '');
    $admin_password = $_POST['password'] ?? '';

    if (empty($admin_username) || empty($admin_password)) {
        $error_message = "Both fields are required.";
    } else {
        try {
            // Prepare SQL to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username");
            $stmt->bindParam(':username', $admin_username, PDO::PARAM_STR);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password 
            if ($admin && $admin_password === $admin['pass_key']) {
                // Successful login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin_username;
                $_SESSION['admin_id'] = $admin['id'];

                // Regenerate session ID for security
                session_regenerate_id(true);

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            // Log error (in production, log to file instead of displaying)
            $error_message = "Database error. Please try again later.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h3 class="text-center">Admin Login</h3>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <div class="alert alert-danger" id="error-message"></div>
            <form action="" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();
            var errorMessage = document.getElementById('error-message');

            if (username === "" || password === "") {
                errorMessage.innerText = "Both fields are required.";
                errorMessage.style.display = "block";
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
