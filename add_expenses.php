<?php 
include("includes/head.php"); 
include("includes/navbar.php"); 
include("includes/sidebar.php"); 
include("includes/script.php"); 

// Initialize message variables
$successMessage = '';
$errorMessage = '';

// Check if the form is submitted
if (isset($_POST['category_id'], $_POST['amount'], $_POST['expense_date'])) {
    // Sanitize user input
    $userId = $_SESSION['user_id'];
    $categoryId = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_STRING); // You may want to use FILTER_VALIDATE_FLOAT for numeric input
    $expenseDate = $_POST['expense_date'];
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

    // Prepare and execute the insert query
    $query = "INSERT INTO expenses (user_id, category_id, amount, expense_date, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iisss", $userId, $categoryId, $amount, $expenseDate, $description);

    if ($stmt->execute()) {
        $successMessage = "Expense added successfully.";
    } else {
        $errorMessage = "Failed to add expense: " . $stmt->error;
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
                            <h4>Expenses</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Add Expense
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
                        <h4 class="text-center">Add New Expense</h4>

                        <!-- Show success or error message if exists -->
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($successMessage); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif ($errorMessage): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($errorMessage); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="add_expenses.php" method="POST">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category_id" class="form-control form-control-lg" id="category" required>
                                    <option value="">-- Select Category --</option>
                                    <!-- Dynamically fetch categories from DB -->
                                    <?php
                                    $userId = $_SESSION['user_id'];
                                    $query = "SELECT id, category_name FROM categories WHERE user_id = ?";
                                    $stmt = $mysqli->prepare($query);
                                    $stmt->bind_param("i", $userId);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="text" name="amount" class="form-control form-control-lg" id="amount" placeholder="Enter Amount" required>
                            </div>

                            <div class="form-group">
                                <label for="expense_date">Date</label>
                                <input type="date" name="expense_date" class="form-control form-control-lg" id="expense_date" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control form-control-lg" id="description" placeholder="Enter Description" rows="3"></textarea>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Add Expense</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
