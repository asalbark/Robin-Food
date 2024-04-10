<?php
session_start();
include 'config.php';

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Remove expired items from the orders table first
$currentDate = date('Y-m-d');
$sqlDeleteOrders = "DELETE FROM orders WHERE food_id IN (SELECT id FROM shared_food WHERE expiry_date < ?)";
if ($stmtDeleteOrders = $conn->prepare($sqlDeleteOrders)) {
    $stmtDeleteOrders->bind_param("s", $currentDate);
    if ($stmtDeleteOrders->execute()) {
        echo "";
    } else {
        echo " " . $stmtDeleteOrders->error;
    }
    $stmtDeleteOrders->close();
} else {
    echo " " . $conn->error;
}

// Now remove the expired items from the shared_food table
$sqlDeleteSharedFood = "DELETE FROM shared_food WHERE expiry_date < ?";
if ($stmtDeleteSharedFood = $conn->prepare($sqlDeleteSharedFood)) {
    $stmtDeleteSharedFood->bind_param("s", $currentDate);
    if ($stmtDeleteSharedFood->execute()) {
        echo "";
    } else {
        echo " " . $stmtDeleteSharedFood->error;
    }
    $stmtDeleteSharedFood->close();
} else {
    echo "" . $conn->error;
}

// Initialize $_SESSION['cart'] as an empty array if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Initialize $shared_food_data as an empty array
$shared_food_data = array();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page or display an error message
    header("Location: login.php");
    exit(); // Stop further execution
}

// Handle saving user location and sorting food items
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if latitude and longitude are set
    if (isset($_POST["latitude"]) && isset($_POST["longitude"])) {
        $user_id = $_SESSION['id']; // Ensure user is logged in
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];

        // Fetch data from the shared_food table
        $sql = "SELECT * FROM shared_food";
        if (!empty($_POST['category'])) {
            $category = $_POST['category'];
            $sql .= " WHERE category = '$category'";
        }
        $result = $conn->query($sql);

        if ($result) {
            // Process the fetched data
            while ($row = $result->fetch_assoc()) {
                $row['distance'] = -1;
                if (!empty($row['latitude']) && !empty($row['longitude'])) {
                    $distance = calculateDistance($row['latitude'], $row['longitude'], $latitude, $longitude);
                    $row['distance'] = $distance;
                }
                $shared_food_data[] = $row;
            }

            // Sort food items by distance
            usort($shared_food_data, function($a, $b) {
                return $a['distance'] - $b['distance'];
            });
        } else {
            echo "Error fetching shared food data: " . $conn->error;
        }
    } else {
        echo "Latitude and longitude not set.";
    }
}

// Handle adding items to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    if (isset($_POST['food_item_id'])) {
        $food_item_id = $_POST['food_item_id'];
        // Check if the item already exists in the cart
        $item_exists = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $food_item_id) {
                // Check if 'quantity' key exists, if not, initialize it to 1
                if (!isset($cart_item['quantity'])) {
                    $cart_item['quantity'] = 1;
                } else {
                    // Item exists, update quantity
                    $cart_item['quantity'] += 1;
                }
                $item_exists = true;
                break;
            }
        }
        // If the item doesn't exist, add it to the cart
        if (!$item_exists) {
            // Retrieve information about the selected food item from the database
            $sql = "SELECT * FROM shared_food WHERE id = ? AND ordered = FALSE";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $food_item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    // Add the selected food item to the cart session variable
                    $row['quantity'] = 1; // Initialize quantity
                    $_SESSION['cart'][] = $row;
                    // Update the ordered column for the selected food item
                    $sqlUpdateOrdered = "UPDATE shared_food SET ordered = TRUE WHERE id = ?";
                    if ($stmtUpdateOrdered = $conn->prepare($sqlUpdateOrdered)) {
                        $stmtUpdateOrdered->bind_param("i", $food_item_id);
                        if ($stmtUpdateOrdered->execute()) {
                            // Success: Food item ordered
                        } else {
                            echo "Error updating ordered status: " . $stmtUpdateOrdered->error;
                        }
                        $stmtUpdateOrdered->close();
                    } else {
                        echo "Error preparing statement: " . $conn->error;
                    }
                } else {
                    // Food item not found or already ordered
                    echo "<script>alert('Food item not found or already ordered');</script>";
                }
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }
    }
}


// Fetch data from the shared_food table if sorting didn't happen
if (empty($shared_food_data)) {
    $sql = "SELECT * FROM shared_food";
    if (!empty($_GET['category'])) {
        $category = $_GET['category'];
        $sql .= " WHERE category = '$category'";
    }
    $result = $conn->query($sql);

    if ($result) {
        // Process the fetched data
        while ($row = $result->fetch_assoc()) {
            $shared_food_data[] = $row;
        }
    } else {
        echo "Error fetching shared food data: " . $conn->error;
    }
}

$cartItemCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$conn->close();

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    // Check if latitude and longitude values are empty
    if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) {
        return -1; // Indicate that distance calculation is not possible
    }

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return $miles;
}
// Sort food items by expiry date
usort($shared_food_data, function($a, $b) {
    return strtotime($a['expiry_date']) - strtotime($b['expiry_date']);
});


?>
<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Received Food</title>
<link rel="stylesheet" href="css/style.css">
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<nav>
<ul class="navbar">
<li><img src="http://localhost/Project/images/robin.png" width="50"></li>
<li><a href="UserSelection.html" class="nav-link" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
<li><a href="SharePage.php" class="nav-link" data-he="שיתוף אוכל" data-en="Share Food">שיתוף אוכל</a></li>
<li><a href="logout.php" class="nav-link" data-he="התנתק" data-en="Logout">התנתק</a></li>
<li><button onclick="toggleTranslation()" data-he="EN" data-en="HE">EN</button></li>

