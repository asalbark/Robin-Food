<?php
session_start();
 
// Include the database connection or config file
include 'config.php';
 
// Initialize an empty array to store the cart items
$cart_items = [];
 
// Check if the cart session variable is set and not empty
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Extract food IDs from the $_SESSION['cart'] array
    $food_ids = array_column($_SESSION['cart'], 'id');
    
    // Prepare a comma-separated string of food IDs for the SQL query
    $food_ids_str = implode(',', $food_ids);
    
    // Retrieve the details of the food items from the database
    $sql = "SELECT * FROM shared_food WHERE id IN ($food_ids_str)";
    $result = $conn->query($sql);
 
    if ($result->num_rows > 0) {
        // Fetch and store the details of each food item in the cart_items array
        while($food_item = $result->fetch_assoc()) {
            $cart_items[] = $food_item;
        }
    } else {
        echo "No items found in the cart.";
    }
} else {
    echo "";
}
 
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
 
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title data-he="עגלה" data-en="Cart">עגלה</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
<link rel="stylesheet" href="cart.css"> <!-- Make sure to replace with the correct path to your CSS file -->
<nav>
<ul class="navbar">
        <li><img src="http://localhost/Project/images/robin.png" width="50"></li>
        <li><a href="UserSelection.html" class="nav-link" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
        <li><a href="SharePage.php" class="nav-link" data-he="שיתוף אוכל" data-en="Share Food">שיתוף אוכל</a></li>
        <li><a href="previous_orders.php" class="nav-link" data-he="הזמנות קודמות" data-en="Previous Orders">הזמנות קודמות</a></li>
        <li><a href="logout.php" class="nav-link" data-he="התנתק" data-en="Logout">התנתק</a></li>
        <li><button onclick="toggleTranslation()" data-he="EN" data-en="HE">EN</button></li>
    </ul>
</nav>
<div id="pageTitle" data-he="העגלה שלי" data-en="My Cart">העגלה שלי</div>
</head>
 
<body>
<div class="container" id="cartContainer">
<?php if (!empty($cart_items)) : ?>
<?php foreach ($cart_items as $food_item) : ?>
<div class="card">
<?php if (!empty($food_item['photo_path'])) { ?>
<img src="<?php echo $food_item['photo_path']; ?>" alt="Food Photo" class="food-photo">
<?php } else { ?>
<img src="placeholder.jpg" alt="Placeholder">
<?php } ?>
<h3 data-he="<?php echo $food_item['food_name']; ?>" data-en="<?php echo $food_item['food_name']; ?>"><?php echo $food_item['food_name']; ?></h3>
                <p data-he="קטגוריה: <?php echo $food_item['category']; ?>" data-en="Category: <?php echo $food_item['category']; ?>"><strong>קטגוריה:</strong> <?php echo $food_item['category']; ?></p>
                <p data-he="ריכיבים: <?php echo $food_item['ingredients']; ?>" data-en="Ingredients: <?php echo $food_item['ingredients']; ?>"><strong>ריכיבים:</strong> <?php echo $food_item['ingredients']; ?></p>
                <p data-he="תאריך הכנה: <?php echo $food_item['making_date']; ?>" data-en="Preparation Date: <?php echo $food_item['making_date']; ?>"><strong>תאריך הכנה:</strong> <?php echo $food_item['making_date']; ?></p>
                <p data-he="תאריך פג תוקף: <?php echo $food_item['expiry_date']; ?>" data-en="Expiry Date: <?php echo $food_item['expiry_date']; ?>"><strong>תאריך פג תוקף:</strong> <?php echo $food_item['expiry_date']; ?></p>
                <p data-he="משקל האוכל: <?php echo $food_item['amount']; ?> גרם" data-en="Food Weight: <?php echo $food_item['amount']; ?> grams"><strong>משקל האוכל:</strong> <?php echo $food_item['amount']; ?> גרם</p>
                <p data-he="מיקום" data-en="Location"><strong>מיקום:</strong> <span class="location" data-lat="<?php echo $food_item['latitude']; ?>" data-lng="<?php echo $food_item['longitude']; ?>"></span></p>
                <!-- Remove button -->
                <button class="removeButton" data-food-id="<?php echo $food_item['id']; ?>" data-he="מחיקה" data-en="Remove">מחיקה</button>
            </div>
