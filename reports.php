<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get date range for reports
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get expense summary for the selected date range
$query = "SELECT c.category_name, SUM(e.amount) AS total_amount
          FROM expenses e
          JOIN categories c ON e.category_id = c.id
          WHERE e.user_id = ? AND e.expense_date BETWEEN ? AND ?
          GROUP BY c.category_name";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iss", $userId, $startDate, $endDate);
$stmt->execute();
$expenseSummary = $stmt->get_result();

// Get total budget for each category
$query = "SELECT c.category_name, SUM(b.budget_limit) AS total_budget
          FROM budgets b
          JOIN categories c ON b.category_id = c.id
          WHERE b.user_id = ? AND b.start_date <= ? AND b.end_date >= ?
          GROUP BY c.category_name";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iss", $userId, $endDate, $startDate);
$stmt->execute();
$budgetTotal = $stmt->get_result();
$budgetData = [];
while ($row = $budgetTotal->fetch_assoc()) {
    $budgetData[$row['category_name']] = [
        'total_budget' => $row['total_budget'],
        'total_expense' => 0
    ];
}

// Get total expenses for each category
$query = "SELECT c.category_name, SUM(e.amount) AS total_expense
          FROM expenses e
          JOIN categories c ON e.category_id = c.id
          WHERE e.user_id = ? AND e.expense_date BETWEEN ? AND ?
          GROUP BY c.category_name";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iss", $userId, $startDate, $endDate);
$stmt->execute();
$expenseData = $stmt->get_result();
while ($row = $expenseData->fetch_assoc()) {
    if (isset($budgetData[$row['category_name']])) {
        $budgetData[$row['category_name']]['total_expense'] = $row['total_expense'];
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
                            <h4>Expense</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Expense Summary
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pb-20"></div>
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 height-100-p mb-30">
                        <div class="row">
                            <!-- Expense Summary Section -->
                            <div class="col-md-6">
                                <div class="card-box pd-20 box-shadow">
                                    <h4 class="text-center">Expense Summary</h4>
                                    <form method="get" action="reports.php">
                                        <div class="form-group">
                                            <label for="start_date">Start Date:</label>
                                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label for="end_date">End Date:</label>
                                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" class="form-control" />
                                        </div>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </form>
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($expenseSummary->num_rows > 0) { 
                                                while ($row = $expenseSummary->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                                        <td><?php echo number_format($row['total_amount'], 2); ?> Php</td>
                                                    </tr>
                                            <?php } 
                                            } else { ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">No expenses recorded for this period.</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Budget Usage Section -->
                            <div class="col-md-6">
                                <div class="card-box pd-20 box-shadow">
                                    <h4 class="text-center">Budget Usage</h4>
                                    <table class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Total Budget</th>
                                                <th>Total Expense</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($budgetData)) { 
                                                foreach ($budgetData as $categoryName => $data) { ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($categoryName); ?></td>
                                                        <td><?php echo number_format($data['total_budget'], 2); ?> Php</td>
                                                        <td><?php echo number_format($data['total_expense'], 2); ?> Php</td>
                                                    </tr>
                                            <?php } 
                                            } else { ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No budget data available.</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
