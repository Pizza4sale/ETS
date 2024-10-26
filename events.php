<?php
include("includes/head.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$events = [];

// Fetch budgets for the user
$query = "SELECT category_id, start_date, end_date, budget_limit FROM budgets WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Build the events array
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => 'Budget for Category ' . htmlspecialchars($row['category_id']),
        'start' => $row['start_date'],
        'end' => $row['end_date'],
        'allDay' => true  // Set to true if you want full-day events
    ];
}

$stmt->close();

// Set the content type to JSON
header('Content-Type: application/json');
echo json_encode($events);
exit();
