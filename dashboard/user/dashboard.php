<?php
require_once '../../config.php';
include '../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];

// Fetch statistics
$query = "
        SELECT 
            (SELECT COUNT(*) FROM match_played WHERE official_1_id = :user_id1 OR official_2_id = :user_id2 OR official_3_id = :user_id3) AS officiated_matches,
            (SELECT COUNT(*) FROM team_player tp JOIN match_played mp ON tp.team_id = mp.team_1_id OR tp.team_id = mp.team_2_id WHERE tp.user_id = :user_id4) AS matches_played,
            (SELECT COUNT(*) FROM team WHERE manager_id = :user_id5) AS managed_teams";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id1', $userId, PDO::PARAM_INT);
$stmt->bindParam(':user_id2', $userId, PDO::PARAM_INT);
$stmt->bindParam(':user_id3', $userId, PDO::PARAM_INT);
$stmt->bindParam(':user_id4', $userId, PDO::PARAM_INT);
$stmt->bindParam(':user_id5', $userId, PDO::PARAM_INT);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch upcoming matches
$queryMatches = "
        SELECT mp.id AS match_id, t1.name AS team_1_name, t2.name AS team_2_name, mp.match_day, 
            CASE 
                WHEN mp.official_1_id = :user_id6 OR mp.official_2_id = :user_id7 OR mp.official_3_id = :user_id8 THEN 'Official'
                WHEN tp.user_id = :user_id9 THEN 'Player'
                ELSE 'Other'
            END AS role
        FROM match_played mp
        LEFT JOIN team t1 ON mp.team_1_id = t1.id
        LEFT JOIN team t2 ON mp.team_2_id = t2.id
        LEFT JOIN team_player tp ON (tp.team_id = mp.team_1_id OR tp.team_id = mp.team_2_id)
        WHERE (mp.official_1_id = :user_id10 OR mp.official_2_id = :user_id11 OR mp.official_3_id = :user_id12 OR tp.user_id = :user_id13)
        ORDER BY mp.match_day ASC";
$stmtMatches = $conn->prepare($queryMatches);
$stmtMatches->bindParam(':user_id6', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id7', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id8', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id9', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id10', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id11', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id12', $userId, PDO::PARAM_INT);
$stmtMatches->bindParam(':user_id13', $userId, PDO::PARAM_INT);
$stmtMatches->execute();
$matches = $stmtMatches->fetchAll(PDO::FETCH_ASSOC);
// Fetch tournament requests
$queryRequests = "
        SELECT tr.id AS request_id, tr.tournament_id, tr.team_id, tr.status, t.name AS tournament_name, tm.name AS team_name
        FROM tournament_request tr
        JOIN tournament t ON tr.tournament_id = t.id
        JOIN team tm ON tr.team_id = tm.id
        WHERE tr.status = 'pending' AND tr.user_id = :user_id
        ORDER BY tr.created_at DESC";
$stmtRequests = $conn->prepare($queryRequests);
$stmtRequests->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmtRequests->execute();
$requests = $stmtRequests->fetchAll(PDO::FETCH_ASSOC);

