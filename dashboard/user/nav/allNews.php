<?php
require_once '../../../config.php';
include '../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../login-form.php");
    exit();
}

// Fetch news created by the user
$query = "SELECT * FROM news ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include '../../../components/shared/dashboard-menu.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="fw-bold">All News</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Main Image</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($news)): ?>
                                <?php foreach ($news as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo htmlspecialchars($item['subtitle']); ?></td>
                                        <td>
                                            <?php if (!empty($item['main_image'])): ?>
                                                <img src="../../../uploads/news/<?php echo htmlspecialchars($item['main_image']); ?>" alt="News Image" style="width: 100px; height: auto;">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                                        <td>
                                            <a href="view_news.php?news_id=<?php echo $item['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No news found. Add your first news!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../components/shared/user-footer.php'; ?>
