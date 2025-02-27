<?php

require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$matchId = $_GET['match_id'] ?? null;
$tournamentId = $_GET['tournament_id'] ?? null;

if (!$matchId || !$tournamentId) {
  die("Match ID and Tournament ID are required.");
}
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  die("User not logged in.");
}

$query = "
  SELECT COUNT(*) AS count
  FROM tournament_officials
  WHERE official_id = :user_id 
    AND tournament_id = :tournament_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
$stmt->execute();
$isOfficial = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

if (!$isOfficial) {
  header("Location: /tourny_mate/logout.php");
  exit();
}
function fetchTournamentType($conn, $tournamentId)
{
  $query = "SELECT tour_type FROM tournament WHERE id = :tournament_id";
  $stmt = $conn->prepare($query);
  $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC)['tour_type'] ?? null;
}

function fetchMatchDetails($conn, $matchId)
{
  $query = "
        SELECT mp.id AS match_id, 
               mp.tournament_id, 
               mp.match_end,  -- Include match_end here
               t1.id AS team_1_id, 
               t1.name AS team_1, 
               t2.id AS team_2_id, 
               t2.name AS team_2
        FROM match_played mp
        JOIN team t1 ON mp.team_1_id = t1.id
        JOIN team t2 ON mp.team_2_id = t2.id
        WHERE mp.id = :match_id";
  $stmt = $conn->prepare($query);
  $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC);
}


function fetchTeamScores($conn, $tournamentId, $matchId)
{
  $query = "
        SELECT tts.team_id, 
               t.name AS team_name, 
               COALESCE(tts.score, 0) AS team_score,
               COALESCE(tts.wickets, 0) AS team_wickets,
               COALESCE(tts.goals, 0) AS goals
        FROM tournament_team_score tts
        JOIN team t ON tts.team_id = t.id
        WHERE tts.tournament_id = :tournament_id AND tts.match_id = :match_id";
  $stmt = $conn->prepare($query);
  $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
  $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function fetchPlayers($conn, $matchId, $team1Id, $team2Id)
{
  $query = "
        SELECT tp.user_id, 
               u.fullName AS player_name, 
               t.name AS team_name, 
               tp.team_id,
               IFNULL(s.runs, 0) AS runs,
               IFNULL(s.total_six, 0) AS total_six,
               IFNULL(s.total_fours, 0) AS total_fours,
               IFNULL(s.total_wickets, 0) AS total_wickets,
               IFNULL(s.total_goals, 0) AS total_goals,
               IFNULL(s.total_assists, 0) AS total_assists,
               IFNULL(s.total_saves, 0) AS total_saves,
               s.is_out
        FROM team_player tp
        JOIN userinfo u ON tp.user_id = u.id
        JOIN team t ON tp.team_id = t.id
        LEFT JOIN individual_score s 
            ON tp.user_id = s.user_id AND s.match_id = :match_id
        WHERE tp.team_id = :team_1_id OR tp.team_id = :team_2_id
        ORDER BY tp.team_id, u.fullName";
  $stmt = $conn->prepare($query);
  $stmt->bindValue(':team_1_id', $team1Id, PDO::PARAM_INT);
  $stmt->bindValue(':team_2_id', $team2Id, PDO::PARAM_INT);
  $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updatePlayerStats($conn, $matchId, $tournamentId, $playerId, $teamId, $runsToAdd, $notOut = false)
{
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
            SET runs = runs + :runs_to_add, is_out = :is_out
            WHERE match_id = :match_id 
              AND tournament_id = :tournament_id 
              AND user_id = :user_id 
              AND team_id = :team_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindValue(':runs_to_add', $runsToAdd, PDO::PARAM_INT);
    $stmt->bindValue(':is_out', $notOut, PDO::PARAM_BOOL);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->execute();
  } else {
    // Insert new record
    $insertQuery = "
            INSERT INTO individual_score (match_id, tournament_id, user_id, team_id, runs, is_out)
            VALUES (:match_id, :tournament_id, :user_id, :team_id, :runs_to_add, :is_out)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindValue(':runs_to_add', $runsToAdd, PDO::PARAM_INT);
    $stmt->bindValue(':is_out', $notOut, PDO::PARAM_BOOL);
    $stmt->execute();
  }
}

function updateFootballPlayerStats($conn, $matchId, $tournamentId, $playerId, $teamId, $goalsToAdd = 0, $assistsToAdd = 0, $savesToAdd = 0)
{
  // Check if the team score already exists
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

  // echo $recordExists;
  // die();
  if ($recordExists) {
    // Update existing record
    $updateQuery = "
      UPDATE individual_score
      SET total_goals = total_goals + :goalsToAdd
      WHERE match_id = :match_id 
        AND tournament_id = :tournament_id 
        AND user_id = :user_id 
        AND team_id = :team_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindValue(':goalsToAdd', $goalsToAdd, PDO::PARAM_INT);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->execute();
  } else {
    // Insert new record
    $insertQuery = "
      INSERT INTO individual_score (match_id, tournament_id, user_id, team_id, total_goals)
      VALUES (:match_id, :tournament_id, :user_id, :team_id, :goalsToAdd)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindValue(':goalsToAdd', $goalsToAdd, PDO::PARAM_INT);
    $stmt->execute();
  }
}


function updateTeamScore($conn, $tournamentId, $teamId, $matchId, $runsToAdd, $wicketsToAdd = 0)
{
  $checkTournamentScoreQuery = "
        SELECT COUNT(*) AS count, score, wickets 
        FROM tournament_team_score
        WHERE match_id = :match_id 
          AND tournament_id = :tournament_id 
          AND team_id = :team_id";
  $stmt = $conn->prepare($checkTournamentScoreQuery);
  $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
  $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
  $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result['count'] > 0) {
    // Update existing team score with runs and wickets
    $newScore = $result['score'] + $runsToAdd;
    $newWickets = $result['wickets'] + $wicketsToAdd;

    $updateTeamScoreQuery = "
            UPDATE tournament_team_score
            SET score = :new_score, wickets = :new_wickets
            WHERE match_id = :match_id 
              AND tournament_id = :tournament_id 
              AND team_id = :team_id";
    $stmt = $conn->prepare($updateTeamScoreQuery);
    $stmt->bindValue(':new_score', $newScore, PDO::PARAM_INT);
    $stmt->bindValue(':new_wickets', $newWickets, PDO::PARAM_INT);
    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->execute();
  } else {
    // Insert new team score with runs and wickets
    $newScore = $runsToAdd;
    $newWickets = $wicketsToAdd;

    $insertTeamScoreQuery = "
            INSERT INTO tournament_team_score (tournament_id, team_id, match_id, score, wickets)
            VALUES (:tournament_id, :team_id, :match_id, :new_score, :new_wickets)";
    $stmt = $conn->prepare($insertTeamScoreQuery);
    $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
    $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->bindValue(':new_score', $newScore, PDO::PARAM_INT);
    $stmt->bindValue(':new_wickets', $newWickets, PDO::PARAM_INT);
    $stmt->execute();
  }
}

