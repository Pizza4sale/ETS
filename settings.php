<?php
// Start output buffering
ob_start();

include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

// Initialize alert variables
$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    // Check if all fields are filled
    if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
        $alertMessage = "All fields are required.";
        $alertType = "danger";
    } else {
        // Fetch the current hashed password from the database
        $query = "SELECT password FROM users WHERE id = ?";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            $stmt->close();

            // Verify current password
            if (password_verify($currentPassword, $hashedPassword)) {
                if ($newPassword === $confirmNewPassword) {
                    // Hash the new password
                    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $query = "UPDATE users SET password = ? WHERE id = ?";
                    if ($stmt = $mysqli->prepare($query)) {
                        $stmt->bind_param("si", $newPasswordHash, $userId);
                        if ($stmt->execute()) {
                            $alertMessage = "Password changed successfully!";
                            $alertType = "success";
                            // Redirect back to settings page after 2 seconds
                            header("Refresh:2; url=settings.php");
                            exit;
                        } else {
                            $alertMessage = "Failed to update password in the database.";
                            $alertType = "danger";
                        }
                        $stmt->close();
                    } else {
                        $alertMessage = "Failed to prepare the update statement.";
                        $alertType = "danger";
                    }
                } else {
                    $alertMessage = "New passwords do not match.";
                    $alertType = "danger";
                }
            } else {
                $alertMessage = "Current password is incorrect.";
                $alertType = "danger";
            }
        } else {
            $alertMessage = "Failed to prepare the select statement.";
            $alertType = "danger";
        }
    }
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
									<h4>Settings</h4>
								</div>
								<nav aria-label="breadcrumb" role="navigation">
									<ol class="breadcrumb">
										<li class="breadcrumb-item">
											<a href="index.php">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">
                                       Settings
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
                <h4 class="text-center mb-4">Change Password</h4>
                
                <!-- Display Alert -->
                <?php if (!empty($alertMessage)): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($alertType); ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($alertMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Change Password Form -->
                <form action="settings.php" method="POST">
                    <div class="form-group mb-3">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control form-control-lg" placeholder="Current Password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control form-control-lg" placeholder="New Password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirm_new_password">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control form-control-lg" placeholder="Confirm New Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>

<?php
// End output buffering and flush output
ob_end_flush();
?>
