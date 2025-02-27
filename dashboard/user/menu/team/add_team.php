<?php
require_once '../../../../config.php'; // Include the database configuration
include '../../../../components/shared/user-header.php'; // Include the header

// Initialize variables
$teamName = $logo = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_name'])) {
    // Get the form data
    $teamName = $_POST['team_name'] ?? '';
    $managerId = $_SESSION['user_id'];
    $userIds = $_POST['user_ids'] ?? [];

    // Validate input
    if (empty($teamName)) {
        $errors[] = "Team name is required.";
    }

    if (empty($userIds)) {
        $errors[] = "At least one user must be selected.";
    }

    // Handle file upload for the logo (optional)
    if (!empty($_FILES['logo']['name'])) {
        $logoDir = '../../../../uploads/logos/';

        // Check if the directory exists; if not, create it
        if (!is_dir($logoDir)) {
            mkdir($logoDir, 0777, true); // Create directory with full permissions
        }
        $logoFile = $logoDir . basename($_FILES['logo']['name']);

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logoFile)) {
            $logo = basename($_FILES['logo']['name']);
        } else {
            $errors[] = "Failed to upload logo.";
        }
    }

    // If no errors, insert the team and players into the database
    if (empty($errors)) {
        try {
            // Insert into the team table
            $query = "INSERT INTO team (name, manager_id, logo) VALUES (:name, :manager_id, :logo)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $teamName);
            $stmt->bindParam(':manager_id', $managerId, PDO::PARAM_INT);
            $stmt->bindParam(':logo', $logo);
            $stmt->execute();

            $teamId = $conn->lastInsertId(); // Get the last inserted team ID

            // Insert team members into team_player table
            foreach ($userIds as $userId) {
                $query = "INSERT INTO team_player (team_id, user_id, created_at) VALUES (:team_id, :user_id, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Success message and redirect
            header("Location: my_team.php?success=1");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch users for the search functionality and default list
$users = [];
if (!empty($_GET['search_user'])) {
    $searchTerm = '%' . $_GET['search_user'] . '%';
    $query = "SELECT id, fullName, email FROM userinfo WHERE fullName LIKE :searchTerm LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':searchTerm', $searchTerm);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $query = "SELECT id, fullName, email FROM userinfo LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>
        <div class="col-lg-9">
            <h2>Create a New Team</h2>

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
                    <label for="team_name" class="form-label">Team Name</label>
                    <input type="text" name="team_name" id="team_name" class="form-control" value="<?php echo htmlspecialchars($teamName); ?>">
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label">Team Logo (optional)</label>
                    <input type="file" name="logo" id="logo" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="search_user" class="form-label">Search Users</label>
                    <div class="input-group">
                        <input type="text" name="search_user" id="search_user" class="form-control" placeholder="Search by full name" value="<?php echo htmlspecialchars($_GET['search_user'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-secondary" name="search">Search</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="user_ids" class="form-label">Select Users</label>
                    <div>
                        <?php foreach ($users as $user): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="user_ids[]" value="<?php echo htmlspecialchars($user['id']); ?>" id="user_<?php echo htmlspecialchars($user['id']); ?>" <?php echo (isset($_POST['user_ids']) && in_array($user['id'], $userIds)) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="user_<?php echo htmlspecialchars($user['id']); ?>">
                                    <?php echo htmlspecialchars($user['fullName']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Team</button>
            </form>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
