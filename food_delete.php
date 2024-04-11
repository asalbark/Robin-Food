<?php
session_start();

// Include the database connection or config file
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Check if the food ID is provided in the request
if (!isset($_POST['foodId'])) {
    echo json_encode(['success' => false, 'message' => 'Food ID is missing']);
    exit;
}

// Get the food ID from the request
$foodId = $_POST['foodId'];

// Start a transaction
$conn->begin_transaction();

// Delete the food item from both orders and shared_food tables
$user_id = $_SESSION['id'];
$sqlOrders = "DELETE FROM orders WHERE user_id = ? AND food_id = ?";
$stmtOrders = $conn->prepare($sqlOrders);
$stmtOrders->bind_param("ii", $user_id, $foodId);

$sqlSharedFood = "DELETE FROM shared_food WHERE id = ?";
$stmtSharedFood = $conn->prepare($sqlSharedFood);
$stmtSharedFood->bind_param("i", $foodId);

// Execute both delete queries
if ($stmtOrders->execute() && $stmtSharedFood->execute()) {
    // Commit the transaction if both queries succeed
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Food item deleted successfully']);
} else {
    // Rollback the transaction if any query fails
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to delete food item']);
}

?>
