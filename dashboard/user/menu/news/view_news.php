<?php
// view_news.php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$newsId = $_GET['news_id'] ?? null;
if (!$newsId) {
    header("Location: news.php");
    exit();
}

// Fetch news details
$query = "SELECT n.*, u.fullName FROM news n JOIN userinfo u ON n.user_id = u.id WHERE n.id = :news_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':news_id', $newsId, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    header("Location: news.php?error=not_found");
    exit();
}
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="fw-bold"><?php echo htmlspecialchars($news['title']); ?></h2>
            <p class="mb-0">By <?php echo htmlspecialchars($news['fullName']); ?> | <?php echo htmlspecialchars(date('F j, Y', strtotime($news['created_at']))); ?></p>
        </div>
        <div class="card-body">
            <!-- Main Image -->
            <?php if (!empty($news['main_image'])): ?>
                <div class="text-center mb-4">
                    <img src="../../../../uploads/news/<?php echo htmlspecialchars($news['main_image']); ?>" 
                         alt="Main Image" class="img-fluid rounded shadow" style="max-height: 400px; max-width: 100%;">
                </div>
            <?php endif; ?>

            <!-- Subtitle -->
            <?php if (!empty($news['subtitle'])): ?>
                <h4 class="text-secondary text-center mb-4"><?php echo htmlspecialchars($news['subtitle']); ?></h4>
            <?php endif; ?>

            <!-- Description -->
            <?php if (!empty($news['description'])): ?>
                <p class="text-justify" style="line-height: 1.8; font-size: 1.1rem;">
                    <?php echo nl2br(htmlspecialchars($news['description'])); ?>
                </p>
            <?php endif; ?>

            <!-- Image Gallery -->
            <div class="row g-4">
                <?php foreach (['image_1', 'image_2', 'image_3'] as $imageField): ?>
                    <?php if (!empty($news[$imageField])): ?>
                        <div class="col-md-4">
                            <div class="card shadow border-0">
                                <img src="../../../../uploads/news/<?php echo htmlspecialchars($news[$imageField]); ?>" 
                                     alt="Additional Image" class="img-fluid rounded">
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Footer Buttons -->
            <div class="text-center mt-5">
                <a href="news.php" class="btn btn-secondary btn-lg shadow">Back to News</a>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