<li>
<a href="cart.php" class="cart-link">
<button class="cart-btn">
<i class="fas fa-shopping-cart cart-icon"></i> <?php echo $cartItemCount; ?>
</button>
</a>
</li>
</ul>
<h1 data-he="שולחן משולב: בואו לקבל, לחלוק ולשמור יחד על אהבה לסביבה ולזולת!"
    data-en="Combined Table: Let's receive, share, and preserve our love for the environment and others">
    שולחן משולב: בואו לקבל, לחלוק ולשמור יחד על אהבה לסביבה ולזולת!
</h1><br>
<br>
<br>
<br>
</nav>
<nav class="sidenav custom-nav-2">
<ul class="nav-list">
<li class="nav-item">
<button type="button" onclick="getLocationAndSort()" class="button" data-he="סידור לפי מיקום"
    data-en="Sort by Location">סידור לפי מיקום</button>
</li>
<li class="nav-item">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<input type="hidden" name="latitude" id="latitude" value="<?php echo isset($_POST['latitude']) ? $_POST['latitude'] : ''; ?>">
<input type="hidden" name="longitude" id="longitude" value="<?php echo isset($_POST['longitude']) ? $_POST['longitude'] : ''; ?>">
<button type="submit" name="sort_by_expiry" class="button" data-he="סידור לפי תאריך פג תוקף"
    data-en="Sort by Expiry Date">סידור לפי תאריך פג תוקף</button>
</form>
</li>

</ul>
</nav>
<br>
<!-- Display shared food data -->
<div class="card-container">
<?php foreach ($shared_food_data as $food_item) { ?>
<div class="card" class="col4">
<p class="food-name"><?php echo $food_item['food_name']; ?></p>
<p><strong data-he="קטגוריה" data-en="Category">קטגוריה</strong> <?php echo $food_item['category']; ?></p>
<p><strong data-he="רכיבים" data-en="Ingredients">רכיבים</strong> <?php echo $food_item['ingredients']; ?></p>
<p><strong data-he="תאריך הכנה" data-en="Preparation Date">תאריך הכנה</strong> <?php echo $food_item['making_date']; ?></p>
<p><strong data-he="תאריך פג תוקף" data-en="Expiry Date">תאריך פג תוקף</strong> <?php echo $food_item['expiry_date']; ?></p>
<p><strong data-he="משקל" data-en="Weight">משקל</strong> <?php echo $food_item['amount']; ?></p>
<p><strong data-he="תאריך איסוף" data-en="Collection Date">תאריך איסוף</strong> <?php echo $food_item['booking_date']; ?></p>
<p><strong data-he="שעות איסוף" data-en="Collection Hours">שעות איסוף</strong> <?php echo $food_item['booking_hour']; ?></p>
<p><strong data-he="מיקום" data-en="Location">מיקום</strong> <span class="location" data-lat="<?php echo $food_item['latitude']; ?>"
            data-lng="<?php echo $food_item['longitude']; ?>"></span></p>
<!-- Display the photo if photo_path is not empty -->
<?php if (!empty($food_item['photo_path'])) { ?>
<img src="<?php echo $food_item['photo_path']; ?>" alt="Food Photo" class="food-photo">
<?php } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<input type="hidden" name="food_item_id" value="<?php echo $food_item['id']; ?>">
<br>
<button type="submit" name="add_to_cart"><i class="fas fa-cart-plus"></i> </button>
</form>
</div>
<?php } ?>
</div>
 
<script>
      function getLocationAndSort() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                // Set the latitude and longitude values in the hidden input fields
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                // Submit the form to save the location
                document.querySelector("form").submit();
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var locations = document.querySelectorAll('.location');

        locations.forEach(function(location) {
            var lat = location.getAttribute('data-lat');
            var lng = location.getAttribute('data-lng');

            reverseGeocode(lat, lng, function (address) {
                location.textContent = "Location: " + address; // Add "Location: " before the address
            });
        });
    });
    
    function showPosition(position) {
    // Set the latitude and longitude values in the hidden input fields
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
    // Submit the form to save the location
    document.querySelector("form").submit();
    }
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }




    document.addEventListener('DOMContentLoaded', function () {
        var locationsList = document.getElementsByClassName('location');
 
        Array.from(locationsList).forEach(function(location) {
            var lat = location.getAttribute('data-lat');
            var lng = location.getAttribute('data-lng');
            var addressElement = location;
 
            reverseGeocode(lat, lng, function(address) {
                addressElement.textContent = address;
            });
        });
    });
 
    function reverseGeocode(lat, lng, callback) {
        var xhr = new XMLHttpRequest();
        var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=he';
 
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                var address = response.display_name;
                callback(address);
            }
        };
 
        xhr.open('GET', url, true);
        xhr.send();
    }
</script>
<script>
    function toggleTranslation() {
        var elements = document.querySelectorAll('[data-he], [data-en]');
        elements.forEach(function(element) {
            if (!element.hasAttribute('lang') || element.getAttribute('lang') === 'he') {
                element.textContent = element.getAttribute('data-en');
                element.setAttribute('lang', 'en');
            } else {
                element.textContent = element.getAttribute('data-he');
                element.setAttribute('lang', 'he');
            }
        });
    }
</script>

 
</body>
</html>