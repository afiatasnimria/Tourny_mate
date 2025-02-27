<?php
require_once '../../../../config.php';

session_start();
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

try {
    // Fetch the highlight to check ownership and retrieve the video file name
    $query = "SELECT video_file FROM highlights WHERE id = :highlight_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':highlight_id', $highlightId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $highlight = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$highlight) {
        // Highlight not found or does not belong to the user
        header("Location: highlights.php?error=notfound");
        exit();
    }

    // Delete the video file from the server
    $uploadDir = '../../../../uploads/videos/';
    $videoFile = $highlight['video_file'];
    if (!empty($videoFile) && file_exists($uploadDir . $videoFile)) {
        unlink($uploadDir . $videoFile);
    }

    // Delete the highlight from the database
    $query = "DELETE FROM highlights WHERE id = :highlight_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':highlight_id', $highlightId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: highlights.php?success=deleted");
    exit();
} catch (PDOException $e) {
    echo '<p class="alert alert-danger text-center">Error deleting highlight: ' . $e->getMessage() . '</p>';
    exit();
}
