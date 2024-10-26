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

// Retrieve the username from the session or set a default
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Get total expenses
$query = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalExpenses = $result->fetch_assoc()['total_expenses'] ?? 0;
} else {
    $totalExpenses = 0; // Error fallback
}

// Get total budgets
$query = "SELECT SUM(budget_limit) AS total_budgets FROM budgets WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalBudgets = $result->fetch_assoc()['total_budgets'] ?? 0;
} else {
    $totalBudgets = 0; // Error fallback
}

// Calculate remaining budget
$query = "SELECT IFNULL(SUM(amount), 0) AS total_expenses
          FROM expenses e
          JOIN budgets b ON e.category_id = b.category_id
          WHERE e.user_id = ? AND e.expense_date BETWEEN b.start_date AND b.end_date";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalExpensesInRange = $result->fetch_assoc()['total_expenses'] ?? 0;
} else {
    $totalExpensesInRange = 0; // Error fallback
}

$remainingBudget = $totalBudgets - $totalExpensesInRange;

// Calculate percentages
$percentageSpent = ($totalBudgets > 0) ? ($totalExpensesInRange / $totalBudgets) * 100 : 0;
$remainingBudgetPercentage = ($totalBudgets > 0) ? ($remainingBudget / $totalBudgets) * 100 : 0;
$totalBudgetsPercentage = 100;  // Total budgets will always be 100%

?>

<!-- Main content -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Dashboard</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                   Dashboard
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="card-box mb-30">
                <div class="pb-20"></div>
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 height-100-p mb-30">
                        <div class="row clearfix progress-box">

                            <!-- Total Expenses -->
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 height-100-p">
                                    <div class="progress-box text-center">
                                        <input
                                            type="text"
                                            class="knob dial1"
                                            value="<?php echo round($percentageSpent); ?>"
                                            data-width="120"
                                            data-height="120"
                                            data-linecap="round"
                                            data-thickness="0.12"
                                            data-bgColor="#fff"
                                            data-fgColor="#1b00ff"
                                            data-angleOffset="180"
                                            readonly
                                            data-toggle="tooltip"
                                            title="Total spent from your available budget"
                                        />
                                        <h5 class="text-blue padding-top-10 h5">Total Expenses</h5>
                                        <span class="d-block">
                                           <?php echo number_format($totalExpensesInRange, 2); ?> PHP Spent
                                           <i class="fa fa-line-chart text-blue"></i>
                                        </span>
                                        <small><?php echo round($percentageSpent, 2); ?>% of your total budget</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Budgets -->
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 height-100-p">
                                    <div class="progress-box text-center">
                                        <input
                                            type="text"
                                            class="knob dial1"
                                            value="<?php echo round($totalBudgetsPercentage); ?>"
                                            data-width="120"
                                            data-height="120"
                                            data-linecap="round"
                                            data-thickness="0.12"
                                            data-bgColor="#fff"
                                            data-fgColor="#00e091"
                                            data-angleOffset="180"
                                            readonly
                                            data-toggle="tooltip"
                                            title="Total budget allocated"
                                        />
                                        <h5 class="text-light-green padding-top-10 h5">Total Budget</h5>
                                        <span class="d-block">
                                           <?php echo number_format($totalBudgets, 2); ?> PHP
                                           <i class="fa text-light-green fa-line-chart"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Remaining Budget -->
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 height-100-p">
                                    <div class="progress-box text-center">
                                        <input
                                            type="text"
                                            class="knob dial1"
                                            value="<?php echo round($remainingBudgetPercentage); ?>"
                                            data-width="120"
                                            data-height="120"
                                            data-linecap="round"
                                            data-thickness="0.12"
                                            data-bgColor="#fff"
                                            data-fgColor="#1b00ff"
                                            data-angleOffset="180"
                                            readonly
                                            data-toggle="tooltip"
                                            title="Remaining budget after expenses"
                                        />
                                        <h5 class="text-blue padding-top-10 h5">Remaining Budget</h5>
                                        <span class="d-block">
                                           <?php echo number_format($remainingBudget, 2); ?> PHP Remaining
                                           <i class="fa fa-line-chart text-blue"></i>
                                        </span>
                                        <small><?php echo round($remainingBudgetPercentage, 2); ?>% of your total budget remaining</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// Initialize tooltips for better UX
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
