<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$tournamentId = $_GET['tournament_id'] ?? null;

if (!$tournamentId) {
    header("Location: official_tournament_list.php");
    exit();
}

// Fetch matches for the tournament
$query = "
    SELECT mp.id AS match_id, 
           t1.name AS team_1, 
           t2.name AS team_2, 
           mp.match_day, 
           mp.match_type
    FROM match_played mp
    JOIN team t1 ON mp.team_1_id = t1.id
    JOIN team t2 ON mp.team_2_id = t2.id
    WHERE mp.tournament_id = :tournament_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-5">
  <h2 class="text-center">Matches for Tournament</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Match Day</th>
        <th>Match Type</th>
        <th>Team 1</th>
        <th>Team 2</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matches as $match): ?>
        <tr>
          <td><?= htmlspecialchars($match['match_day']); ?></td>
          <td><?= htmlspecialchars($match['match_type']); ?></td>
          <td><?= htmlspecialchars($match['team_1']); ?></td>
          <td><?= htmlspecialchars($match['team_2']); ?></td>
          <td>
            <a href="update_score.php?match_id=<?= $match['match_id']; ?>&tournament_id=<?= $tournamentId; ?>" 
               class="btn btn-primary btn-sm">Update Score</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>