function updateTeamScoreGoal($conn, $tournamentId, $teamId, $matchId, $goalsToAdd)
{
  // Use INSERT ... ON DUPLICATE KEY UPDATE for upserting
  $query = "
        INSERT INTO tournament_team_score (tournament_id, team_id, match_id, goals)
        VALUES (:tournament_id, :team_id, :match_id, :goals_to_add)
        ON DUPLICATE KEY UPDATE 
            goals = goals + :goals_to_add_update";

  $stmt = $conn->prepare($query);
  $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
  $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
  $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
  $stmt->bindValue(':goals_to_add', $goalsToAdd, PDO::PARAM_INT);
  $stmt->bindValue(':goals_to_add_update', $goalsToAdd, PDO::PARAM_INT);

  $stmt->execute();
}



$tourType = fetchTournamentType($conn, $tournamentId);
// Fetch match and team details
$match = fetchMatchDetails($conn, $matchId);

if (!$match) {
  die("Match not found.");
}

// Fetch team scores
$teamScores = fetchTeamScores($conn, $tournamentId, $matchId);

// Fetch players grouped by teams
$players = fetchPlayers($conn, $matchId, $match['team_1_id'], $match['team_2_id']);

// Group players by team
$playersByTeam = [];
foreach ($players as $player) {
  $playersByTeam[$player['team_name']][] = $player;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $actionType = $_POST['action_type'] ?? null;

  if ($actionType === 'update_player_football_stats') {
    $playerId = $_POST['player_id'] ?? null;
    $teamId = $_POST['team_id'] ?? null;
    $goal = isset($_POST['goal']) && $_POST['goal'] === '+1' ? 1 : 0;
    $assist = isset($_POST['assist']) && $_POST['assist'] === '+1' ? 1 : 0;
    $save = isset($_POST['save']) && $_POST['save'] === '+1' ? 1 : 0;

    updateFootballPlayerStats($conn, $matchId, $tournamentId, $playerId, $teamId, $goal, $assist, $save);
    updateTeamScoreGoal($conn, $tournamentId, $teamId, $matchId, $goal);
    $match = fetchMatchDetails($conn, $matchId);
    if (!$match) {
      die("Match not found.");
    }

    // Fetch team scores
    $teamScores = fetchTeamScores($conn, $tournamentId, $matchId);

    // Fetch players grouped by teams
    $players = fetchPlayers($conn, $matchId, $match['team_1_id'], $match['team_2_id']);

    // Group players by team
    $playersByTeam = [];
    foreach ($players as $player) {
      $playersByTeam[$player['team_name']][] = $player;
    }

    echo "<div class='alert alert-success'>Player stats and team score updated successfully.</div>";
  }

  if ($actionType === 'update_player_stats') {
    $playerId = $_POST['player_id'] ?? null;
    $teamId = $_POST['team_id'] ?? null;
    $statType = $_POST['stat_type'] ?? null;
    $isNotOut = 0;
    $totalSix = isset($_POST['total_six']) && $_POST['total_six'] === "1" ? true : false;
    $totalFour = isset($_POST['total_fours']) && $_POST['total_fours'] === "1" ? true : false;
    $totalThree = isset($_POST['total_three']) && $_POST['total_three'] === "1" ? true : false;
    $totalTwo = isset($_POST['total_two']) && $_POST['total_two'] === "1" ? true : false;
    $totalOne = isset($_POST['total_one']) && $_POST['total_one'] === "1" ? true : false;


    // echo $totalSix . $totalFour . $totalThree . $totalTwo . $totalOne;
    // die();
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
        // Update player stats
        updatePlayerStats($conn, $matchId, $tournamentId, $playerId, $teamId, $runsToAdd, $isNotOut);

        // Update team score
        updateTeamScore($conn, $tournamentId, $teamId, $matchId, $runsToAdd, 0);

        // Fetch match and team details
        $match = fetchMatchDetails($conn, $matchId);
        if (!$match) {
          die("Match not found.");
        }

        // Fetch team scores
        $teamScores = fetchTeamScores($conn, $tournamentId, $matchId);

        // Fetch players grouped by teams
        $players = fetchPlayers($conn, $matchId, $match['team_1_id'], $match['team_2_id']);

        // Group players by team
        $playersByTeam = [];
        foreach ($players as $player) {
          $playersByTeam[$player['team_name']][] = $player;
        }

        echo "<div class='alert alert-success'>Player stats and team score updated successfully.</div>";
      } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
      }
    }
  }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'end_match') {
  try {
    $updateQuery = "
          UPDATE match_played 
          SET match_end = 1 
          WHERE id = :match_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
    $stmt->execute();

    echo "<div class='alert alert-success'>Match ended successfully.</div>";

    // Refetch match details to update the UI
    $match['match_end'] = 1;
  } catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error ending the match: " . $e->getMessage() . "</div>";
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
  $playerId = $_POST['player_id'] ?? null;
  $teamId = $_POST['team_id'] ?? null;
  $statType = $_POST['stat_type'] ?? null;

  if ($playerId && $teamId && $statType === 'wicket') {
    try {
      // Update player stats to mark the player as out (or delete their stats)
      updatePlayerStats($conn, $matchId, $tournamentId, $playerId, $teamId, 0, true);

      // Optionally, update the team score if needed
      updateTeamScore($conn, $tournamentId, $teamId, -1, 1);  // Adjust accordingly for your score system

      // Fetch match and team details
      $match = fetchMatchDetails($conn, $matchId);
      if (!$match) {
        die("Match not found.");
      }

      // Fetch team scores
      $teamScores = fetchTeamScores($conn, $tournamentId, $matchId);

      // Fetch players grouped by teams
      $players = fetchPlayers($conn, $matchId, $match['team_1_id'], $match['team_2_id']);

      // Group players by team
      $playersByTeam = [];
      foreach ($players as $player) {
        $playersByTeam[$player['team_name']][] = $player;
      }

      echo "<div class='alert alert-success'>Player is out (wicket) and stats updated successfully.</div>";
    } catch (PDOException $e) {
      echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
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
              <td>
                <?php if ($tourType === 'cricket'): ?>
                  <?= $teamScore['team_score']; ?> / <?= $teamScore['team_wickets']; ?>
                <?php else: ?>
                  <?= $teamScore['goals']; ?>
                <?php endif; ?>
              </td>
            </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if (!$match['match_end']): ?>
        <form method="POST">
          <input type="hidden" name="action_type" value="end_match">
          <button type="submit" class="btn btn-danger btn-lg">End Match</button>
        </form>
      <?php else: ?>
        <div class="alert alert-info">This match has ended. Actions are disabled.</div>
      <?php endif; ?>

      <h4 class="mt-5">Individual Player Statistics</h4>
      <?php if (!empty($playersByTeam)): ?>
        <?php foreach ($playersByTeam as $teamName => $teamPlayers): ?>
          <h5><?= htmlspecialchars($teamName); ?></h5>
          <?php if ($tourType === 'cricket'): ?>
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
                      <!-- Action buttons for updating stats -->
                      <form method="POST" style="display:inline-block;"></form>
                      <input type="hidden" name="action_type" value="update_player_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="total_six" value="1">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="stat_type" value="six">
                      <button type="submit" class="btn btn-success btn-sm" <?= $player['is_out'] || $match['match_end'] ? 'disabled' : ''; ?>>+6</button>
                      </form>
                      <form method="POST" style="display:inline-block;"></form>
                      <input type="hidden" name="action_type" value="update_player_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="stat_type" value="four">
                      <input type="hidden" name="total_fours" value="1">
                      <button type="submit" class="btn btn-info btn-sm" <?= $player['is_out'] || $match['match_end'] ? 'disabled' : ''; ?>>+4</button>
                      </form>
                      <form method="POST" style="display:inline-block;"></form>
                      <input type="hidden" name="action_type" value="update_player_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="stat_type" value="three">
                      <input type="hidden" name="total_three" value="1">
                      <button type="submit" class="btn btn-info btn-sm" <?= $player['is_out'] || $match['match_end'] ? 'disabled' : ''; ?>>+3</button>
                      </form>
                      <form method="POST" style="display:inline-block;"></form>
                      <input type="hidden" name="action_type" value="update_player_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="stat_type" value="two">
                      <input type="hidden" name="total_two" value="1">
                      <button type="submit" class="btn btn-info btn-sm" <?= $player['is_out'] || $match['match_end'] ? 'disabled' : ''; ?>>+2</button>
                      </form>
                      <form method="POST" style="display:inline-block;"></form>
                      <input type="hidden" name="action_type" value="update_player_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="stat_type" value="one">
                      <input type="hidden" name="total_one" value="1">
                      <button type="submit" class="btn btn-info btn-sm" <?= $player['is_out'] || $match['match_end'] ? 'disabled' : ''; ?>>+1</button>
                      </form>
                      <form method="POST" style="display:inline-block;" onsubmit="return submitDeleteForm(this);"></form>
                      <input type="hidden" name="action_type" value="delete_player_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="stat_type" value="wicket">
                      <button type="submit" class="btn btn-danger btn-sm" <?= $player['is_out'] || $match['match_end'] ? 'disabled' : ''; ?>>Wicket</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Player Name</th>
                  <th>Goals</th>
                  <th>Assists</th>
                  <th>Saves</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($teamPlayers as $player): ?>
                  <tr>
                    <td><?= htmlspecialchars($player['player_name']); ?></td>
                    <td><?= $player['total_goals']; ?></td>
                    <td><?= $player['total_assists']; ?></td>
                    <td><?= $player['total_saves']; ?></td>
                    <td>
                      <!-- Action buttons for updating stats -->
                      <form method="POST" style="display:inline-block;"></form>
                      <input type="hidden" name="action_type" value="update_player_football_stats">
                      <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                      <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                      <input type="hidden" name="goal" value="+1">
                      <button type="submit" class="btn btn-success btn-sm" <?= $match['match_end'] ? 'disabled' : ''; ?>>+Goal</button>
                      </form>
                      <!-- <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="action_type" value="update_player_football_stats">
                        <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                        <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                        <input type="hidden" name="assist" value="+1">
                        <button type="submit" class="btn btn-info btn-sm">+Assist</button>
                      </form>
                      <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="action_type" value="update_player_football_stats">
                        <input type="hidden" name="player_id" value="<?= $player['user_id']; ?>">
                        <input type="hidden" name="team_id" value="<?= $player['team_id']; ?>">
                        <input type="hidden" name="save" value="+1">
                        <button type="submit" class="btn btn-warning btn-sm">+Save</button>
                      </form> -->
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  function submitDeleteForm(form) {
    var methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    form.appendChild(methodField);

    form.submit();
    return false; // Prevent default form submission and let the simulated DELETE method handle it.
  }
</script>

<?php include '../../../../components/shared/user-footer.php'; ?>