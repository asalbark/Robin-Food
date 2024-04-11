<?php
// Handle form submission
include 'config.php';

$deliveryDays = isset($_POST['deliveryDays']) ? $_POST['deliveryDays'] : [];
$deliveryHours = isset($_POST['deliveryHours']) ? $_POST['deliveryHours'] : [];

// Convert arrays to strings for storage
$deliveryDaysStr = implode(",", $deliveryDays);
$deliveryHoursStr = implode(",", $deliveryHours);

// Insert into database
$sql = "INSERT INTO delivery_schedule (delivery_days, delivery_hours) VALUES ('$deliveryDaysStr', '$deliveryHoursStr')";

if ($conn->query($sql) === TRUE) {
    echo "Food shared successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Share Food Page</title>
<style>
    /* Add your CSS styles here */
</style>
</head>
<body>
<h1>Share Food Page</h1>
<form id="shareForm" action="submit.php" method="post">
    <label for="deliveryDays">Select delivery days:</label>
    <select id="deliveryDays" name="deliveryDays[]" multiple>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
        <option value="Saturday">Saturday</option>
        <option value="Sunday">Sunday</option>
    </select>

    <label for="deliveryHours">Select delivery hours:</label>
    <select id="deliveryHours" name="deliveryHours[]" multiple>
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
        <option value="Evening">Evening</option>
    </select>

    <button type="submit">Share Food</button>
</form>

<script>
    // Add your JavaScript code here, if needed
</script>
</body>
</html>
