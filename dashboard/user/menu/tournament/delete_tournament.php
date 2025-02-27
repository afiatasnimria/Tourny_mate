<?php
require_once '../../../../config.php';

$tournamentId = $_GET['tournament_id'] ?? null;

if ($tournamentId) {
    try {
        // Delete related records from `tournament_team` and `tournament_official`
        $query = "DELETE FROM tournament_team WHERE tournament_id = :tournament_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();

        $query = "DELETE FROM tournament_officials WHERE tournament_id = :tournament_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the tournament itself
        $query = "DELETE FROM tournament WHERE id = :tournament_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: tournament_organizer.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: tournament_organizer.php?error=1");
    exit();
}
?>
