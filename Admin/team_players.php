<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle player deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $player_id = $_GET['delete'];
    
    try {
        // Delete player from team
        $delete_stmt = $conn->prepare("DELETE FROM team_player WHERE id = :player_id");
        $delete_stmt->bindParam(':player_id', $player_id, PDO::PARAM_INT);
        $delete_stmt->execute();
        
        $_SESSION['message'] = "Player removed from team successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error removing player: " . $e->getMessage();
    }
    
    header("Location: team_players.php");
    exit();
}

// Fetch teams
try {
    $teams_query = $conn->query("SELECT id, name FROM team ORDER BY name");
    $teams = $teams_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $teams = [];
    $_SESSION['error'] = "Error fetching teams: " . $e->getMessage();
}

// Fetch team players based on selected team
$selected_team = isset($_GET['team']) ? intval($_GET['team']) : null;
$team_players = [];

if ($selected_team) {
    try {
        $players_query = $conn->prepare("SELECT 
            tp.id as player_id, 
            c.full_name, 
            t.name as team_name, 
            tp.created_at
        FROM team_player tp
        JOIN contact c ON tp.user_id = c.id
        JOIN team t ON tp.team_id = t.id
        WHERE t.id = :team_id");
        $players_query->bindParam(':team_id', $selected_team, PDO::PARAM_INT);
        $players_query->execute();
        $team_players = $players_query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error fetching team players: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Players Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'component/sidebar.php'; ?>
            
            <main class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Team Players Management</h1>
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
                    <div class="col-md-4 mb-3">
                        <form method="get" action="team_players.php">
                            <div class="card">
                                <div class="card-header">
                                    Select Team
                                </div>
                                <div class="card-body">
                                    <select name="team" class="form-control" onchange="this.form.submit()">
                                        <option value="">Select a Team</option>
                                        <?php foreach ($teams as $team): ?>
                                            <option value="<?php echo $team['id']; ?>" 
                                                <?php echo ($selected_team == $team['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($team['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-8">
                        <?php if ($selected_team): ?>
                            <div class="card">
                                <div class="card-header">
                                    Team Players
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($team_players)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Player Name</th>
                                                        <th>Team</th>
                                                        <th>Added On</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($team_players as $player): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($player['full_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($player['team_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($player['created_at']); ?></td>
                                                        <td>
                                                            <a href="team_players.php?team=<?php echo $selected_team; ?>&delete=<?php echo $player['player_id']; ?>" 
                                                               class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('Are you sure you want to remove this player from the team?');">
                                                                <i class="fas fa-trash"></i> Remove
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No players found for this team.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
