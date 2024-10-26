<?php
// Start the session and enable error reporting


include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $profilePicture = $_FILES['profile_picture'];

    $uploadDir = 'uploads/';
    $fileName = null;

    // Handle profile picture upload
    if ($profilePicture['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($profilePicture['tmp_name']);
        
        if (in_array($fileType, $allowedTypes) && $profilePicture['size'] <= 2 * 1024 * 1024) { // 2MB limit
            $fileName = time() . '-' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', basename($profilePicture['name']));
            $uploadFile = $uploadDir . $fileName;

            if (!move_uploaded_file($profilePicture['tmp_name'], $uploadFile)) {
                $alertMessage = "Failed to upload profile picture.";
                $alertType = "danger";
            }
        } else {
            $alertMessage = "Invalid file type or size exceeds 2MB.";
            $alertType = "danger";
        }
    }

    // Prepare the update query if no errors
    if (empty($alertMessage)) {
        if ($fileName) {
            $query = "UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssi", $username, $email, $fileName, $userId);
        } else {
            $query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssi", $username, $email, $userId);
        }

        // Execute and check for success
        if ($stmt && $stmt->execute()) {
            $alertMessage = "Profile updated successfully!";
            $alertType = "success";
            header("Refresh:2; url=profile.php");
        } else {
            $alertMessage = "Failed to update profile. Error: " . $stmt->error;
            $alertType = "danger";
        }
    }
}

// Fetch user data
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>





<!-- Main content -->
<div class="main-container">
			<div class="pd-ltr-20 xs-pd-20-10">
				<div class="min-height-200px">
					<div class="page-header">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="title">
									<h4>Profile</h4>
								</div>
								<nav aria-label="breadcrumb" role="navigation">
									<ol class="breadcrumb">
										<li class="breadcrumb-item">
											<a href="index.php">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">
                                       Profile
										</li>
									</ol>
								</nav>
							</div>
                        </div>

					</div>

          <div class="card-box mb-30">
						
						<div class="pb-20"></div>
        <div class="pd-ltr-20">
            <div class="card-box pd-20 box-shadow">
                <h4 class="text-center mb-4">Update Your Profile</h4>

                <!-- Display Alert -->
                <?php if ($alertMessage): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($alertType); ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($alertMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Display Profile Picture -->
                <div class="text-center mb-4">
    <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" alt="Profile Picture" class="profile-pic img-fluid rounded-circle" style="max-width: 150px;">
</div>


                <!-- Profile Update Form -->
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control form-control-lg" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control form-control-lg" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control form-control-lg">
                        <small class="form-text text-muted">Allowed file types: jpg, jpeg, png. Max size: 2MB.</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
<style>
.profile-pic {
    width: 150px; /* Set desired width */
    height: 150px; /* Set desired height */
    border-radius: 50%; /* Make the image circular */
    object-fit: cover; /* Ensure the image covers the container without distortion */
}
</style>
