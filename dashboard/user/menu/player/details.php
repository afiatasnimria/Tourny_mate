<?php
require_once '../../../../config.php';
require_once '../../../../utils.php';
include '../../../../components/shared/user-header.php';

// Define Base URL

// Base uploads directory for profile pictures
$baseUploadsPath = '../../../../uploads/user/';

// Get the player ID from the query string
$playerId = $_GET['player_id'] ?? null;

if (!$playerId) {
    die("Player ID is required.");
}

// Fetch player details from `team_player`
try {
    $query = "
        SELECT 
            tp.user_id AS player_id,
            u.fullName AS player_name,
            t.name AS team_name,
            u.email,
            u.phone,
            COALESCE(u.dp, 'default_dp.jpg') AS profile_picture,
            COALESCE(u.cover, 'default_cover.jpg') AS cover_picture
        FROM team_player tp
        JOIN userinfo u ON tp.user_id = u.id
        JOIN team t ON tp.team_id = t.id
        WHERE tp.user_id = :player_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':player_id', $playerId, PDO::PARAM_INT);
    $stmt->execute();
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$player) {
        die("Player not found.");
    }
} catch (PDOException $e) {
    die("Error fetching player details: " . $e->getMessage());
}

// Fetch player statistics
try {
    $statsQuery = "
        SELECT 
            COALESCE(SUM(runs), -1) AS total_runs,
            COALESCE(SUM(total_six), -1) AS total_sixes,
            COALESCE(SUM(total_fours), -1) AS total_fours,
            COALESCE(SUM(total_wickets), -1) AS total_wickets,
            COALESCE(SUM(total_over), -1) AS total_overs,
            COALESCE(SUM(total_dots), -1) AS total_dots
        FROM individual_score
        WHERE user_id = :player_id";
    $stmt = $conn->prepare($statsQuery);
    $stmt->bindParam(':player_id', $playerId, PDO::PARAM_INT);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching player statistics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Player Profile</h2>

        <!-- Player Profile Card -->
        <div class="card mb-4">
            <div class="row g-0">
            <img src="/tourny_mate/uploads/user/<?= htmlspecialchars($player['cover_picture'] ?: 'uploads/default-cover.jpg'); ?>" 
             alt="Cover Image" 
             class="img-fluid w-100 rounded" style="height: 300px; object-fit: cover;">
                <div class="col-md-4">
                    <img src="/tourny_mate/uploads/user/<?= htmlspecialchars($player['profile_picture']); ?>" 
                         class="img-fluid rounded-start" alt="Profile Picture">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo htmlspecialchars($player['player_name']); ?></h4>
                        <p class="card-text">
                            <strong>Team:</strong> <?php echo htmlspecialchars($player['team_name']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($player['email']); ?><br>
                            <strong>Phone:</strong> <?php echo htmlspecialchars($player['phone']); ?><br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row">
            <!-- Batting Statistics -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Batting Statistics (Bar Chart)</h5>
                        <canvas id="battingChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Bowling Statistics -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bowling Statistics (Pie Chart)</h5>
                        <canvas id="bowlingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Batting Data
        const battingData = {
            labels: ['Total Runs', 'Sixes', 'Fours'],
            datasets: [{
                label: 'Batting Stats',
                data: [
                    <?php echo $stats['total_runs']; ?>,
                    <?php echo $stats['total_sixes']; ?>,
                    <?php echo $stats['total_fours']; ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Bowling Data
        const bowlingData = {
            labels: ['Wickets', 'Overs', 'Dot Balls'],
            datasets: [{
                label: 'Bowling Stats',
                data: [
                    <?php echo $stats['total_wickets']; ?>,
                    <?php echo $stats['total_overs']; ?>,
                    <?php echo $stats['total_dots']; ?>
                ],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Bar Chart for Batting
        const battingChartConfig = {
            type: 'bar',
            data: battingData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Pie Chart for Bowling
        const bowlingChartConfig = {
            type: 'pie',
            data: bowlingData
        };

        // Render Charts
        const battingCtx = document.getElementById('battingChart').getContext('2d');
        const bowlingCtx = document.getElementById('bowlingChart').getContext('2d');
        new Chart(battingCtx, battingChartConfig);
        new Chart(bowlingCtx, bowlingChartConfig);
    </script>

<?php include '../../../../components/shared/user-footer.php'; ?>
