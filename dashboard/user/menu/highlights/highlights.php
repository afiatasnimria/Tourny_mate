<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
    exit();
}

// Fetch highlights created by the user
$query = "SELECT * FROM highlights WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$highlights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="fw-bold">Manage Highlights</h3>
                </div>
                <div class="card-body">
                    <div class="text-end mb-3">
                        <a href="add_highlight.php" class="btn btn-success">Add Highlight</a>
                    </div>
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Title</th>
                                <th>Details</th>
                                <th>Video</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($highlights)): ?>
                                <?php foreach ($highlights as $highlight): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($highlight['title']); ?></td>
                                        <td><?php echo htmlspecialchars($highlight['details']); ?></td>
                                        <td>
                                            <?php if (!empty($highlight['video_file'])): ?>
                                                <video width="150" height="100" controls>
                                                    <source src="../../../../uploads/videos/<?php echo htmlspecialchars($highlight['video_file']); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            <?php else: ?>
                                                <span class="text-muted">No Video</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($highlight['created_at']); ?></td>
                                        <td>
                                            <a href="view_highlight.php?highlight_id=<?php echo $highlight['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                            <a href="edit_highlight.php?highlight_id=<?php echo $highlight['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <a href="delete_highlight.php?highlight_id=<?php echo $highlight['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this highlight?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No highlights found. Add your first highlight!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
