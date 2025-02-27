<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Redirect to login page if not logged in
    header('Location: /tourny_mate/login-form.php');
    exit;
}

require_once __DIR__.'../../../utils.php';

// Example session variables for demonstration
$_SESSION['username'] = $_SESSION['username'] ?? 'John Doe';
$_SESSION['dp'] = $_SESSION['dp'] ?? null; // If null, show default image

// Set default image
$defaultImage = "https://img.freepik.com/free-icon/user_318-563642.jpg";
$userImage = $_SESSION['dp'] ?: $defaultImage;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link {
            font-size: 1rem;
            margin-right: 10px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #ffc107 !important;
        }

        .profile-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dropdown-menu {
            left: auto !important;
            right: 0 !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>dashboard/<?php echo $_SESSION['role']; ?>/dashboard.php">Battle Base</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/<?php echo $_SESSION['role']; ?>/nav/allNews.php">News/Blogs</a>
                    </li>
                </ul>
                <!-- Profile Dropdown -->
                <div class="dropdown ms-3">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($userImage); ?>" alt="User Profile"
                            class="profile-image">
                        <span class="ms-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>dashboard/<?php echo $_SESSION['role']; ?>/menu/profile/profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
