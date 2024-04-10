<?php
include 'config.php';

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    // Check if the item ID is a valid integer
    if (!ctype_digit($itemId)) {
        echo "Invalid item ID.";
        exit; // Stop script execution
    }

    // Perform deletion from the database
    $sql = "DELETE FROM shared_food WHERE id = $itemId";
    if ($conn->query($sql) === TRUE) {
        // Deletion successful
        echo "success";
    } else {
        // Error
        echo "Error: " . $conn->error;
    }
} else {
    echo "Item ID not provided.";
}

$conn->close();
?>
