<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$matchId = $_GET['match_id'] ?? null;
$tournamentId = $_GET['tournament_id'] ?? null;

if (!$matchId || !$tournamentId) {
    die("Match ID and Tournament ID are required.");
}

// Fetch match and team details
$query = "
    SELECT mp.id AS match_id, 
           mp.tournament_id, 
           t1.id AS team_1_id, t1.name AS team_1, 
           t2.id AS team_2_id, t2.name AS team_2
    FROM match_played mp
    JOIN team t1 ON mp.team_1_id = t1.id
    JOIN team t2 ON mp.team_2_id = t2.id
    WHERE mp.id = :match_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
$stmt->execute();
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    die("Match not found.");
}

// Fetch team scores
$teamScoresQuery = "
    SELECT tts.team_id, 
           t.name AS team_name, 
           COALESCE(tts.score, 0) AS team_score
    FROM tournament_team_score tts
    JOIN team t ON tts.team_id = t.id
    WHERE tts.tournament_id = :tournament_id";
$stmt = $conn->prepare($teamScoresQuery);
$stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
$stmt->execute();
$teamScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch players grouped by teams
$playerQuery = "
    SELECT tp.user_id, 
           u.fullName AS player_name, 
           t.name AS team_name, 
           tp.team_id,
           IFNULL(s.runs, 0) AS runs,
           IFNULL(s.total_six, 0) AS total_six,
           IFNULL(s.total_fours, 0) AS total_fours,
           IFNULL(s.total_wickets, 0) AS total_wickets
    FROM team_player tp
    JOIN userinfo u ON tp.user_id = u.id
    JOIN team t ON tp.team_id = t.id
    LEFT JOIN individual_score s 
        ON tp.user_id = s.user_id AND s.match_id = :match_id
    WHERE tp.team_id = :team_1_id OR tp.team_id = :team_2_id
    ORDER BY tp.team_id, u.fullName";

$stmt = $conn->prepare($playerQuery);
$stmt->bindValue(':team_1_id', $match['team_1_id'], PDO::PARAM_INT);
$stmt->bindValue(':team_2_id', $match['team_2_id'], PDO::PARAM_INT);
$stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
$stmt->execute();

