<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$user_id = $_SESSION['user_id'];
$tournamentId = $_GET['tournament_id'] ?? null;
$errors = [];

// Fetch tournament details
if ($tournamentId) {
    $query = "SELECT * FROM tournament WHERE id = :tournament_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->execute();
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch existing teams in the tournament
$existing_teams = [];
if ($tournamentId) {
    $query = "
        SELECT t.id, t.name
        FROM team t
        JOIN tournament_team tt ON t.id = tt.team_id
        WHERE tt.tournament_id = :tournament_id
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->execute();
    $existing_teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch teams not yet in the tournament
$available_teams = [];
if ($tournamentId) {
    $query = "
        SELECT t.id, t.name
        FROM team t
        WHERE t.id NOT IN (
            SELECT team_id FROM tournament_team WHERE tournament_id = :tournament_id
        )
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->execute();
    $available_teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submission to add new teams
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamIds = $_POST['team_ids'] ?? [];

    if (empty($teamIds)) {
        $errors[] = "At least one team must be selected.";
    }

    if (empty($errors)) {
        foreach ($teamIds as $teamId) {
            $query = "INSERT INTO tournament_team (tournament_id, team_id) VALUES (:tournament_id, :team_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
            $stmt->execute();
        }

        $_SESSION['message'] = "Teams added successfully.";
        header("Location: tournament_organizer.php?success=1");
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
            <h2 class="text-center">Edit Tournament</h2>

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
            <div class="mb-3">
                    <label for="name" class="form-label">Tournament Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['name']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="venue" class="form-label">Venue</label>
                    <input type="text" name="venue" id="venue" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['venue']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="region" class="form-label">Region</label>
                    <input type="text" name="region" id="region" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['region']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="district" class="form-label">District</label>
                    <input type="text" name="district" id="district" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['district']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="thana" class="form-label">Thana</label>
                    <input type="text" name="thana" id="thana" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['thana']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="area" class="form-label">Area</label>
                    <input type="text" name="area" id="area" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['area']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="tour_type" class="form-label">Tournament Type</label>
                    <input type="text" name="tour_type" id="tour_type" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['tour_type']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['start_date']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="<?php echo htmlspecialchars($tournament['end_date']); ?>" required>
                </div>
                <h4>Existing Teams</h4>
                <?php if (!empty($existing_teams)): ?>
                    <ul class="list-group mb-3">
                        <?php foreach ($existing_teams as $team): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($team['name']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No teams added to this tournament yet.</p>
                <?php endif; ?>

                <h4>Add Teams</h4>
                <div class="mb-3">
                    <label for="team_ids" class="form-label">Available Teams</label>
                    <div id="team_ids"></div>
                        <?php foreach ($available_teams as $team): ?>
                            <div class="form-check"></div>
                                <input class="form-check-input" type="checkbox" name="team_ids[]" value="<?php echo htmlspecialchars($team['id']); ?>" id="team_<?php echo htmlspecialchars($team['id']); ?>">
                                <label class="form-check-label" for="team_<?php echo htmlspecialchars($team['id']); ?>">
                                    <?php echo htmlspecialchars($team['name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="tournament_organizer.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>