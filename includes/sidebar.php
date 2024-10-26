<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="left-side-bar">
    <div class="brand-logo">
        <a href="index.php">
            <span>Dashboard</span>
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <nav class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li>
                    <a href="index.php" class="dropdown-toggle no-arrow <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" aria-label="Home">
                        <span class="micon bi bi-house-heart-fill"></span><span class="mtext">Home</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" aria-expanded="<?php echo isset($dropdown_active) && $dropdown_active == 'expenses' ? 'true' : 'false'; ?>" aria-label="Expenses">
                        <span class="micon bi bi-wallet2"></span><span class="mtext">Expenses</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="add_expenses.php" class="<?php echo $current_page == 'add_expenses.php' ? 'active' : ''; ?>">Add Expenses</a></li>
                        <li><a href="expenses.php" class="<?php echo $current_page == 'expenses.php' ? 'active' : ''; ?>">Manage Expenses</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" aria-expanded="<?php echo isset($dropdown_active) && $dropdown_active == 'budget' ? 'true' : 'false'; ?>" aria-label="Budget">
                        <span class="micon bi bi-cash"></span><span class="mtext">Budget</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="add_budget.php" class="<?php echo $current_page == 'add_budget.php' ? 'active' : ''; ?>">Add Budget</a></li>
                        <li><a href="Budget.php" class="<?php echo $current_page == 'Budget.php' ? 'active' : ''; ?>">Manage Budget</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" aria-expanded="<?php echo isset($dropdown_active) && $dropdown_active == 'categories' ? 'true' : 'false'; ?>" aria-label="Categories">
                        <span class="micon bi bi-tags"></span><span class="mtext">Categories</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="add_categories.php" class="<?php echo $current_page == 'add_categories.php' ? 'active' : ''; ?>">Add Categories</a></li>
                        <li><a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">Manage Categories</a></li>
                    </ul>
                </li>
                <li>
                    <a href="calendar.php" class="dropdown-toggle no-arrow <?php echo $current_page == 'calendar.php' ? 'active' : ''; ?>" aria-label="Calendar">
                        <span class="micon bi bi-calendar"></span><span class="mtext">Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="dropdown-toggle no-arrow <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" aria-label="Summary Report">
                        <span class="micon bi bi-file-earmark-text"></span><span class="mtext">Summary Report</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
