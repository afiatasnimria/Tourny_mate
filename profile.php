<?php
include './components/shared/manager-header.php'; // Include header component
?>

<!-- Profile Section -->
<div class="container my-5">
    <div class="profile-header">
        <div class="profile-picture">
            <img src="https://img.freepik.com/free-icon/user_318-875902.jpg" alt="User Profile Picture">
        </div>
        <h1>
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </h1>
    </div>

    <!-- Stats Section -->
    <div class="row text-center">
        <div class="col-md-3">
            <div class="stat-box">
                <p><a href="tournament.php">Tournament</a></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p>Matches</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p><a href="team.php">Teams</a></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p>Sport</p>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="mt-5">
        <h3>Profile Information</h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></li>
            <li class="list-group-item"><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['phone']); ?></li>
        </ul>
    </div>
</div>

<?php
include './components/shared/manager-footer.php'; // Include footer component
?>
