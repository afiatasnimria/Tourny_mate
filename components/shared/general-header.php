<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Battle Base</title>
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/custom.css">
  <style>
    /* Header Styling */
    .navbar {
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
    }

    .navbar-nav .nav-link {
      font-size: 1rem;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      color: #f8c146;
    }

    .btn {
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover {
      transform: scale(1.05);
    }

    /* Loading Animation */
/* Loading Animation */
#preloader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  background: transparent; /* Ensure background is transparent */
  mix-blend-mode: color-burn;
}

#preloader .spinner-border {
  width: 10rem;
  height: 10rem;
  color: #f8c146;
}

/* Fade-out Animation */
.loaded #preloader {
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.5s ease, visibility 0.5s ease;
}

  </style>
</head>

<body>
  <!-- Preloader -->
  <div id="preloader">
      <img class="preloader" src="images/loading-img.gif" alt="">
  </div>

  <!-- Header -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <!-- Brand -->
      <a href="index.php" class="navbar-brand">BATTLE BASE</a>

      <!-- Toggle Button for Mobile -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Content -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Centered Navigation Links -->
        <ul class="navbar-nav mx-auto d-flex align-items-center">
          <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
          <li class="nav-item"><a href="allScores.php" class="nav-link">Scores</a></li>
          <li class="nav-item"><a href="allTournaments.php" class="nav-link">Tournament</a></li>
          <li class="nav-item"><a href="news.php" class="nav-link">News</a></li>
          <li class="nav-item"><a href="highlights.php" class="nav-link">Highlights</a></li>
          <li class="nav-item"><a href="players.php" class="nav-link">Players</a></li>
        </ul>

        <!-- Right-Aligned Buttons -->
        <div class="d-flex align-items-center">
          <?php if ($username): ?>
            <a href="dashboard/<?= $role; ?>/dashboard.php" class="btn btn-sm btn-outline-light mx-2">
              <i class="fas fa-user-circle"></i> Welcome, <?= $username; ?>
            </a>
            <a href="logout.php" class="btn btn-sm btn-light">Logout</a>
          <?php else: ?>
            <a href="login-form.php" class="btn btn-sm btn-light mx-2">Login</a>
            <a href="register-form.php" class="btn btn-sm btn-outline-light">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- End of Header -->