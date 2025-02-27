<?php
require_once '../../../../config.php';

$teamId = $_GET['team_id'] ?? null;

if ($teamId) {
    // Delete team members
    $query = "DELETE FROM team_player WHERE team_id = :team_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->execute();

    // Delete the team
    $query = "DELETE FROM team WHERE id = :team_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->execute();
}

header("Location: my_team.php");
exit();
?>
