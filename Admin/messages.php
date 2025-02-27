<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle message deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = $_GET['delete'];
    
    try {
        // Delete message
        $delete_stmt = $conn->prepare("DELETE FROM contact WHERE id = :message_id");
        $delete_stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
        $delete_stmt->execute();
        
        $_SESSION['message'] = "Message deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting message: " . $e->getMessage();
    }
    
    header("Location: messages.php");
    exit();
}

// Fetch messages (most recent first)
try {
    $messages_query = $conn->query("
        SELECT 
            id, 
            full_name, 
            email, 
            message, 
            created_at 
        FROM contact 
        ORDER BY created_at DESC
    ");
    $messages = $messages_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
    $_SESSION['error'] = "Error fetching messages: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .message-card {
            transition: transform 0.3s;
        }
        .message-card:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'component/sidebar.php'; ?>
            
            <main class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Messages</h1>
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
                    <?php if (empty($messages)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                No messages found.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card message-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <?php echo htmlspecialchars($msg['full_name']); ?>
                                            </h5>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($msg['email']); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <?php echo htmlspecialchars($msg['message']); ?>
                                        </p>
                                        <div class="text-end">
                                            <a href="messages.php?delete=<?php echo $msg['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this message?');">
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