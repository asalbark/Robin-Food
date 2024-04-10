<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Handle unauthorized access
    exit;
}

// Retrieve user ID
$user_id = $_SESSION['id'];

// Query to fetch unread notifications count for the user
$sql = "SELECT COUNT(*) AS count FROM messages WHERE receiver_id = ? AND status = 'unread'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $notification_count = $row['count'];
    // Return count as JSON
    echo json_encode(array('count' => $notification_count));
} else {
    echo json_encode(array('count' => 0)); // Return 0 if there's an error
}
$stmt->close();
$conn->close();
?>
