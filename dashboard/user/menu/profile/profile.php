<?php
include '../../../../components/shared/user-header.php'; // Include the header

// Start session and include database connection
require_once '../../../../config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Define user uploads directory
$user_upload_dir = "../../../../uploads/user/";

// Create the user uploads directory if it doesn't exist
if (!is_dir($user_upload_dir)) {
    mkdir($user_upload_dir, 0755, true); // Create directory with proper permissions
}

// Fetch user information
try {
    $query = "SELECT * FROM userinfo WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Error fetching user profile: " . $e->getMessage());
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fullName = $_POST['fullName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        // Handle file uploads
        $dp = $user['dp']; // Default to current value
        $cover = $user['cover']; // Default to current value

        // Profile Picture Upload
        // Profile Picture Upload
        if (isset($_FILES['dp']) && $_FILES['dp']['error'] === UPLOAD_ERR_OK) {
            $dpFilename = uniqid() . "_" . basename($_FILES['dp']['name']);
            $dpFilePath = $user_upload_dir . $dpFilename;
            if (move_uploaded_file($_FILES['dp']['tmp_name'], $dpFilePath)) {
            $dp = $dpFilename;
            } else {
            $errors[] = "Failed to upload profile picture.";
            }
        }

        // Cover Image Upload
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $coverFilename = uniqid() . "_" . basename($_FILES['cover']['name']);
            $coverFilePath = $user_upload_dir . $coverFilename;
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $coverFilePath)) {
            $cover = $coverFilename;
            } else {
            $errors[] = "Failed to upload cover image.";
            }
        }

        // Update user information in the database
        $updateQuery = "UPDATE userinfo SET fullName = :fullName, email = :email, phone = :phone, dp = :dp, cover = :cover WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([
            'fullName' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'dp' => $dp,
            'cover' => $cover,
            'id' => $user_id,
        ]);

        // Redirect to profile page
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating profile: " . $e->getMessage());
    }
}
?>

<div class="container mt-5">
    <!-- Cover Image -->
    <div class="position-relative">
        <img src="/tourny_mate/uploads/user/<?= htmlspecialchars($user['cover'] ?: 'uploads/default-cover.jpg'); ?>" 
             alt="Cover Image" 
             class="img-fluid w-100 rounded" style="height: 300px; object-fit: cover;">
        <!-- Profile Picture -->
        <img src="/tourny_mate/uploads/user/<?= htmlspecialchars($user['dp'] ?: 'uploads/default-profile.png'); ?>" 
             alt="<?= htmlspecialchars($user['dp']); ?>" 
             class="rounded-circle position-absolute" 
             style="width: 150px; height: 150px; object-fit: cover; top: 250px; left: 50%; transform: translateX(-50%); border: 5px solid #fff;">
    </div>

    <!-- Profile Information -->
    <div class="text-center" style="margin-top: 120px;">
        <h1><?= htmlspecialchars($user['fullName']); ?></h1>
        <p class="text-muted"><?= htmlspecialchars($user['email']); ?> | <?= htmlspecialchars($user['phone']); ?></p>
    </div>

    <!-- Profile Update Form -->
    <div class="mt-5">
        <h3>Edit Profile</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" id="fullName" name="fullName" class="form-control" 
                       value="<?= htmlspecialchars($user['fullName']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" 
                       value="<?= htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="dp" class="form-label">Profile Picture</label>
                <input type="file" id="dp" name="dp" class="form-control">
            </div>
            <div class="mb-3">
                <label for="cover" class="form-label">Cover Image</label>
                <input type="file" id="cover" name="cover" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>

<?php
include '../../../../components/shared/user-footer.php'; // Include the footer
?>
