<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle news deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $news_id = $_GET['delete'];
    
    try {
        // Delete news article
        $delete_stmt = $conn->prepare("DELETE FROM news WHERE id = :news_id");
        $delete_stmt->bindParam(':news_id', $news_id, PDO::PARAM_INT);
        $delete_stmt->execute();
        
        $_SESSION['message'] = "News article deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting news article: " . $e->getMessage();
    }
    
    header("Location: news_management.php");
    exit();
}

// Handle news creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_news'])) {
    try {
        // Prepare file uploads
        $main_image = !empty($_FILES['main_image']['name']) ? $_FILES['main_image']['name'] : null;
        $image_1 = !empty($_FILES['image_1']['name']) ? $_FILES['image_1']['name'] : null;
        $image_2 = !empty($_FILES['image_2']['name']) ? $_FILES['image_2']['name'] : null;
        $image_3 = !empty($_FILES['image_3']['name']) ? $_FILES['image_3']['name'] : null;

        // Move uploaded files
        $upload_dir = 'uploads/news/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        // Move main image
        if ($main_image) {
            move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $main_image);
        }
        // Move additional images
        if ($image_1) {
            move_uploaded_file($_FILES['image_1']['tmp_name'], $upload_dir . $image_1);
        }
        if ($image_2) {
            move_uploaded_file($_FILES['image_2']['tmp_name'], $upload_dir . $image_2);
        }
        if ($image_3) {
            move_uploaded_file($_FILES['image_3']['tmp_name'], $upload_dir . $image_3);
        }

        // Insert news article
        $insert_stmt = $conn->prepare("
            INSERT INTO news (user_id, title, subtitle, description, main_image, image_1, image_2, image_3) 
            VALUES (:user_id, :title, :subtitle, :description, :main_image, :image_1, :image_2, :image_3)
        ");
        $insert_stmt->bindParam(':user_id', $_SESSION['admin_id'], PDO::PARAM_INT);
        $insert_stmt->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
        $insert_stmt->bindParam(':subtitle', $_POST['subtitle'], PDO::PARAM_STR);
        $insert_stmt->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
        $insert_stmt->bindParam(':main_image', $main_image, PDO::PARAM_STR);
        $insert_stmt->bindParam(':image_1', $image_1, PDO::PARAM_STR);
        $insert_stmt->bindParam(':image_2', $image_2, PDO::PARAM_STR);
        $insert_stmt->bindParam(':image_3', $image_3, PDO::PARAM_STR);
        $insert_stmt->execute();
        
        $_SESSION['message'] = "News article created successfully.";
        header("Location: news_management.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error creating news article: " . $e->getMessage();
    }
}

// Fetch news articles with username (most recent first)
try {
    $news_query = $conn->query("
        SELECT 
            n.id, 
            n.title, 
            n.subtitle,
            n.description, 
            n.main_image,
            n.image_1,
            n.image_2,
            n.image_3,
            n.created_at,
            c.full_name as posted_by
        FROM news n
        JOIN contact c ON n.user_id = c.id
        ORDER BY n.created_at DESC
    ");
    $news_articles = $news_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $news_articles = [];
    $_SESSION['error'] = "Error fetching news articles: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .news-card {
            transition: transform 0.3s;
        }
        .news-card:hover {
            transform: scale(1.02);
        }
        .truncate {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .news-image {
            max-height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'component/sidebar.php'; ?>
            
            <main class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">News Management</h1>
                </div>

                <?php 
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
                    unset($_SESSION['message']);
                }
                if (isset($_SESSION['error'])) {
                    echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                    unset($_SESSION['error']);
                }
                ?>

                <div class="row">
                    <?php if (empty($news_articles)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                No news articles found.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($news_articles as $article): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card news-card">
                                    <?php if ($article['main_image']): ?>
                                        <img src="/tourny_mate/uploads/news/<?php echo htmlspecialchars($article['main_image']); ?>" 
                                             class="card-img-top news-image" 
                                             alt="News Main Image">
                                    <?php endif; ?>
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <?php echo htmlspecialchars($article['title']); ?>
                                            </h5>
                                            <small class="text-muted">
                                                Posted by: <?php echo htmlspecialchars($article['posted_by']); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('d M Y, h:i A', strtotime($article['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($article['subtitle']): ?>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <?php echo htmlspecialchars($article['subtitle']); ?>
                                            </h6>
                                        <?php endif; ?>
                                        <p class="card-text truncate">
                                            <?php echo htmlspecialchars($article['description']); ?>
                                        </p>
                                        <div class="text-end">
                                            <a href="news_management.php?delete=<?php echo $article['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this news article?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>