$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group players by team
$playersByTeam = [];
foreach ($players as $player) {
    $playersByTeam[$player['team_name']][] = $player;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionType = $_POST['action_type'] ?? null;

    if ($actionType === 'update_player_stats') {
        $playerId = $_POST['player_id'] ?? null;
        $teamId = $_POST['team_id'] ?? null;
        $statType = $_POST['stat_type'] ?? null;

        if ($playerId && $teamId && $statType) {
            $statMapping = [
                "six" => 6,
                "four" => 4,
                "three" => 3,
                "two" => 2,
                "one" => 1,
                "wicket" => 0,
            ];

            $runsToAdd = $statMapping[$statType] ?? null;

            if ($runsToAdd === null) {
                echo "<div class='alert alert-danger'>Invalid stat type.</div>";
                return;
            }

            try {
                // Update or insert into individual_score
                $checkQuery = "
                    SELECT COUNT(*) AS count
                    FROM individual_score
                    WHERE match_id = :match_id 
                      AND tournament_id = :tournament_id 
                      AND user_id = :user_id 
                      AND team_id = :team_id";

                $stmt = $conn->prepare($checkQuery);
                $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
                $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->execute();

                $recordExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

                if ($recordExists) {
                    // Update existing record
                    $updateQuery = "
                        UPDATE individual_score
                        SET runs = runs + :runs_to_add
                        WHERE match_id = :match_id 
                          AND tournament_id = :tournament_id 
                          AND user_id = :user_id 
                          AND team_id = :team_id";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bindValue(':runs_to_add', $runsToAdd, PDO::PARAM_INT);
                    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
                    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // Insert new record
                    $insertQuery = "
                        INSERT INTO individual_score (match_id, tournament_id, user_id, team_id, runs)
                        VALUES (:match_id, :tournament_id, :user_id, :team_id, :runs_to_add)";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
                    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->bindValue(':runs_to_add', $runsToAdd, PDO::PARAM_INT);
                    $stmt->execute();
                }

                // Update or insert into tournament_team_score
                $checkTournamentScoreQuery = "
                    SELECT COUNT(*) AS count
                    FROM tournament_team_score
                    WHERE tournament_id = :tournament_id 
                      AND team_id = :team_id";
                $stmt = $conn->prepare($checkTournamentScoreQuery);
                $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
                $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->execute();

                $teamRecordExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

                if ($teamRecordExists) {
                    // Update existing team score
                    $updateTeamScoreQuery = "
                        UPDATE tournament_team_score
                        SET score = score + :runs_to_add
                        WHERE tournament_id = :tournament_id AND team_id = :team_id";
                    $stmt = $conn->prepare($updateTeamScoreQuery);
                    $stmt->bindValue(':runs_to_add', $runsToAdd, PDO::PARAM_INT);
                    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // Insert new team score
                    $insertTeamScoreQuery = "
                        INSERT INTO tournament_team_score (tournament_id, team_id, score)
                        VALUES (:tournament_id, :team_id, :runs_to_add)";
                    $stmt = $conn->prepare($insertTeamScoreQuery);
                    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
                    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
                    $stmt->bindValue(':runs_to_add', $runsToAdd, PDO::PARAM_INT);
                    $stmt->execute();
                }

                echo "<div class='alert alert-success'>Player stats and team score updated successfully.</div>";
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<div class="container mt-5">
  <h2 class="text-center">Team Scores</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Team Name</th>
        <th>Score</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($teamScores as $teamScore): ?>
        <tr>
          <td><?= htmlspecialchars($teamScore['team_name']); ?></td>
          <td><?= $teamScore['team_score']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h4 class="mt-5">Individual Player Statistics</h4>
  <?php if (!empty($playersByTeam)): ?>
    <?php foreach ($playersByTeam as $teamName => $teamPlayers): ?>
      <h5><?= htmlspecialchars($teamName); ?></h5>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Player Name</th>
            <th>Runs</th>
            <th>Sixes</th>
            <th>Fours</th>
            <th>Wickets</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($teamPlayers as $player): ?>
            <tr>
              <td><?= htmlspecialchars($player['player_name']); ?></td>
              <td><?= $player['runs']; ?></td>
              <td><?= $player['total_six']; ?></td>
              <td><?= $player['total_fours']; ?></td>
              <td><?= $player['total_wickets']; ?></td>
              <td>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="action_type" value="update_player_stats">
                  <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                  <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                  <input type="hidden" name="stat_type" value="six">
                  <button type="submit" class="btn btn-success btn-sm">+6</button>
                </form>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="action_type" value="update_player_stats">
                  <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                  <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                  <input type="hidden" name="stat_type" value="four">
                  <button type="submit" class="btn btn-primary btn-sm">+4</button>
                </form>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="action_type" value="update_player_stats">
                  <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                  <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                  <input type="hidden" name="stat_type" value="three">
                  <button type="submit" class="btn btn-info btn-sm">+3</button>
                </form>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="action_type" value="update_player_stats">
                  <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                  <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                  <input type="hidden" name="stat_type" value="two">
                  <button type="submit" class="btn btn-primary btn-sm">+2</button>
                </form>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="action_type" value="update_player_stats">
                  <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                  <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                  <input type="hidden" name="stat_type" value="one">
                  <button type="submit" class="btn btn-secondary btn-sm">+1 Run</button>
                </form>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="action_type" value="update_player_stats">
                  <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                  <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                  <input type="hidden" name="stat_type" value="wicket">
                  <button type="submit" class="btn btn-danger btn-sm">Out</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No players found for this match.</p>
  <?php endif; ?>
</div>
