<?php 
ob_start(); // Start output buffering
include("includes/head.php"); 
include("includes/navbar.php"); 
include("includes/sidebar.php"); 
include("includes/script.php"); 

if (isset($_POST['category_name'])) {
    $userId = $_SESSION['user_id'];
    $categoryName = filter_var(trim($_POST['category_name']), FILTER_SANITIZE_STRING);

    // Check for existing category
    $checkQuery = "SELECT id FROM categories WHERE category_name = ? AND user_id = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("si", $categoryName, $userId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $errorMessage = "Category already exists.";
    } else {
        // Insert new category into the database
        $query = "INSERT INTO categories (category_name, user_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("si", $categoryName, $userId);

        // Execute and check for success
        if ($stmt->execute()) {
            $successMessage = "Category added successfully.";
            header("Location: categories.php"); // Redirect to categories page
            exit();
        } else {
            $errorMessage = "Failed to add category: " . $stmt->error;
        }
    }
}
ob_end_flush(); // Send output to the browser
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
                                <li class="breadcrumb-item active" aria-current="page">Add Categories</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pb-20"></div>
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Add Expense Category</h4>

                        <!-- Show success or error message if exists -->
                        <?php if (isset($successMessage)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $successMessage; ?>
                            </div>
                        <?php elseif (isset($errorMessage)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>

                        <form action="add_categories.php" method="POST">
                            <div class="input-group custom">
                                <input type="text" name="category_name" class="form-control form-control-lg" placeholder="Category Name" required>
                            </div>
                            <div class="input-group custom">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Add Category</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
