<?php
include 'config.php';

// Check if food ID is provided
if (!isset($_GET['id'])) {
    echo "Food ID not provided.";
    exit;
}

// Retrieve food ID from the query string
$food_id = $_GET['id'];

// Delete related orders
$sql_delete_orders = "DELETE FROM orders WHERE food_id = ?";
if ($stmt_delete_orders = $conn->prepare($sql_delete_orders)) {
    $stmt_delete_orders->bind_param("i", $food_id);
    $stmt_delete_orders->execute();
    $stmt_delete_orders->close();
} else {
    echo "Error preparing statement to delete orders: " . $conn->error;
}

// Then delete the food record
$sql_delete_food = "DELETE FROM shared_food WHERE id = ?";
if ($stmt_delete_food = $conn->prepare($sql_delete_food)) {
    $stmt_delete_food->bind_param("i", $food_id);
    if ($stmt_delete_food->execute()) {
        header("location: MySharedFood.php");
        exit;
    } else {
        echo "Error deleting food record: " . $stmt_delete_food->error;
    }
    $stmt_delete_food->close();
} else {
    echo "Error preparing statement to delete food: " . $conn->error;
}
?>
