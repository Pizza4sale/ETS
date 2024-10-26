<?php
// Start the session only if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data, including the username
$query = "SELECT username, profile_picture FROM users WHERE id = ?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    // Log any errors for debugging
    error_log("Database error: " . $mysqli->error);
}

// Check for budgets that are exceeded
$notifications = [];
$budgetQuery = "SELECT b.category_id, c.category_name, IFNULL(SUM(e.amount), 0) as total_expense, b.budget_limit 
                FROM budgets b 
                LEFT JOIN expenses e ON b.category_id = e.category_id AND e.expense_date BETWEEN b.start_date AND b.end_date 
                JOIN categories c ON b.category_id = c.id 
                WHERE b.user_id = ? 
                GROUP BY b.category_id";
$budgetStmt = $mysqli->prepare($budgetQuery);
if ($budgetStmt) {
    $budgetStmt->bind_param("i", $userId);
    $budgetStmt->execute();
    $budgetResult = $budgetStmt->get_result();

    while ($row = $budgetResult->fetch_assoc()) {
        if ($row['total_expense'] > $row['budget_limit']) {
            $notifications[] = "Budget exceeded for category: " . htmlspecialchars($row['category_name']) . 
                               " (Spent: " . number_format($row['total_expense'], 2) . 
                               ", Limit: " . number_format($row['budget_limit'], 2) . ")";
        }
    }
    $budgetStmt->close();
} else {
    error_log("Database error: " . $mysqli->error);
}
?>

<div class="header">
    <div class="header-left">
        <div class="menu-icon bi bi-list"></div>
        <div class="search-toggle-icon bi bi-search" data-toggle="header_search"></div>
        <div class="header-search">
            <form action="search.php" method="GET">
                <div class="form-group mb-0">
                    <i class="dw dw-search2 search-icon"></i>
                    <input type="text" class="form-control search-input" name="query" placeholder="Search Here" id="searchInput" />
                </div>
                <div id="searchResults"></div>
            </form>
        </div>
    </div>
    <div class="header-right">
        <div class="user-notification">
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                    <i class="icon-copy dw dw-notification"></i>
                    <span id="notification-badge" class="badge notification-active"><?php echo count($notifications); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div id="notification-list" class="notification-list mx-h-350 standard-scrollbar">
                        <?php if (empty($notifications)): ?>
                            <p class="no-notifications">No notifications</p>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <p><?php echo $notification; ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" alt="Profile Picture" />
                    </span>
                    <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="profile.php"><i class="dw dw-user1"></i> Profile</a>
                    <a class="dropdown-item" href="settings.php"><i class="dw dw-settings2"></i> Settings</a>
                    <a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS for standard scrollbar and additional styles */
.standard-scrollbar {
    overflow-y: auto;
    max-height: 350px; /* Adjust the maximum height as needed */
    padding: 10px; /* Add padding for a cleaner look */
}

/* Style for user icon */
.user-icon img {
    width: 50px; /* Set the desired width */
    height: 50px; /* Set the desired height */
    border-radius: 50%; /* Make it circular */
    object-fit: cover; /* Ensure the image covers the space without distortion */
}

/* Style for individual notifications */
#notification-list p {
    margin: 0 0 15px; /* Add spacing between notifications */
    padding: 15px;
    background-color: #fff; /* Background color for notifications */
    border-radius: 8px; /* Rounded corners for notifications */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
    transition: background-color 0.3s ease; /* Smooth background color transition */

    /* Text styles */
    font-size: 14px;
    color: #333;
}

#notification-list a {
    text-decoration: none;
    color: #007bff; /* Link color */
    font-weight: bold;
}

#notification-list a:hover {
    background-color: #f5f5f5; /* Background color on hover */
}

#notification-list p.no-notifications {
    text-align: center;
    font-style: italic;
    color: #888;
}
</style>
