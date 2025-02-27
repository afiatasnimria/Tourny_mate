<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle tournament deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $tournament_id = $_GET['delete'];
    
    try {
        // Start transaction to handle related records
        $conn->beginTransaction();

        // Delete related tournament_officials entries
        $delete_officials_stmt = $conn->prepare("DELETE FROM tournament_officials WHERE tournament_id = :tournament_id");
        $delete_officials_stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $delete_officials_stmt->execute();

        // Delete related tournament_team entries
        $delete_teams_stmt = $conn->prepare("DELETE FROM tournament_team WHERE tournament_id = :tournament_id");
        $delete_teams_stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $delete_teams_stmt->execute();

        // Delete related tournament_team_score entries
        $delete_scores_stmt = $conn->prepare("DELETE FROM tournament_team_score WHERE tournament_id = :tournament_id");
        $delete_scores_stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $delete_scores_stmt->execute();

        // Delete tournament
        $delete_stmt = $conn->prepare("DELETE FROM tournament WHERE id = :tournament_id");
        $delete_stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $delete_stmt->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['message'] = "Tournament deleted successfully.";
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['error'] = "Error deleting tournament: " . $e->getMessage();
    }
    
    header("Location: tournaments.php");
    exit();
}

// Tournament creation removed as per user request

// Fetch tournaments with creator details
try {
    $tournaments_query = $conn->query("
        SELECT 
            t.id, 
            t.name, 
            t.venue, 
            t.region, 
            t.district, 
            t.thana, 
            t.area, 
            t.start_date,
            t.end_date,
            c.full_name as creator_name,
            (SELECT COUNT(*) FROM tournament_team tt WHERE tt.tournament_id = t.id) as team_count
        FROM tournament t
        JOIN contact c ON t.creator_id = c.id
        ORDER BY t.start_date DESC
    ");
    $tournaments = $tournaments_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tournaments = [];
    $_SESSION['error'] = "Error fetching tournaments: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tournament Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .tournament-card {
            transition: transform 0.3s;
        }
        .tournament-card:hover {
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
                    <h1 class="h2">Tournament Management</h1>
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
                    <?php if (empty($tournaments)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                No tournaments found.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($tournaments as $tournament): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card tournament-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <?php echo htmlspecialchars($tournament['name']); ?>
                                            </h5>
                                            <small class="text-muted">
                                                Created by: <?php echo htmlspecialchars($tournament['creator_name']); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('d M Y, h:i A', strtotime($tournament['start_date'])); ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="card-text">
                                                    <strong>Venue:</strong> <?php echo htmlspecialchars($tournament['venue']); ?><br>
                                                    <strong>Region:</strong> <?php echo htmlspecialchars($tournament['region']); ?><br>
                                                    <strong>District:</strong> <?php echo htmlspecialchars($tournament['district']); ?><br>
                                                    <strong>Thana:</strong> <?php echo htmlspecialchars($tournament['thana']); ?><br>
                                                    <strong>Area:</strong> <?php echo htmlspecialchars($tournament['area']); ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <div class="badge bg-info text-white p-2">
                                                    <i class="fas fa-users"></i> 
                                                    <?php echo $tournament['team_count']; ?> Teams
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="tournaments.php?delete=<?php echo $tournament['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this tournament? This will also remove all associated team and score data.');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
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