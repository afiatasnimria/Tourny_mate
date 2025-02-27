<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
    exit();
}

// Fetch tournaments managed by the user
$query = "
    SELECT id, name, venue, start_date, end_date
    FROM tournament
    WHERE creator_id = :creator_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':creator_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>
        <div class="col-lg-9">
            <h2 class="text-center">Manage Tournaments</h2>
            <div class="d-flex justify-content-end mb-3">
                <a href="add_tournament.php" class="btn btn-success">Add Tournament</a>
            </div>

            <?php if (!empty($tournaments)): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Venue</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tournaments as $tournament): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tournament['name']); ?></td>
                                <td><?php echo htmlspecialchars($tournament['venue']); ?></td>
                                <td><?php echo htmlspecialchars($tournament['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($tournament['end_date']); ?></td>
                                <td>
                                    <a href="view_tournament.php?tournament_id=<?php echo $tournament['id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="edit_tournament.php?tournament_id=<?php echo $tournament['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete_tournament.php?tournament_id=<?php echo $tournament['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this tournament?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning text-center">You have not created any tournaments yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>