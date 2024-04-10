<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "robin_food";

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);
 
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // echo "Connected successfully<br>";  // Debugging: Check if connection is successful
}
catch(Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
