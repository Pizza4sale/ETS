<?php 
include("includes/head.php"); 
include("includes/navbar.php"); 
include("includes/sidebar.php"); 
include("includes/script.php"); 

// Fetch budgets and their total expenses for the logged-in user
$userId = $_SESSION['user_id'];
$query = "SELECT b.id, c.category_name, b.budget_limit, b.start_date, b.end_date, 
          COALESCE(SUM(e.amount), 0) AS total_spent 
          FROM budgets b 
          LEFT JOIN expenses e ON b.category_id = e.category_id AND e.expense_date BETWEEN b.start_date AND b.end_date
          JOIN categories c ON b.category_id = c.id 
          WHERE b.user_id = ?
          GROUP BY b.id";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Main content -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Budgets</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Manage Budget
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
                        <h4 class="text-center">Budgets</h4>
                        <table class="table hover data-table-export nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Budget Limit</th>
                                    <th>Total Spent</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($row['budget_limit'], 2)); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($row['total_spent'], 2)); ?></td>
                                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                                        <td>
                                            <?php
                                            // Status Badge
                                            if ($row['total_spent'] > $row['budget_limit']) {
                                                echo "<span class='badge badge-danger'>Exceeded</span>";
                                            } else {
                                                echo "<span class='badge badge-success'>Within Limit</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
