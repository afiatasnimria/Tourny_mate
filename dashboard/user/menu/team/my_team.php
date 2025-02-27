<?php
include '../../../../components/shared/user-header.php';

// Database Connection
require_once '../../../../config.php';

// Fetch the user's team
$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
    exit();
}

$query = "
    SELECT t.id AS team_id, t.name AS team_name, t.logo AS team_logo
    FROM team t
    WHERE t.manager_id = :user_id LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$team = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch team members if the team exists
$teamMembers = [];
if ($team) {
    $teamId = $team['team_id'];
    $queryMembers = "
        SELECT u.id AS user_id, u.fullName, tp.created_at
        FROM team_player tp
        JOIN userinfo u ON tp.user_id = u.id
        WHERE tp.team_id = :team_id";
    $stmtMembers = $conn->prepare($queryMembers);
    $stmtMembers->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmtMembers->execute();
    $teamMembers = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>
        <div class="col-lg-9">
            <h2 class="text-center mb-4">My Team</h2>

            <?php if ($team): ?>
                <!-- Team Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Team Details</span>
                        <a href="edit_team.php?team_id=<?php echo $team['team_id']; ?>" class="btn btn-light btn-sm">Edit Team</a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <?php if ($team['team_logo']): ?>
                                <img src="../../../../uploads/logos/<?php echo htmlspecialchars($team['team_logo']); ?>" 
                                     alt="Team Logo" 
                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;"
                                     class="me-3">
                            <?php endif; ?>
                            <div>
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($team['team_name']); ?></h5>
                                <p class="text-muted mb-0">Managed by You</p>
                            </div>
                        </div>
                        <p class="card-text"><strong>Team Members:</strong></p>
                        <ul class="list-group">
                            <?php if (!empty($teamMembers)): ?>
                                <?php foreach ($teamMembers as $member): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../player/details.php?player_id=<?php echo $member['user_id']; ?>">
                                            <?php echo htmlspecialchars($member['fullName']); ?>
                                        </a>
                                        <div>
                                            <span class="badge bg-secondary">
                                                Joined: <?php echo date('d M Y', strtotime($member['created_at'])); ?>
                                            </span>
                                            <a href="remove_member.php?team_id=<?php echo $teamId; ?>&user_id=<?php echo $member['user_id']; ?>" 
                                               class="btn btn-sm btn-danger ms-2"
                                               onclick="return confirm('Are you sure you want to remove this member?');">Remove</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No members added yet.</li>
                            <?php endif; ?>
                        </ul>
                        <div class="mt-4">
                            <a href="add_team_member.php?team_id=<?php echo $team['team_id']; ?>" class="btn btn-success me-2">Add Members</a>
                            <a href="edit_team.php?team_id=<?php echo $team['team_id']; ?>" class="btn btn-primary me-2">Edit Team</a>
                            <a href="delete_team.php?team_id=<?php echo $team['team_id']; ?>" 
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to delete this team? This action cannot be undone.');">Delete Team</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- No Team Message -->
                <div class="alert alert-warning text-center">
                    You don't have a team yet! Create one to start participating in tournaments.
                </div>
                <div class="text-center">
                    <a href="add_team.php" class="btn btn-success">Create Team</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
