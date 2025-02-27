<?php
// Start session and include database connection
require_once '../../config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Define user uploads directory
$user_upload_dir = __DIR__."uploads/user/$user_id/";

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
        if (isset($_FILES['dp']) && $_FILES['dp']['error'] === UPLOAD_ERR_OK) {
            $dpFilename = uniqid() . "_" . basename($_FILES['dp']['name']);
            $dp = $user_upload_dir . $dpFilename;
            move_uploaded_file($_FILES['dp']['tmp_name'], $dp);
        }

        // Cover Image Upload
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $coverFilename = uniqid() . "_" . basename($_FILES['cover']['name']);
            $cover = $user_upload_dir . $coverFilename;
            move_uploaded_file($_FILES['cover']['tmp_name'], $cover);
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom Styles -->
    <style>
        .cover-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            position: absolute;
            top: 250px;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid #fff;
        }

        .profile-info {
            margin-top: 100px;
        }

        .form-section {
            margin-top: 2rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Cover Image -->
        <div class="cover-container position-relative">
            <img src="<?= htmlspecialchars($user['cover'] ?: 'uploads/default-cover.jpg'); ?>" 
                 alt="Cover Image" 
                 class="cover-image">
            <!-- Profile Picture -->
            <img src="<?= htmlspecialchars($user['dp'] ?: 'uploads/default-profile.png'); ?>" 
                 alt="Profile Picture" 
                 class="profile-image">
        </div>

        <!-- Profile Information -->
        <div class="profile-info text-center">
            <h1><?= htmlspecialchars($user['fullName']); ?></h1>
            <p><?= htmlspecialchars($user['email']); ?> | <?= htmlspecialchars($user['phone']); ?></p>
        </div>

        <!-- Profile Update Form -->
        <div class="form-section">
            <h3>Edit Profile</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" class="form-control" 
                           value="<?= htmlspecialchars($user['fullName']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="dp">Profile Picture</label>
                    <input type="file" id="dp" name="dp" class="form-control-file">
                </div>
                <div class="form-group">
                    <label for="cover">Cover Image</label>
                    <input type="file" id="cover" name="cover" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>