<?php endforeach; ?>
<!-- Order button -->
<button id="orderButton" data-he="הזמן" data-en="Order">הזמן</button>
<?php else : ?>
    <p data-he="העגלה ריקה" data-en="Cart is empty">העגלה ריקה</p>

<?php endif; ?>
</div>
 
    <!-- Confirmation message -->
<div id="confirmation" class="confirmation">
<div class="confirmation-content">
<p data-he="הזמנתך התקבלה" data-en="Your order has been received">הזמנתך התקבלה</p>

</div>
</div>
 
    <!-- JavaScript to handle item removal and order confirmation -->
<script>
        document.addEventListener('DOMContentLoaded', function() {
            var locationsList = document.getElementsByClassName('location');
 
            Array.from(locationsList).forEach(function(location) {
                var lat = location.getAttribute('data-lat');
                var lng = location.getAttribute('data-lng');
                var addressElement = location;
 
                reverseGeocode(lat, lng, function(address) {
                    addressElement.textContent = address;
                    addMapLinks(addressElement, lat, lng);
                });
            });
        });
 
        function reverseGeocode(lat, lng, callback) {
    var xhr = new XMLHttpRequest();
    var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=he';
 
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            var address = response.display_name;
            callback(address);
        }
    };
 
    xhr.open('GET', url, true);
    xhr.send();
}
 
        // Function to add Waze and Google Maps links to the address
        function addMapLinks(element, lat, lng) {
            var wazeLink = document.createElement('a');
            wazeLink.href = 'https://www.waze.com/ul?ll=' + lat + ',' + lng;
            wazeLink.target = '_blank';
            wazeLink.textContent = 'Open in Waze';
 
            var googleMapsLink = document.createElement('a');
            googleMapsLink.href = 'https://www.google.com/maps?q=' + lat + ',' + lng;
            googleMapsLink.target = '_blank';
            googleMapsLink.textContent = 'Open in Google Maps';
 
            element.appendChild(document.createElement('br'));
            element.appendChild(wazeLink);
            element.appendChild(document.createTextNode(' | '));
            element.appendChild(googleMapsLink);
        }
</script>
<script>
        function toggleTranslation() {
            var elements = document.querySelectorAll('[data-he], [data-en]');
            elements.forEach(function(element) {
                if (element.dataset.he && element.dataset.en) {
                    if (element.textContent.trim() === element.dataset.he) {
                        element.textContent = element.dataset.en;
                    } else {
                        element.textContent = element.dataset.he;
                    }
                }
            });
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all remove buttons
        var removeButtons = document.querySelectorAll('.removeButton');
        removeButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                var foodId = event.target.getAttribute('data-food-id');
                removeItemFromCart(foodId);
                // Remove the card from the DOM
                event.target.parentNode.remove();
            });
        });
    });

    // Function to remove item from cart
    function removeItemFromCart(foodId) {
        // Send an AJAX request to remove the item from the cart session variable
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'remove_from_cart.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('Item removed from cart successfully.');
            } else {
                console.error('Error removing item from cart.');
            }
        };
        xhr.send('food_id=' + encodeURIComponent(foodId));
    }

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var orderButton = document.getElementById("orderButton");
    orderButton.onclick = function() {
        // Insert the order into the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'insert_order.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log(xhr.responseText); // Log the response from insert_order.php
                // Clear the cart
                clearCart();
                // Redirect to the previous orders page after a delay
                setTimeout(function() {
                    window.location.href = "previous_orders.php";
                }, 3000); // 3 seconds delay
            } else {
                console.error('Error placing order.');
                // Handle error, if any
            }
        };
        xhr.send();
    }
});

// Function to clear the cart session variable
function clearCart() {
    // Send an AJAX request to update the cart session variable to an empty array
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_cart_session.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log('Cart session variable cleared successfully.');
        } else {
            console.error('Error clearing cart session variable.');
        }
    };
    xhr.send('cart=[]');
}

// Function to display thank you message
function displayThankYouMessage() {
    // Display a popup or overlay with a thank you message
    var confirmationPopup = document.getElementById("confirmation");
    confirmationPopup.style.display = "block";
}

</script>

</body>
</html>