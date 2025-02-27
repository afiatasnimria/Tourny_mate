<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config.php';
include './components/shared/general-header.php';

// Get the highlight ID from the URL
$highlight_id = isset($_GET['highlight_id']) ? (int) $_GET['highlight_id'] : 0;

try {
    // Fetch the full details of the selected highlight
    $query = "SELECT title, details, video_file, created_at FROM highlights WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $highlight_id]);
    $highlight = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching highlight: " . $e->getMessage();
}
?>
<!-- Highlight Section -->
<div class="container mt-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php elseif ($highlight): ?>
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <div class="video-container mb-4" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                    <video width="100%" height="400" controls style="border-radius: 8px;">
                        <source src="./uploads/videos/<?= htmlspecialchars($highlight['video_file']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <h1 style="font-size: 2rem; font-weight: bold; color: #333;"><?= htmlspecialchars($highlight['title']); ?></h1>
                <h4 style="font-size: 1.2rem; font-weight: 400; color: #555;"><?= nl2br(htmlspecialchars($highlight['details'])); ?></h4>
                <p class="text-muted mt-3" style="font-size: 0.9rem;">Published on: <?= htmlspecialchars(date('F j, Y', strtotime($highlight['created_at']))); ?></p>
            </div>

            <!-- Highlight Info -->
            <div class="col-md-4">
                <div style="background-color: #fff; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
                    <h4 style="font-size: 1.5rem; font-weight: bold; color: #333;">Highlight Info</h4>
                    <p class="text-secondary" style="margin-top: 1rem;">View and enjoy the featured highlight video. If you’d like to explore more, visit the highlights gallery.</p>
                    <a href="highlights.php" class="btn btn-primary btn-sm mt-3">Back to Highlights</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">Highlight not found.</p>
    <?php endif; ?>
</div>

<!-- Styles -->
<style>
    .video-container {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    h1 {
        font-size: 2.5rem;
        color: #333;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    h4 {
        font-size: 1.5rem;
        color: #555;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<?php include './components/shared/general-footer.php'; ?>
