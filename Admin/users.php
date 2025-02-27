<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    try {
        // Start transaction to handle related records
        $conn->beginTransaction();

        // Delete related team_player entries
        $delete_team_players_stmt = $conn->prepare("DELETE FROM team_player WHERE user_id = :user_id");
        $delete_team_players_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_team_players_stmt->execute();

        // Delete related tournament_officials entries
        $delete_tournament_officials_stmt = $conn->prepare("DELETE FROM tournament_officials WHERE official_id = :user_id");
        $delete_tournament_officials_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_tournament_officials_stmt->execute();

        // Delete related tournament_team entries (indirect relation via team_player)
        $delete_tournament_teams_stmt = $conn->prepare("DELETE FROM tournament_team WHERE team_id IN (SELECT team_id FROM team_player WHERE user_id = :user_id)");
        $delete_tournament_teams_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_tournament_teams_stmt->execute();

        // Delete user
        $delete_user_stmt = $conn->prepare("DELETE FROM userinfo WHERE id = :user_id");
        $delete_user_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_user_stmt->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['message'] = "User deleted successfully.";
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
    
    header("Location: users.php");
    exit();
}

// Fetch users with tournament details
try {
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    if (!empty($search_term)) {
        $query = "
            SELECT 
                u.id, 
                u.fullName AS full_name,
                u.email,
                u.phone,
                (
                    SELECT COUNT(DISTINCT tt.tournament_id) 
                    FROM tournament_team tt 
                    JOIN team_player tp ON tt.team_id = tp.team_id
                    WHERE tp.user_id = u.id
                ) + (
                    SELECT COUNT(DISTINCT toff.tournament_id)
                    FROM tournament_officials toff
                    WHERE toff.official_id = u.id
                ) AS tournament_count
            FROM userinfo u
            WHERE u.fullName LIKE :search
            ORDER BY u.fullName
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':search', "%{$search_term}%", PDO::PARAM_STR);
    } else {
        $query = "
            SELECT 
                u.id, 
                u.fullName AS full_name,
                u.email,
                u.phone,
                (
                    SELECT COUNT(DISTINCT tt.tournament_id) 
                    FROM tournament_team tt 
                    JOIN team_player tp ON tt.team_id = tp.team_id
                    WHERE tp.user_id = u.id
                ) + (
                    SELECT COUNT(DISTINCT toff.tournament_id)
                    FROM tournament_officials toff
                    WHERE toff.official_id = u.id
                ) AS tournament_count
            FROM userinfo u
            ORDER BY u.fullName
        ";
        $stmt = $conn->prepare($query);
    }
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $_SESSION['error'] = "Error fetching users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .user-card {
            transition: transform 0.3s;
        }
        .user-card:hover {
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
                    <h1 class="h2">User Management</h1>
                    <form class="d-flex" action="users.php" method="get">
                        <input class="form-control me-2" type="search" name="search" 
                               placeholder="Search by name" 
                               value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
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

                <?php if (!empty($_GET['search'])): ?>
                    <div class="alert alert-info">
                        Search results for: <strong><?php echo htmlspecialchars($_GET['search']); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php if (empty($users)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                No users found.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card user-card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-1">
                                            <?php echo htmlspecialchars($user['full_name']); ?>
                                        </h5>
                                        <small>Email: <?php echo htmlspecialchars($user['email']); ?></small><br>
                                        <small>Phone: <?php echo htmlspecialchars($user['phone']); ?></small>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="badge bg-success text-white p-2">
                                                <i class="fas fa-trophy"></i> 
                                                <?php echo $user['tournament_count']; ?> Tournaments Joined
                                            </div>
                                            <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this user? This will remove all associated tournament data.');">
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