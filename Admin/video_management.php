<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle video deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $video_id = $_GET['delete'];
    
    try {
        // First, get the video file to delete from filesystem
        $stmt = $conn->prepare("SELECT video_file FROM highlights WHERE id = :id");
        $stmt->bindParam(':id', $video_id, PDO::PARAM_INT);
        $stmt->execute();
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($video) {
            $video_path = 'uploads/' . $video['video_file'];
            
            // Delete from database
            $delete_stmt = $conn->prepare("DELETE FROM highlights WHERE id = :id");
            $delete_stmt->bindParam(':id', $video_id, PDO::PARAM_INT);
            $delete_stmt->execute();
            
            // Delete file from filesystem if it exists
            if (file_exists($video_path)) {
                unlink($video_path);
            }
            
            $_SESSION['message'] = "Video deleted successfully.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting video: " . $e->getMessage();
    }
    
    header("Location: video_management.php");
    exit();
}

// Fetch videos
try {
    $stmt = $conn->query("SELECT * FROM highlights ORDER BY created_at DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $videos = [];
    $_SESSION['error'] = "Error fetching videos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Video Management - Tournament Highlights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'component/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Video Management</h1>
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

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Video File</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos as $video): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($video['id']); ?></td>
                                <td><?php echo htmlspecialchars($video['title']); ?></td>
                                <td><?php echo htmlspecialchars($video['video_file']); ?></td>
                                <td><?php echo htmlspecialchars($video['created_at']); ?></td>
                                <td>
                                    <a href="video_management.php?delete=<?php echo $video['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this video?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>