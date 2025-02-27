<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Fetch dashboard statistics
try {
    $stats = [
        'total_teams' => $conn->query("SELECT COUNT(*) as count FROM team")->fetch()['count'],
        'total_tournaments' => $conn->query("SELECT COUNT(*) as count FROM tournament")->fetch()['count'],
        'total_users' => $conn->query("SELECT COUNT(*) as count FROM contact")->fetch()['count'],
        'total_matches' => $conn->query("SELECT COUNT(*) as count FROM match_played")->fetch()['count']
    ];
} catch (PDOException $e) {
    $stats = ['error' => $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Tournament Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .dashboard-card {
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'component/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div> -->
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Teams</h5>
                                        <p class="card-text display-6"><?php echo $stats['total_teams']; ?></p>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Tournaments</h5>
                                        <p class="card-text display-6"><?php echo $stats['total_tournaments']; ?></p>
                                    </div>
                                    <i class="fas fa-trophy fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Users</h5>
                                        <p class="card-text display-6"><?php echo $stats['total_users']; ?></p>
                                    </div>
                                    <i class="fas fa-user-friends fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Matches</h5>
                                        <p class="card-text display-6"><?php echo $stats['total_matches']; ?></p>
                                    </div>
                                    <i class="fas fa-futbol fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                Recent Activity
                            </div>
                            <div class="card-body">
                                <!-- Add recent activity log or recent entries from database -->
                                <p>No recent activity logged.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                Quick Actions
                            </div>
                            <div class="card-body">
                                <a href="teams.php" class="btn btn-outline-primary w-100 mb-2">Manage Teams</a>
                                <a href="tournaments.php" class="btn btn-outline-success w-100 mb-2">Manage Tournament</a>
                                <a href="users.php" class="btn btn-outline-info w-100">View Users</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>