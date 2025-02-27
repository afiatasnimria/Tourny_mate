<?php
require_once '../../../../config.php';
require_once '../../../../utils.php';
include '../../../../components/shared/user-header.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../../login-form.php");
    exit();
}

// Get tournament ID from the URL
$tournament_id = isset($_GET['tournament_id']) ? (int)$_GET['tournament_id'] : 0;

if ($tournament_id <= 0) {
    die("Invalid tournament ID.");
}

// Fetch tournament details and teams
try {
    $query = "SELECT * FROM tournament WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $tournament_id]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        die("Tournament not found.");
    }

    if($tournament['creator_id'] != $_SESSION['user_id']) {
    header("Location: /tourny_mate/logout.php");
    exit();
    }

    // Fetch all teams in the tournament
    $teamQuery = "
        SELECT t.id, t.name, t.logo
        FROM team t
        JOIN tournament_team tt ON t.id = tt.team_id
        WHERE tt.tournament_id = :tournament_id";
    $stmt = $conn->prepare($teamQuery);
    $stmt->execute(['tournament_id' => $tournament_id]);
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch matches for the tournament
    $matchQuery = "
        SELECT 
            mp.id, 
            t1.name AS team_1, 
            t2.name AS team_2, 
            mp.match_day, 
            mp.match_type
        FROM match_played mp
        JOIN team t1 ON mp.team_1_id = t1.id
        JOIN team t2 ON mp.team_2_id = t2.id
        WHERE mp.tournament_id = :tournament_id
        ORDER BY mp.match_day ASC";
    $stmt = $conn->prepare($matchQuery);
    $stmt->execute(['tournament_id' => $tournament_id]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle Match Scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_1_id = $_POST['team_1_id'] ?? 0;
    $team_2_id = $_POST['team_2_id'] ?? 0;
    $match_day = $_POST['match_day'] ?? '';
    $match_type = $_POST['match_type'] ?? '';

    // Check if the user is the creator of the tournament
    $creatorQuery = "SELECT creator_id FROM tournament WHERE id = :id";
    $stmt = $conn->prepare($creatorQuery);
    $stmt->execute(['id' => $tournament_id]);
    $creator = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($creator['creator_id'] != $_SESSION['user_id']) {
        $error = "You are not authorized to schedule matches for this tournament.";
    } elseif ($team_1_id == $team_2_id) {
        $error = "Teams cannot be the same for a match.";
    } elseif (empty($match_day) || empty($match_type)) {
        $error = "Match day and match type are required.";
    } else {
        try {
            $insertMatch = "
                INSERT INTO match_played (tournament_id, team_1_id, team_2_id, match_day, match_type)
                VALUES (:tournament_id, :team_1_id, :team_2_id, :match_day, :match_type)";
            $stmt = $conn->prepare($insertMatch);
            $stmt->execute([
                'tournament_id' => $tournament_id,
                'team_1_id' => $team_1_id,
                'team_2_id' => $team_2_id,
                'match_day' => $match_day,
                'match_type' => $match_type,
            ]);

            $success = "Match scheduled successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
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
            <h1 class="text-center mb-4"><?= htmlspecialchars($tournament['name']); ?> - Tournament Details</h1>

            <div class="mb-5">
                <h3>Tournament Information</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Venue:</strong> <?= htmlspecialchars($tournament['venue']); ?></p>
                                <p><strong>Region:</strong> <?= htmlspecialchars($tournament['region']); ?></p>
                                <p><strong>District:</strong> <?= htmlspecialchars($tournament['district']); ?></p>
                                <p><strong>Thana:</strong> <?= htmlspecialchars($tournament['thana']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Area:</strong> <?= htmlspecialchars($tournament['area']); ?></p>
                                <p><strong>Type:</strong> <?= htmlspecialchars($tournament['tour_type']); ?></p>
                                <p><strong>Start Date:</strong> <?= htmlspecialchars($tournament['start_date']); ?></p>
                                <p><strong>End Date:</strong> <?= htmlspecialchars($tournament['end_date']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="mb-5">
                <h3>Participating Teams</h3>
                <div class="row">
                    <?php if (!empty($teams)): ?>
                        <?php foreach ($teams as $team): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <img src="<?= BASE_URL . '/uploads/logos/' . htmlspecialchars($team['logo'] ?: 'default-team-logo.png'); ?>"
                                        alt="<?= htmlspecialchars($team['name']); ?>"
                                        class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title text-center"><?= htmlspecialchars($team['name']); ?></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No teams are participating in this tournament.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-5">
                <h3>Scheduled Matches</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Team 1</th>
                            <th>Team 2</th>
                            <th>Match Day</th>
                            <th>Match Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($matches)): ?>
                            <?php foreach ($matches as $index => $match): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= htmlspecialchars($match['team_1']); ?></td>
                                    <td><?= htmlspecialchars($match['team_2']); ?></td>
                                    <td><?= htmlspecialchars($match['match_day']); ?></td>
                                    <td><?= htmlspecialchars($match['match_type']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No matches scheduled yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mb-5">
                <h3>Schedule a Match</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="team_1_id" class="form-label">Team 1</label>
                            <select name="team_1_id" id="team_1_id" class="form-select" required>
                                <option value="">Select Team 1</option>
                                <?php foreach ($teams as $team): ?>
                                    <option value="<?= $team['id']; ?>"><?= htmlspecialchars($team['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="team_2_id" class="form-label">Team 2</label>
                            <select name="team_2_id" id="team_2_id" class="form-select" required>
                                <option value="">Select Team 2</option>
                                <?php foreach ($teams as $team): ?>
                                    <option value="<?= $team['id']; ?>"><?= htmlspecialchars($team['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="match_day" class="form-label">Match Day</label>
                            <input type="datetime-local" name="match_day" id="match_day" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="match_type" class="form-label">Match Type</label>
                            <select name="match_type" id="match_type" class="form-select" required>
                                <option value="">Select Match Type</option>
                                <option value="T20">T20</option>
                                <option value="ODI">ODI</option>
                                <option value="Friendly">Friendly</option>
                                <option value="Football">Football</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Schedule Match</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>