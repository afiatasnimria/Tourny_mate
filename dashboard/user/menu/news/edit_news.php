<?php
// edit_news.php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
    exit();
}

$newsId = $_GET['news_id'] ?? null;
if (!$newsId) {
    header("Location: news.php");
    exit();
}

$errors = [];

// Fetch news details
$query = "SELECT * FROM news WHERE id = :news_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':news_id', $newsId, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    header("Location: news.php?error=not_found");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $description = $_POST['description'] ?? '';
    $mainImage = $_FILES['main_image']['name'] ?? $news['main_image'];
    $image1 = $_FILES['image_1']['name'] ?? $news['image_1'];
    $image2 = $_FILES['image_2']['name'] ?? $news['image_2'];
    $image3 = $_FILES['image_3']['name'] ?? $news['image_3'];

    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    if (empty($errors)) {
        try {
            $uploadDir = '../../../../uploads/news/';
            foreach (['main_image', 'image_1', 'image_2', 'image_3'] as $imageField) {
                if (!empty($_FILES[$imageField]['name'])) {
                    $targetFile = $uploadDir . basename($_FILES[$imageField]['name']);
                    move_uploaded_file($_FILES[$imageField]['tmp_name'], $targetFile);
                }
            }

            $query = "UPDATE news SET title = :title, subtitle = :subtitle, description = :description, main_image = :main_image, 
                      image_1 = :image_1, image_2 = :image_2, image_3 = :image_3 WHERE id = :news_id AND user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':subtitle', $subtitle);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':main_image', $mainImage);
            $stmt->bindParam(':image_1', $image1);
            $stmt->bindParam(':image_2', $image2);
            $stmt->bindParam(':image_3', $image3);
            $stmt->bindParam(':news_id', $newsId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: news.php?success=updated");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="fw-bold">Edit News</h3>
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
                            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" id="subtitle" class="form-control" value="<?php echo htmlspecialchars($news['subtitle']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($news['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="main_image" class="form-label">Main Image</label>
                            <input type="file" name="main_image" id="main_image" class="form-control">
                            <?php if (!empty($news['main_image'])): ?>
                                <img src="../../../../uploads/news/<?php echo htmlspecialchars($news['main_image']); ?>" alt="Current Image" class="img-fluid mt-2" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="image_1" class="form-label">Additional Image 1</label>
                            <input type="file" name="image_1" id="image_1" class="form-control">
                            <?php if (!empty($news['image_1'])): ?>
                                <img src="../../../../uploads/news/<?php echo htmlspecialchars($news['image_1']); ?>" alt="Additional Image 1" class="img-fluid mt-2" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="image_2" class="form-label">Additional Image 2</label>
                            <input type="file" name="image_2" id="image_2" class="form-control">
                            <?php if (!empty($news['image_2'])): ?>
                                <img src="../../../../uploads/news/<?php echo htmlspecialchars($news['image_2']); ?>" alt="Additional Image 2" class="img-fluid mt-2" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="image_3" class="form-label">Additional Image 3</label>
                            <input type="file" name="image_3" id="image_3" class="form-control">
                            <?php if (!empty($news['image_3'])): ?>
                                <img src="../../../../uploads/news/<?php echo htmlspecialchars($news['image_3']); ?>" alt="Additional Image 3" class="img-fluid mt-2" style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="news.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
