<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
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
    if (empty($videoFile['name'])) {
        $errors[] = "A video file is required.";
    } elseif (!in_array($videoFile['type'], ['video/mp4', 'video/mov', 'video/avi'])) {
        $errors[] = "Invalid video format. Only MP4, MOV, and AVI files are allowed.";
    }

    if (empty($errors)) {
        try {
            // Upload the video file
            $uploadDir = '../../../../uploads/videos/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($videoFile['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($videoFile['tmp_name'], $filePath)) {
                $errors[] = "Failed to upload the video file.";
            } else {
                // Insert highlight into the database
                $query = "
                    INSERT INTO highlights (user_id, title, details, video_file)
                    VALUES (:user_id, :title, :details, :video_file)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':details', $details);
                $stmt->bindParam(':video_file', $fileName);
                $stmt->execute();

                header("Location: highlights.php?success=1");
                exit();
            }
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
                    <h3 class="fw-bold">Add Highlight</h3>
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
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">Details</label>
                            <textarea name="details" id="details" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="video_file" class="form-label">Video File</label>
                            <input type="file" name="video_file" id="video_file" class="form-control" accept="video/mp4, video/mov, video/avi" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Add Highlight</button>
                            <a href="highlights.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
