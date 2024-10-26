<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");


$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category_id'];
    $budgetLimit = filter_var($_POST['budget_limit'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Validate budget limit
    if (!is_numeric($budgetLimit) || $budgetLimit <= 0) {
        $errorMessage = "Please enter a valid budget limit.";
    } elseif (strtotime($endDate) < strtotime($startDate)) {
        $errorMessage = "End date must be after start date.";
    } else {
        $query = "INSERT INTO budgets (category_id, user_id, budget_limit, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("iisss", $categoryId, $userId, $budgetLimit, $startDate, $endDate);

        if ($stmt->execute()) {
            $successMessage = "Budget set successfully.";
        } else {
            // Log the error for debugging
            error_log("Database error: " . $stmt->error);
            $errorMessage = "Failed to set budget. Please try again.";
        }
    }
}

// Fetch budget status and notifications
$notifications = [];
$currentDate = date('Y-m-d');
$notificationThreshold = 7; // days before the end date to trigger a notification

$budgetQuery = "SELECT b.category_id, c.category_name, IFNULL(SUM(e.amount), 0) as total_expense, 
                b.budget_limit, b.end_date 
                FROM budgets b 
                LEFT JOIN expenses e ON b.category_id = e.category_id 
                AND e.expense_date BETWEEN b.start_date AND b.end_date 
                JOIN categories c ON b.category_id = c.id 
                WHERE b.user_id = ? 
                GROUP BY b.category_id";

$budgetStmt = $mysqli->prepare($budgetQuery);
if ($budgetStmt) {
    $budgetStmt->bind_param("i", $userId);
    $budgetStmt->execute();
    $budgetResult = $budgetStmt->get_result();

    while ($row = $budgetResult->fetch_assoc()) {
        $totalExpense = $row['total_expense'];
        $budgetLimit = $row['budget_limit'];
        $endDate = $row['end_date'];

        // Check for exceeded budget
        if ($totalExpense > $budgetLimit) {
            $notifications[] = "Budget exceeded for category: " . htmlspecialchars($row['category_name']) . 
                               " (Spent: " . number_format($totalExpense, 2) . 
                               ", Limit: " . number_format($budgetLimit, 2) . ")";
        }

        // Check for low budget (e.g., 10% remaining)
        $remainingBudget = $budgetLimit - $totalExpense;
        if ($remainingBudget > 0 && ($remainingBudget / $budgetLimit) < 0.10) {
            $notifications[] = "Low budget for category: " . htmlspecialchars($row['category_name']) . 
                               " (Remaining: " . number_format($remainingBudget, 2) . ")";
        }

        // Check for near end date
        if ((strtotime($endDate) - strtotime($currentDate)) <= ($notificationThreshold * 86400)) { // 86400 seconds in a day
            $notifications[] = "Budget for category: " . htmlspecialchars($row['category_name']) . 
                               " is nearing its end date (End Date: " . htmlspecialchars($endDate) . ")";
        }
    }
    $budgetStmt->close();
} else {
    // Handle query preparation error
    error_log("Database error: " . $mysqli->error);
    echo "Error: Unable to fetch budget status.";
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
                            <h4>Budget</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Add Budget
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
                        <h4 class="text-center">Set Budget for Category</h4>

                        <!-- Show success or error message -->
                        <?php if (isset($successMessage)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $successMessage; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php elseif (isset($errorMessage)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $errorMessage; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="add_budget.php" method="POST">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category_id" class="form-control form-control-lg" id="category" required>
                                    <option value="">-- Select Category --</option>
                                    <!-- Dynamically fetch categories from DB -->
                                    <?php
                                    $query = "SELECT id, category_name FROM categories WHERE user_id = ?";
                                    $stmt = $mysqli->prepare($query);
                                    $stmt->bind_param("i", $userId);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="budget_limit">Budget Limit</label>
                                <input type="text" name="budget_limit" class="form-control form-control-lg" id="budget_limit" placeholder="Enter Budget Limit" required>
                            </div>

                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-lg" id="start_date" required>
                            </div>

                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-lg" id="end_date" required>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Set Budget</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
