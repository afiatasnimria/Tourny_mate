<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$teamId = $_GET['team_id'] ?? null;
$errors = [];

// Fetch team details
if ($teamId) {
    $query = "SELECT name, logo FROM team WHERE id = :team_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $stmt->execute();
    $team = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = $_POST['team_name'] ?? '';
    $logo = $team['logo'];

    // Validate input
    if (empty($teamName)) {
        $errors[] = "Team name is required.";
    }

    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $logoDir = '../../../../uploads/logos/';

        if (!is_dir($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $logoFile = $logoDir . basename($_FILES['logo']['name']);

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logoFile)) {
            $logo = basename($_FILES['logo']['name']);
        } else {
            $errors[] = "Failed to upload logo.";
        }
    }

    // Update team details
    if (empty($errors)) {
        $query = "UPDATE team SET name = :name, logo = :logo WHERE id = :team_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $teamName);
        $stmt->bindParam(':logo', $logo);
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: my_team.php?success=1");
        exit();
    }
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Team</h5>
                    <a href="my_team.php" class="btn btn-light btn-sm">Back to Team</a>
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
                            <label for="team_name" class="form-label">Team Name</label>
                            <input type="text" name="team_name" id="team_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($team['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Team Logo (optional)</label>
                            <input type="file" name="logo" id="logo" class="form-control">
                            <?php if ($team['logo']): ?>
                                <img src="../../../../uploads/logos/<?php echo htmlspecialchars($team['logo']); ?>" 
                                     alt="Current Logo" class="mt-2" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            <a href="my_team.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>
