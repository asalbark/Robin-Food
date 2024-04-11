<?php
include 'config.php';
include 'geocode.php';

// Handle saving user location
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if latitude and longitude are set
    if (isset($_POST["latitude"]) && isset($_POST["longitude"])) {
        // Retrieve user ID from session
        $user_id = $_SESSION['id'];

        // Retrieve latitude and longitude from the form
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];

        // Insert the location into the user_locations table
        $sql = "INSERT INTO user_locations (user_id, latitude, longitude) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("idd", $user_id, $latitude, $longitude);
            if ($stmt->execute()) {
                echo "Location saved successfully.";
            } else {
                echo "Error saving location: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Latitude and longitude are not set.";
    }
}

// Fetch data from the shared_food table
$sql = "SELECT * FROM shared_food";
$result = $conn->query($sql);

$shared_food_data = array(); // Initialize an empty array to store shared food data

if ($result->num_rows > 0) {
    // Loop through each row and add it to the shared food data array
    while($row = $result->fetch_assoc()) {
        // Check if latitude and longitude keys exist before accessing them
        if (isset($row['latitude']) && isset($row['longitude']) && isset($_POST['latitude']) && isset($_POST['longitude'])) {
            // Calculate distance between user's location and food item's location
            $distance = calculateDistance($row['latitude'], $row['longitude'], $_POST["latitude"], $_POST["longitude"]);
            $row['distance'] = $distance;
        }
        $shared_food_data[] = $row;
    }
    // Sort food items by distance
    usort($shared_food_data, function($a, $b) {
        return $a['distance'] - $b['distance'];
    });
} else {
    echo "0 results";
}

// Close the database connection
$conn->close();

// Function to calculate distance between two sets of coordinates
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return $miles;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Food</title>
</head>
<body>
    <h2>Received Food</h2>

    <!-- Display shared food data -->
    <?php foreach ($shared_food_data as $food_item) { ?>
        <div>
            <p>Food Name: <?php echo $food_item['food_name']; ?></p>
            <p>Category: <?php echo $food_item['category']; ?></p>
            <p>Ingredients: <?php echo $food_item['ingredients']; ?></p>
            <p>Making Date: <?php echo ($food_item['making_date']); ?></p>
            <p>Expiry Date: <?php echo ($food_item['expiry_date']); ?></p>
            <p>Amount: <?php echo $food_item['amount']; ?></p>
            <p>Longitude: <?php echo isset($food_item['longitude']) ? $food_item['longitude'] : "N/A"; ?></p>
            <p>Latitude: <?php echo isset($food_item['latitude']) ? $food_item['latitude'] : "N/A"; ?></p>
            <p>Photo Path: <?php echo $food_item['photo_path']; ?></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="food_item_id" value="<?php echo $food_item['id']; ?>">
                <button type="submit" name="add_to_cart">Add to Cart</button>
            </form>
        </div>
    <?php } ?>

    <form id="locationForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <button type="button" onclick="getLocation()">Send My Location</button>
    </form>

    <!-- Button to sort by location -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="latitude" value="<?php echo isset($_POST['latitude']) ? $_POST['latitude'] : ''; ?>">
        <input type="hidden" name="longitude" value="<?php echo isset($_POST['longitude']) ? $_POST['longitude'] : ''; ?>">
        <button type="submit" name="sort">Sort by Location</button>
    </form>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            // Submit the location form to save the location
            document.getElementById('locationForm').submit();
        }
    </script>
    <footer>
    <div>
        <a href="cart.php"> <!-- Change view_cart.php to the page where you display the cart -->
            <img src="cart_icon.png" alt="Cart" width="50" height="50">
        </a>
</div>
    </footer>
</body>
</html>
