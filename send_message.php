<?php
session_start();
include 'config.php';

// Retrieve sender (current user) ID
$sender_id = $_SESSION['id'];

// Retrieve receiver (shared food owner) and order IDs from the POST data
$receiver_id = $_POST['receiver_id'] ?? '';
$order_id = $_POST['order_id'] ?? '';

// Retrieve message content from the POST data
$message_content = $_POST['message'] ?? '';

// Insert the message into the database
$sql = "INSERT INTO messages (sender_id, receiver_id, order_id, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiis", $sender_id, $receiver_id, $order_id, $message_content);

if ($stmt->execute()) {
    // Return the inserted message data
    $message_id = $stmt->insert_id;
    $message = array(
        'id' => $message_id,
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'order_id' => $order_id,
        'message' => $message_content
    );
    echo json_encode($message);
} else {
    // Handle database error
    http_response_code(500);
    echo "Error sending message: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
