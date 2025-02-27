<?php
// delete_news.php
require_once '../../../../config.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
$newsId = $_GET['news_id'] ?? null;

if (!$userId || !$newsId) {
    header("Location: news.php?error=invalid_request");
    exit();
}

try {
    // Fetch the news details to delete associated images
    $queryFetch = "SELECT main_image, image_1, image_2, image_3 FROM news WHERE id = :news_id AND user_id = :user_id";
    $stmtFetch = $conn->prepare($queryFetch);
    $stmtFetch->bindParam(':news_id', $newsId, PDO::PARAM_INT);
    $stmtFetch->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtFetch->execute();
    $news = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if (!$news) {
        header("Location: news.php?error=not_found");
        exit();
    }

    // Delete associated images
    $uploadDir = '../../../../uploads/news/';
    foreach (['main_image', 'image_1', 'image_2', 'image_3'] as $imageField) {
        if (!empty($news[$imageField])) {
            $filePath = $uploadDir . $news[$imageField];
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file
            }
        }
    }

    // Delete the news entry from the database
    $queryDelete = "DELETE FROM news WHERE id = :news_id AND user_id = :user_id";
    $stmtDelete = $conn->prepare($queryDelete);
    $stmtDelete->bindParam(':news_id', $newsId, PDO::PARAM_INT);
    $stmtDelete->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtDelete->execute();

    header("Location: news.php?success=deleted");
    exit();
} catch (PDOException $e) {
    error_log("Error deleting news: " . $e->getMessage());
    header("Location: news.php?error=db_error");
    exit();
}
?>
