<?php
require_once 'config.php';
include './components/shared/general-header.php'; // Include header

// Ensure a player ID is provided
if (!isset($_GET['player_id']) || !is_numeric($_GET['player_id'])) {
  die("Invalid player ID.");
}

$player_id = $_GET['player_id'];

// Fetch player details, stats, and total matches played
try {
  $query = "
        SELECT u.fullName, u.dp, u.email, u.phone, 
               COUNT(ind.match_id) AS total_matches,
               SUM(ind.runs) AS total_runs, 
               SUM(ind.total_wickets) AS total_wickets, 
               SUM(ind.total_goals) AS total_goals
        FROM userinfo u
        LEFT JOIN individual_score ind ON u.id = ind.user_id
        WHERE u.id = :player_id
        GROUP BY u.id";
  $stmt = $conn->prepare($query);
  $stmt->execute(['player_id' => $player_id]);
  $player = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$player) {
    die("Player not found.");
  }
} catch (PDOException $e) {
  die("Error fetching player details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($player['fullName']); ?> - Player Details</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f4f4f9;
    }

    .player-header {
      text-align: center;
      margin-top: 2rem;
      margin-bottom: 2rem;
    }

    .player-header img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 5px solid #007bff;
    }

    .player-header h1 {
      margin-top: 1rem;
      font-size: 2rem;
      color: #333;
    }

    .player-header p {
      color: #666;
      font-size: 1rem;
    }

    .section-title {
      color: #333;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 2rem;
      text-align: center;
    }

    .chart-container {
      margin: 2rem auto;
      max-width: 600px;
    }

    .stats-container {
      display: flex;
      justify-content: space-around;
      margin-bottom: 2rem;
    }

    .stat-box {
      text-align: center;
      padding: 1rem;
      border-radius: 10px;
      background-color: #ffffff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stat-box h5 {
      font-size: 1.2rem;
      color: #007bff;
    }

    .stat-box p {
      font-size: 1.5rem;
      font-weight: bold;
      margin: 0;
    }
  </style>
</head>

<body>

  <div class="container">
    <!-- Player Header -->
    <div class="player-header">
      <img src="<?= !empty($player['dp']) ? '/tourny_mate/uploads/user/' . htmlspecialchars($player['dp']) : 'https://img.freepik.com/free-vector/online-games-illustrated_23-2148540054.jpg?ga=GA1.1.1320900330.1735297158&semt=ais_hybrid' ?>"
        alt="<?= htmlspecialchars($player['fullName']); ?>">
      <h1><?= htmlspecialchars($player['fullName']); ?></h1>
      <p><?= htmlspecialchars($player['email']); ?> | <?= htmlspecialchars($player['phone']); ?></p>
    </div>

    <!-- Player Stats Section -->
    <div class="section-title">Player Statistics</div>

    <div class="stats-container">
      <div class="stat-box">
        <h5>Total Matches</h5>
        <p><?= htmlspecialchars($player['total_matches'] ?: 0); ?></p>
      </div>
      <div class="stat-box">
        <h5>Total Runs</h5>
        <p><?= htmlspecialchars($player['total_runs'] ?: 0); ?></p>
      </div>
      <div class="stat-box">
        <h5>Total Wickets</h5>
        <p><?= htmlspecialchars($player['total_wickets'] ?: 0); ?></p>
      </div>
      <div class="stat-box">
        <h5>Total Goals</h5>
        <p><?= htmlspecialchars($player['total_goals'] ?: 0); ?></p>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="section-title">Performance Overview</div>

    <!-- Matches vs Performance Chart -->
    <div class="chart-container">
      <canvas id="matchesVsPerformanceChart"></canvas>
    </div>
  </div>

  <script>
    // Matches vs Performance Chart
    const ctx = document.getElementById('matchesVsPerformanceChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar', // Bar chart for a clean visual
      data: {
        labels: ['Total Matches Played'], // X-axis
        datasets: [{
            label: 'Runs',
            data: [<?= $player['total_runs'] ?: 0; ?>], // Runs data
            backgroundColor: '#007bff'
          },
          {
            label: 'Wickets',
            data: [<?= $player['total_wickets'] ?: 0; ?>], // Wickets data
            backgroundColor: '#28a745'
          },
          {
            label: 'Goals',
            data: [<?= $player['total_goals'] ?: 0; ?>], // Goals data
            backgroundColor: '#ffc107'
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          tooltip: {
            enabled: true
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Metrics'
            }
          },
          y: {
            title: {
              display: true,
              text: 'Values'
            },
            beginAtZero: true
          }
        }
      }
    });
  </script>

  <?php include './components/shared/general-footer.php'; ?>