<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
    exit();
}

$highlightId = $_GET['highlight_id'] ?? null;
if (!$highlightId) {
    header("Location: highlights.php");
    exit();
}

// Fetch the highlight details
$query = "SELECT * FROM highlights WHERE id = :highlight_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':highlight_id', $highlightId, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$highlight = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$highlight) {
    echo '<p class="alert alert-warning text-center">Highlight not found.</p>';
    include '../../../../components/shared/user-footer.php';
    exit();
}
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
                    <h3 class="fw-bold">View Highlight</h3>
                </div>
                <div class="card-body">
                    <h4 class="text-primary"><?php echo htmlspecialchars($highlight['title']); ?></h4>
                    <p class="text-secondary"><?php echo htmlspecialchars($highlight['details']); ?></p>
                    <?php if (!empty($highlight['video_file'])): ?>
                        <div class="text-center">
                            <video width="500" height="300" controls>
                                <source src="../../../../uploads/videos/<?php echo htmlspecialchars($highlight['video_file']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No video available for this highlight.</p>
                    <?php endif; ?>

                    <div class="mt-4 text-end">
                        <a href="highlights.php" class="btn btn-secondary">Back to Highlights</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
