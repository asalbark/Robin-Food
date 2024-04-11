<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to the login page or display an error message
    header("Location: login.php");
    exit(); // Stop further execution
}

// Check if the cart session variable is set and not empty
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Prepare and execute the SQL query to insert orders into the database
    $userId = $_SESSION['id'];
    $orderDate = date('Y-m-d');
    
    foreach ($_SESSION['cart'] as $cartItem) {
        $foodId = $cartItem['id'];
        
        $sql = "INSERT INTO orders (user_id, food_id, order_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userId, $foodId, $orderDate);
        if ($stmt->execute()) {
            // Order successfully inserted into the database
        } else {
            echo "Error inserting order: " . $stmt->error;
        }
    }
    
    // Clear the cart session variable after inserting orders into the database
    $_SESSION['cart'] = array();
} else {
    echo "Cart is empty.";
}

// Close the database connection
$conn->close();
?>
