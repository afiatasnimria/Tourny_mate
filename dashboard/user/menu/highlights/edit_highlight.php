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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $details = $_POST['details'] ?? '';
    $videoFile = $_FILES['video_file'] ?? null;

    // Validate inputs
    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    $fileName = $highlight['video_file']; // Keep the existing file by default
    if (!empty($videoFile['name'])) {
        if (!in_array($videoFile['type'], ['video/mp4', 'video/mov', 'video/avi'])) {
            $errors[] = "Invalid video format. Only MP4, MOV, and AVI files are allowed.";
        } else {
            // Upload the new video file
            $uploadDir = '../../../../uploads/videos/';
            $fileName = time() . '_' . basename($videoFile['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($videoFile['tmp_name'], $filePath)) {
                $errors[] = "Failed to upload the video file.";
            } else {
                // Delete the old video file
                if (!empty($highlight['video_file']) && file_exists($uploadDir . $highlight['video_file'])) {
                    unlink($uploadDir . $highlight['video_file']);
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            // Update the highlight in the database
            $query = "
                UPDATE highlights
                SET title = :title, details = :details, video_file = :video_file
                WHERE id = :highlight_id AND user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':video_file', $fileName);
            $stmt->bindParam(':highlight_id', $highlightId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: highlights.php?success=1");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
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
                    <h3 class="fw-bold">Edit Highlight</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($highlight['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">details</label>
                            <input type="text" name="details" id="details" class="form-control" value="<?php echo htmlspecialchars($highlight['details']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="video_file" class="form-label">Video File</label>
                            <input type="file" name="video_file" id="video_file" class="form-control" accept="video/mp4, video/mov, video/avi">
                            <small class="text-muted">Leave blank to keep the existing video file.</small>
                            <?php if (!empty($highlight['video_file'])): ?>
                                <div class="mt-3">
                                    <video width="250" height="150" controls>
                                        <source src="../../../../uploads/videos/<?php echo htmlspecialchars($highlight['video_file']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Update Highlight</button>
                            <a href="highlights.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
