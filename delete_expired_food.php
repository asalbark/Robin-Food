<?php
include 'config.php';

// Connect to the database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete expired food items from the database
$sql = "DELETE FROM shared_food WHERE expiry_date < CURDATE()";
if ($conn->query($sql) === TRUE) {
    echo "Expired food items deleted successfully";
} else {
    echo "Error deleting expired food items: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
