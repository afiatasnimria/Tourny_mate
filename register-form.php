<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background: url('images/sports-background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #000000; /* Black text */
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: rgba(0, 51, 102, 0.9); /* Dark blue background for navbar */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand, .nav-link {
            color: #ffffff; /* White text for navbar links */
            font-weight: 500;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #007bff; /* Blue text on hover */
        }
        .btn-primary {
            background-color: #007bff; /* Blue background for buttons */
            border-color: #007bff; /* Blue border for buttons */
            color: #ffffff; /* White text for buttons */
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue background on hover */
            border-color: #0056b3; /* Darker blue border on hover */
        }
        .bg-primary {
            background-color: rgba(0, 51, 102, 0.9) !important; /* Dark blue background for primary sections */
        }
        .text-white {
            color: #ffffff !important; /* White text */
        }
        .alert-success {
            background-color: rgba(0, 128, 0, 0.7); /* Green background for success alerts */
            border-color: rgba(0, 128, 0, 0.7); /* Green border for success alerts */
            color: #ffffff; /* White text for success alerts */
        }
        .alert-danger {
            background-color: rgba(255, 0, 0, 0.7); /* Red background for error alerts */
            border-color: rgba(255, 0, 0, 0.7); /* Red border for error alerts */
            color: #ffffff; /* White text for error alerts */
        }
        .container {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .form-control {
            border-color: #ced4da;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }
        .input-group-text:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .form-section {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background for form section */
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Back</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login-form.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-primary text-white rounded-start">
                <!-- Logo Section -->
                <div class="text-center">
                    <a href="index.php">
                        <img src="images/logo.jpg" alt="Logo" class="img-fluid mb-4" style="max-width: 200px;">
                    </a>
                    <h2>Welcome to Our Platform!</h2>
                    <p>Join us today and explore powerful tools for managers and players.</p>
                </div>
            </div>
            <div class="col-lg-6 form-section">
                <!-- Register Form Section -->
                <h2 class="mb-4 text-center">Create an Account</h2>
                <p class="text-center">Join the platform and get started now.</p>

                <!-- Custom Alerts -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" id="fullname" class="form-control" name="fullname" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" class="form-control" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" class="form-control" name="phone" placeholder="Enter your phone number" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login-form.php" class="text-primary text-decoration-none">Login here</a>.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Password Toggle Script -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>
</html>
