<?php
require_once '../../../../config.php';

$teamId = $_GET['team_id'] ?? null;
$userId = $_GET['user_id'] ?? null;

if ($teamId && $userId) {
    $query = "DELETE FROM team_player WHERE team_id = :team_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
}

header("Location: my_team.php");
exit();
?>
