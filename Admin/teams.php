<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle team deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $team_id = $_GET['delete'];
    
    try {
        // Start transaction to handle related records
        $conn->beginTransaction();

        // Delete related team_player entries
        $delete_players_stmt = $conn->prepare("DELETE FROM team_player WHERE team_id = :team_id");
        $delete_players_stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $delete_players_stmt->execute();

        // Delete related tournament_team entries
        $delete_tournament_teams_stmt = $conn->prepare("DELETE FROM tournament_team WHERE team_id = :team_id");
        $delete_tournament_teams_stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $delete_tournament_teams_stmt->execute();

        // Delete related tournament_team_score entries
        $delete_scores_stmt = $conn->prepare("DELETE FROM tournament_team_score WHERE team_id = :team_id");
        $delete_scores_stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $delete_scores_stmt->execute();

        // Delete team
        $delete_stmt = $conn->prepare("DELETE FROM team WHERE id = :team_id");
        $delete_stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $delete_stmt->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['message'] = "Team deleted successfully.";
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['error'] = "Error deleting team: " . $e->getMessage();
    }
    
    header("Location: teams.php");
    exit();
}

// Fetch teams with additional details
try {
    $teams_query = $conn->query("
        SELECT 
            t.id, 
            t.name, 
            t.logo,
            c.full_name as manager_name,
            (SELECT COUNT(*) FROM team_player tp WHERE tp.team_id = t.id) as player_count,
            (SELECT COUNT(DISTINCT tournament_id) FROM tournament_team tt WHERE tt.team_id = t.id) as tournament_count
        FROM team t
        LEFT JOIN contact c ON t.manager_id = c.id
        ORDER BY t.name
    ");
    $teams = $teams_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $teams = [];
    $_SESSION['error'] = "Error fetching teams: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .team-logo {
            max-height: 150px;
            object-fit: contain;
        }
        .team-card {
            transition: transform 0.3s;
        }
        .team-card:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'component/sidebar.php'; ?>
            
            <main class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Team Management</h1>
                </div>

                <?php 
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
                    unset($_SESSION['message']);
                }
                if (isset($_SESSION['error'])) {
                    echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                    unset($_SESSION['error']);
                }
                ?>

                <div class="row">
                    <?php if (empty($teams)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                No teams found.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($teams as $team): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card team-card">
                                    <?php if ($team['logo']): ?>
                                        <img src="/tourny_mate/uploads/logos/<?php echo htmlspecialchars($team['logo']); ?>" 
                                             class="card-img-top team-logo" 
                                             alt="Team Logo">
                                    <?php endif; ?>
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <?php echo htmlspecialchars($team['name']); ?>
                                            </h5>
                                            <?php if ($team['manager_name']): ?>
                                                <small class="text-muted">
                                                    Manager: <?php echo htmlspecialchars($team['manager_name']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="badge bg-info text-white p-2 me-2">
                                                    <i class="fas fa-users"></i> 
                                                    <?php echo $team['player_count']; ?> Players
                                                </div>
                                                <div class="badge bg-success text-white p-2">
                                                    <i class="fas fa-trophy"></i> 
                                                    <?php echo $team['tournament_count']; ?> Tournaments
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <a href="teams.php?delete=<?php echo $team['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this team? This will also remove all associated players and tournament data.');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>