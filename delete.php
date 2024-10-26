<?php 
include("includes/head.php"); 

// Initialize message variables
$message = '';
$error = '';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Handle category deletion
    if (isset($_GET['delete_id'])) {
        $deleteId = filter_var($_GET['delete_id'], FILTER_VALIDATE_INT); // Validate as integer
        if ($deleteId) {
            $deleteQuery = "DELETE FROM categories WHERE id = ? AND user_id = ?";
            $deleteStmt = $mysqli->prepare($deleteQuery);
            
            if ($deleteStmt) {
                $deleteStmt->bind_param("ii", $deleteId, $userId);
                if ($deleteStmt->execute()) {
                    $_SESSION['message'] = "Category deleted successfully!";
                } else {
                    $_SESSION['error'] = "Error deleting category: " . $deleteStmt->error;
                }
            } else {
                $_SESSION['error'] = "Failed to prepare delete statement.";
            }
        } else {
            $_SESSION['error'] = "Invalid category ID.";
        }
    }

    // Redirect to categories page after deletion attempt
    header("Location: categories.php"); 
    exit();
} else {
    // Redirect to login if user_id is not set
    header("Location: login.php");
    exit();
}
