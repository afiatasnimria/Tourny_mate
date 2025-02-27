<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$teamId = $_GET['team_id'] ?? null;
$errors = [];

// Fetch available users for selection (users not assigned to any team)
$users = [];
$query = "
    SELECT id, fullName, email 
    FROM userinfo 
    WHERE id NOT IN (SELECT user_id FROM team_player)
    LIMIT 10
";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userIds = $_POST['user_ids'] ?? [];

  if (empty($userIds)) {
    $errors[] = "At least one user must be selected.";
  }

  if (empty($errors)) {
    foreach ($userIds as $userId) {
      $query = "INSERT INTO team_player (team_id, user_id, created_at) VALUES (:team_id, :user_id, NOW())";
      $stmt = $conn->prepare($query);
      $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
      $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
      $stmt->execute();
    }

    header("Location: my_team.php?success=1");
    exit();
  }
}
?>

<div class="container-fluid mt-5">
  <div class="row">
    <div class="col-lg-3">
      <?php include '../../../../components/shared/dashboard-menu.php'; ?>
    </div>
    <div class="col-lg-9">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Add Members to Team</h5>
          <a href="my_team.php" class="btn btn-light btn-sm">Back to Team</a>
        </div>
        <div class="card-body">
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach ($errors as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-4">
              <label for="user_ids" class="form-label">Select Users</label>
              <div class="card">
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                  <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="user_ids[]"
                          value="<?php echo htmlspecialchars($user['id']); ?>"
                          id="user_<?php echo htmlspecialchars($user['id']); ?>">
                        <label class="form-check-label" for="user_<?php echo htmlspecialchars($user['id']); ?>">
                          <?php echo htmlspecialchars($user['fullName']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                        </label>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="text-muted">No users available to add to the team.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary me-2">Add Members</button>
              <a href="my_team.php" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
