<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

// Initialize variables
$userId = $_SESSION['user_id'] ?? null;
$tournamentId = $_GET['tournament_id'] ?? null;
$matches = [];
$success = null;
$errors = [];

if (!$userId) {
    die("Error: User ID is not set in the session.");
}
if (!$tournamentId) {
    die("Error: Tournament ID is not provided in the URL.");
}

// Fetch matches
try {
    $query = "
        SELECT 
            mp.id AS match_id,
            mp.match_day,
            mp.match_type,
            t1.name AS team_1_name,
            t2.name AS team_2_name,
            s.team_1_score,
            s.team_2_score
        FROM match_played mp
        JOIN team t1 ON mp.team_1_id = t1.id
        JOIN team t2 ON mp.team_2_id = t2.id
        LEFT JOIN score s ON mp.id = s.match_id
        WHERE mp.tournament_id = :tournament_id
          AND :user_id IN (mp.official_1_id, mp.official_2_id, mp.official_3_id)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':tournament_id' => (int)$tournamentId,
        ':user_id' => (int)$userId,
    ]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Error fetching matches: " . $e->getMessage();
}

// Fetch players for the teams
$teamPlayers = [];
try {
    $teamIds = array_column($matches, 'team_1_id');
    $teamIds = array_merge($teamIds, array_column($matches, 'team_2_id'));
    $teamIds = array_unique($teamIds);

    if (!empty($teamIds)) {
        $placeholders = implode(',', array_fill(0, count($teamIds), '?'));
        $playerQuery = "
            SELECT 
                p.id AS player_id,
                p.team_id,
                u.fullName AS player_name
            FROM player p
            JOIN userinfo u ON p.user_id = u.id
            WHERE p.team_id IN ($placeholders)";
        $stmt = $conn->prepare($playerQuery);
        $stmt->execute($teamIds);
        $teamPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $errors[] = "Error fetching players: " . $e->getMessage();
}

// Update match and player scores
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchId = $_POST['match_id'] ?? null;
    $team1Score = $_POST['team_1_score'] ?? null;
    $team2Score = $_POST['team_2_score'] ?? null;
    $individualScores = $_POST['individual_scores'] ?? [];

    if (!$matchId || $team1Score === null || $team2Score === null) {
        $errors[] = "Match ID, team scores, and player scores are required.";
    } else {
        try {
            // Update match scores
            $updateMatchQuery = "
                INSERT INTO score (match_id, team_1_score, team_2_score)
                VALUES (:match_id, :team_1_score, :team_2_score)
                ON DUPLICATE KEY UPDATE 
                team_1_score = :team_1_score,
                team_2_score = :team_2_score";
            $stmt = $conn->prepare($updateMatchQuery);
            $stmt->execute([
                ':match_id' => $matchId,
                ':team_1_score' => $team1Score,
                ':team_2_score' => $team2Score,
            ]);

            // Update individual player scores
            foreach ($individualScores as $playerId => $scores) {
                $runs = $scores['runs'] ?? 0;
                $wickets = $scores['wickets'] ?? 0;

                $updatePlayerQuery = "
                    INSERT INTO individual_score (match_id, user_id, odi_runs, total_wickets)
                    VALUES (:match_id, :user_id, :runs, :wickets)
                    ON DUPLICATE KEY UPDATE 
                    odi_runs = :runs,
                    total_wickets = :wickets";
                $stmt = $conn->prepare($updatePlayerQuery);
                $stmt->execute([
                    ':match_id' => $matchId,
                    ':user_id' => $playerId,
                    ':runs' => $runs,
                    ':wickets' => $wickets,
                ]);
            }

            $success = "Scores updated successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error updating scores: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Edit Match and Player Scores</h2>

    <!-- Success and Error Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Matches Table -->
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
                    <td><?php echo htmlspecialchars($match['match_day']); ?></td>
                    <td><?php echo htmlspecialchars($match['match_type']); ?></td>
                    <td><?php echo htmlspecialchars($match['team_1_name']); ?></td>
                    <td><?php echo htmlspecialchars($match['team_2_name']); ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editScoreModal-<?php echo $match['match_id']; ?>">
                            Edit Scores
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="editScoreModal-<?php echo $match['match_id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Match and Player Scores</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="match_id" value="<?php echo $match['match_id']; ?>">
                                            <div class="mb-3">
                                                <label for="team1Score" class="form-label">Team 1 Score</label>
                                                <input type="number" name="team_1_score" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="team2Score" class="form-label">Team 2 Score</label>
                                                <input type="number" name="team_2_score" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Individual Player Scores</label>
                                                <div>
                                                    <?php foreach ($teamPlayers as $player): ?>
                                                        <?php if ($player['team_id'] == $match['team_1_id'] || $player['team_id'] == $match['team_2_id']): ?>
                                                            <div class="mb-3">
                                                                <label><?php echo htmlspecialchars($player['player_name']); ?></label>
                                                                <input type="number" name="individual_scores[<?php echo $player['player_id']; ?>][runs]" 
                                                                       placeholder="Runs" class="form-control mb-2">
                                                                <input type="number" name="individual_scores[<?php echo $player['player_id']; ?>][wickets]" 
                                                                       placeholder="Wickets" class="form-control">
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update Scores</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
