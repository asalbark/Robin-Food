<?php
session_start();

// Include the database connection or config file
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Get the current date
$currentDate = date('Y-m-d');

// Delete records where pickup date is greater than the current date
$sql = "DELETE FROM orders
        WHERE user_id = ? 
        AND food_id IN (SELECT id FROM shared_food WHERE booking_date > ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $_SESSION['id'], $currentDate);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Old orders deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete old orders']);
}
?>
