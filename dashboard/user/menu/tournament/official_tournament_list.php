<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'] ?? null;
$errors = [];

if (!$userId) {
  header("Location: ../../../../login-form.php");
  exit();
}

// Fetch tournaments where the user is an official
$query = "
    SELECT t.id AS tournament_id, t.name AS tournament_name, t.venue, t.start_date, t.end_date
    FROM tournament_officials o
    JOIN tournament t ON o.tournament_id = t.id
    WHERE o.official_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle score update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tournamentId = $_POST['tournament_id'] ?? null;
  $score = $_POST['score'] ?? '';

  if (empty($tournamentId) || empty($score)) {
    $errors[] = "Tournament ID and Score are required.";
  } else {
    try {
      $query = "
                UPDATE tournament_team
                SET score = :score
                WHERE tournament_id = :tournament_id";
      $stmt = $conn->prepare($query);
      $stmt->bindParam(':score', $score);
      $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
      $stmt->execute();

      header("Location: official_tournament_list.php?success=1");
      exit();
    } catch (PDOException $e) {
      $errors[] = "Database error: " . $e->getMessage();
    }
  }
}
?>

<div class="container-fluid mt-5">
  <div class="row">
    <div class="col-lg-3">
      <?php include '../../../../components/shared/dashboard-menu.php'; ?>
    </div>
    <div class="col-lg-9">
      <h2 class="text-center">Tournaments as Official</h2>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">Score updated successfully!</div>
      <?php endif; ?>

      <?php if (!empty($tournaments)): ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Tournament Name</th>
              <th>Venue</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Actions gdfhdhdhdf</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tournaments as $tournament): ?>
              <tr>
                <td><?php echo htmlspecialchars($tournament['tournament_name']); ?></td>
                <td><?php echo htmlspecialchars($tournament['venue']); ?></td>
                <td><?php echo htmlspecialchars($tournament['start_date']); ?></td>
                <td><?php echo htmlspecialchars($tournament['end_date']); ?></td>
                <td>
                <a href="manage_matches.php?tournament_id=<?php echo $tournament['tournament_id']; ?>" class="btn btn-primary btn-sm">Edit Scores</a>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      <?php else: ?>
        <div class="alert alert-warning text-center">You are not assigned as an official for any tournaments.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>