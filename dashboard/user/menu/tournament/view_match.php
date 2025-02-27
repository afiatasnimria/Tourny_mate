<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$matchId = $_GET['match_id'] ?? null;

if (!$matchId) {
    header("Location: official_tournament_list.php");
    exit();
}

// Fetch match details, including team logos
$query = "
    SELECT mp.match_day, mp.match_type, 
           t1.id AS team_1_id, t1.name AS team_1_name, t1.logo AS team_1_logo, 
           t2.id AS team_2_id, t2.name AS team_2_name, t2.logo AS team_2_logo
    FROM match_played mp
    JOIN team t1 ON mp.team_1_id = t1.id
    JOIN team t2 ON mp.team_2_id = t2.id
    WHERE mp.id = :match_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':match_id', $matchId, PDO::PARAM_INT);
$stmt->execute();
$matchDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$matchDetails) {
    echo '<p class="alert alert-warning text-center">Match details not found.</p>';
    include '../../../../components/shared/user-footer.php';
    exit();
}

// Fetch players for both teams
$query = "
    SELECT tp.team_id, u.fullName AS player_name, u.role 
    FROM team_player tp
    JOIN userinfo u ON tp.user_id = u.id
    WHERE tp.team_id IN (:team_1_id, :team_2_id)
    ORDER BY tp.team_id, u.fullName";
$stmt = $conn->prepare($query);
$stmt->bindParam(':team_1_id', $matchDetails['team_1_id'], PDO::PARAM_INT);
$stmt->bindParam(':team_2_id', $matchDetails['team_2_id'], PDO::PARAM_INT);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group players by team
$playersByTeam = [
    $matchDetails['team_1_id'] => [],
    $matchDetails['team_2_id'] => []
];
foreach ($players as $player) {
    $playersByTeam[$player['team_id']][] = $player;
}
?>

<div class="container-fluid mt-5">
<div class="row">
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>
        <div class="col-lg-9">
    <h2 class="text-center mb-4">Match Details</h2>
    <div class="row justify-content-center">
        <!-- Team 1 Details -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body text-center">
                    <img src="/tourny_mate/uploads/logos/<?= htmlspecialchars($matchDetails['team_1_logo']); ?>" 
                         alt="<?= htmlspecialchars($matchDetails['team_1_name']); ?> Logo" 
                         class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px;">
                    <h4 class="card-title text-primary"><?= htmlspecialchars($matchDetails['team_1_name']); ?></h4>
                    <h5 class="text-secondary">Players:</h5>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($playersByTeam[$matchDetails['team_1_id']] as $player): ?>
                            <li class="list-group-item"><?= htmlspecialchars($player['player_name']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Team 2 Details -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body text-center">
                    <img src="/tourny_mate/uploads/logos/<?= htmlspecialchars($matchDetails['team_2_logo']); ?>" 
                         alt="<?= htmlspecialchars($matchDetails['team_2_name']); ?> Logo" 
                         class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px;">
                    <h4 class="card-title text-primary"><?= htmlspecialchars($matchDetails['team_2_name']); ?></h4>
                    <h5 class="text-secondary">Players:</h5>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($playersByTeam[$matchDetails['team_2_id']] as $player): ?>
                            <li class="list-group-item"><?= htmlspecialchars($player['player_name']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">
    <div class="text-center">
        <p class="text-secondary">Match Date: <?= htmlspecialchars($matchDetails['match_day']); ?></p>
        <p class="text-secondary">Match Type: <?= htmlspecialchars($matchDetails['match_type']); ?></p>
    </div>
</div>

</div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
