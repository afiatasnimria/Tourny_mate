<?php
require_once __DIR__ . '/../../utils.php'; // Fixed path


// Determine the current page for active state
$currentPage = basename($_SERVER['PHP_SELF']); // Get the current file name
?>

<!-- Sidebar Trigger for Mobile -->
<button class="btn btn-outline-secondary d-lg-none mb-3" type="button" data-bs-toggle="offcanvas"
    data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
    Menu
</button>

<!-- Sidebar Menu for Large Screens -->
<div class="d-none d-lg-block bg-light border-end vh-100 p-3">
    <h4 class="fw-bold">Dashboard Menu</h4>
    <ul class="nav nav-pills flex-column gap-2">
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>dashboard/<?php echo $_SESSION['role']; ?>/dashboard.php" 
               class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Statistics</a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>dashboard/user/menu/team/my_team.php" 
               class="nav-link <?php echo $currentPage === 'my_team.php' ? 'active' : ''; ?>">My Team</a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>dashboard/user/menu/tournament/tournament.php" 
               class="nav-link <?php echo $currentPage === 'tournament.php' ? 'active' : ''; ?>">Tournaments</a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>dashboard/user/menu/news/news.php"
               class="nav-link <?php echo $currentPage === 'news.php' ? 'active' : ''; ?>">My News & Blogs</a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>dashboard/user/menu/highlights/highlights.php"
               class="nav-link <?php echo $currentPage === 'highlights.php' ? 'active' : ''; ?>">Highlights</a>
        </li>
    </ul>
</div>

<!-- Offcanvas Sidebar for Mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="offcanvasMenuLabel">Dashboard Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column gap-2">
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>dashboard/<?php echo $_SESSION['role']; ?>/dashboard.php" 
                   class="nav-link <?php echo $currentPage === 'statistics.php' ? 'active' : ''; ?>" 
                   data-bs-dismiss="offcanvas">Statistics</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>dashboard/user/menu/team/my_team.php"
                   class="nav-link <?php echo $currentPage === 'my_team.php' ? 'active' : ''; ?>" 
                   data-bs-dismiss="offcanvas">My Team</a>
            </li>
            <li class="nav-item">
                <a href="#tournaments" 
                   class="nav-link <?php echo $currentPage === 'tournaments.php' ? 'active' : ''; ?>" 
                   data-bs-dismiss="offcanvas">Tournaments</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>dashboard/user/menu/news/news.php" 
                   class="nav-link <?php echo $currentPage === 'news.php' ? 'active' : ''; ?>" 
                   data-bs-dismiss="offcanvas">My News & Blogs</a>
            </li>
        </ul>
    </div>
</div>