// Handle request actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_action'])) {
    $requestId = $_POST['request_id'];
    $action = $_POST['request_action'];

    if ($action === 'accept') {
        // Approve the request and add to tournament_team
        $queryApprove = "
            UPDATE tournament_request
            SET status = 'approved'
            WHERE id = :request_id";
        $stmtApprove = $conn->prepare($queryApprove);
        $stmtApprove->bindParam(':request_id', $requestId, PDO::PARAM_INT);
        $stmtApprove->execute();

        // Fetch the tournament_id and team_id for the request
        $queryFetch = "SELECT tournament_id, team_id FROM tournament_request WHERE id = :request_id";
        $stmtFetch = $conn->prepare($queryFetch);
        $stmtFetch->bindParam(':request_id', $requestId, PDO::PARAM_INT);
        $stmtFetch->execute();
        $request = $stmtFetch->fetch(PDO::FETCH_ASSOC);

        // Add to tournament_team
        $queryAddTeam = "INSERT INTO tournament_team (tournament_id, team_id) VALUES (:tournament_id, :team_id)";
        $stmtAddTeam = $conn->prepare($queryAddTeam);
        $stmtAddTeam->bindParam(':tournament_id', $request['tournament_id'], PDO::PARAM_INT);
        $stmtAddTeam->bindParam(':team_id', $request['team_id'], PDO::PARAM_INT);
        $stmtAddTeam->execute();
    } elseif ($action === 'reject') {
        // Reject the request
        $queryReject = "
            UPDATE tournament_request
            SET status = 'rejected'
            WHERE id = :request_id";
        $stmtReject = $conn->prepare($queryReject);
        $stmtReject->bindParam(':request_id', $requestId, PDO::PARAM_INT);
        $stmtReject->execute();
    }

    // Refresh the page
    header("Location: dashboard.php");
    exit();
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include '../../components/shared/dashboard-menu.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="container mt-4">
                <!-- Statistics Section -->
                <section id="statistics" class="mb-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="fw-bold">Your Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h3 class="text-success fw-bold"><?php echo htmlspecialchars($stats['matches_played'] ?? 0); ?></h3>
                                    <p>Matches as a Player</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-warning fw-bold"><?php echo htmlspecialchars($stats['managed_teams'] ?? 0); ?></h3>
                                    <p>Matches as a Manager</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-info fw-bold"><?php echo htmlspecialchars($stats['officiated_matches'] ?? 0); ?></h3>
                                    <p>Matches as a Official</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- My Matches Section -->
                <section id="matches" class="mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="fw-bold">Your Upcoming Matches</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Here are the matches where you're participating as a team member or official:</p>
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Match</th>
                                        <th>Date</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($matches)): ?>
                                        <?php foreach ($matches as $match): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($match['team_1_name'] . ' vs ' . $match['team_2_name']); ?></td>
                                                <td><?php echo htmlspecialchars($match['match_day']); ?></td>
                                                <td><?php echo htmlspecialchars($match['role']); ?></td>
                                                <td>
                                                    <a href="menu/tournament/view_match.php?match_id=<?php echo $match['match_id']; ?>" class="btn btn-primary btn-sm">View Match</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No upcoming matches found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Tournament Requests Section -->
                <section id="tournament-requests" class="mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="fw-bold">Tournament Requests</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Review and manage recent tournament requests:</p>
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tournament</th>
                                        <th>Team</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($requests)): ?>
                                        <?php foreach ($requests as $request): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($request['tournament_name']); ?></td>
                                                <td><?php echo htmlspecialchars($request['team_name']); ?></td>
                                                <td><?php echo htmlspecialchars($request['status']); ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline-block">
                                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                                        <button type="submit" name="request_action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                                    </form>
                                                    <form method="POST" class="d-inline-block">
                                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                                        <button type="submit" name="request_action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No tournament requests found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Quick Links Section -->
                <section id="quick-links" class="mb-5">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-lg">
                                <div class="card-header bg-success text-white text-center fw-bold">
                                    Manage Team
                                </div>
                                <div class="card-body text-center">
                                    <p>View your team, add players, or create a new team to get started.</p>
                                    <a href="<?php echo BASE_URL; ?>dashboard/user/menu/team/my_team.php" class="btn btn-success w-100">Go to My Team</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-lg">
                                <div class="card-header bg-primary text-white text-center fw-bold">
                                    Tournaments
                                </div>
                                <div class="card-body text-center">
                                    <p>Organize, join, or manage tournaments. Stay ahead in the game.</p>
                                    <a href="<?php echo BASE_URL; ?>dashboard/user/menu/tournament/tournament.php" class="btn btn-primary w-100">Explore Tournaments</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-lg">
                                <div class="card-header bg-secondary text-white text-center fw-bold">
                                    News & Updates
                                </div>
                                <div class="card-body text-center">
                                    <p>Stay informed with the latest sports news, blogs, and updates.</p>
                                    <a href="<?php echo BASE_URL; ?>dashboard/user/nav/allNews.php" class="btn btn-secondary w-100">Read News</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<?php include '../../components/shared/user-footer.php'; ?>