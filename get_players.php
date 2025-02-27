<?php
require_once 'config.php';

// Get team ID from query string
$team_id = intval($_GET['team_id'] ?? 0);

try {
    $stmt = $conn->prepare("SELECT player_name FROM team_player WHERE team_id = :team_id");
    $stmt->execute([':team_id' => $team_id]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($players);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
