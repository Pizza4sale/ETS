<?php 
include("includes/head.php"); 
include("includes/navbar.php"); 
include("includes/sidebar.php"); 
include("includes/script.php"); 

// Initialize message variables
$message = '';
$error = '';

// Fetch categories for the logged-in user
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM categories WHERE user_id = ?";
    $stmt = $mysqli->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "<div class='alert alert-danger'>Error preparing the statement: " . $mysqli->error . "</div>";
        exit();
    }
} else {
    // Redirect to login if user_id is not set
    header("Location: login.php");
    exit();
}

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
    header("Location: categories.php"); // Redirect to categories page
    exit();
}

// Display messages after redirection
if (isset($_SESSION['message'])) {
    $message = htmlspecialchars($_SESSION['message']);
    unset($_SESSION['message']);
} elseif (isset($_SESSION['error'])) {
    $error = htmlspecialchars($_SESSION['error']);
    unset($_SESSION['error']);
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
                            <h4>Categories</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Manage Categories
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
                        <h4 class="text-center">Categories</h4>

                        <!-- Display messages -->
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <table class="data-table table stripe hover nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                            <td>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No categories found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this category?")) {
            // Open a new PHP script to handle the deletion
            window.location.href = "delete.php?delete_id=" + id; // Redirect to delete script
        }
    }
</script>
