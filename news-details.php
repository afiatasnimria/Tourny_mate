<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config.php';

include './components/shared/general-header.php';

// Get the news ID from the URL
$news_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

try {
    // Fetch the full details of the selected news
    $query = "SELECT title, subtitle, description, main_image, image_1, image_2, image_3 FROM news WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $news_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $news = $result[0] ?? null; // Get the first result or null if not found
} catch (PDOException $e) {
    $error = "Error fetching news: " . $e->getMessage();
}
?>
<!-- News Section -->
<div class="container mt-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php elseif ($news): ?>
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <img src="./uploads/news/<?= htmlspecialchars($news['main_image']); ?>" alt="<?= htmlspecialchars($news['title']); ?>"
                    style="width: 100%; margin-bottom: 2rem;">
                <h1><?= htmlspecialchars($news['title']); ?></h1>
                <h4><?= htmlspecialchars($news['subtitle']); ?></h4>
                <p style="margin-top: 1.5rem; line-height: 1.8;"><?= nl2br(htmlspecialchars($news['description'])); ?></p>
            </div>

            <!-- Additional Images -->
            <div class="col-md-4">
                <h4>Additional Images</h4>
                <?php if (!empty($news['image_1'])): ?>
                    <img src="./uploads/news/<?= htmlspecialchars($news['image_1']); ?>" alt="Additional Image 1" style="width: 100%; margin-bottom: 1rem;">
                <?php endif; ?>
                <?php if (!empty($news['image_2'])): ?>
                    <img src="./uploads/news/<?= htmlspecialchars($news['image_2']); ?>" alt="Additional Image 2" style="width: 100%; margin-bottom: 1rem;">
                <?php endif; ?>
                <?php if (!empty($news['image_3'])): ?>
                    <img src="./uploads/news/<?= htmlspecialchars($news['image_3']); ?>" alt="Additional Image 3" style="width: 100%; margin-bottom: 1rem;">
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">News not found.</p>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script>
    // Hide the loader once the page is fully loaded
    window.addEventListener("load", function() {
        document.getElementById("preloader").style.display = "none";
    });
</script>

<?php include './components/shared/general-footer.php'; ?>