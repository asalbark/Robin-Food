<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if food_id is set
    if (isset($_POST['food_id'])) {
        // Retrieve the food ID from the form
        $food_id = $_POST['food_id'];

        // Add the food ID to the cart session variable
        $_SESSION['cart'][] = $food_id;

        // Redirect back to the page where the item was added
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>
