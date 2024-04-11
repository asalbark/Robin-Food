<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 
include 'config.php';
 
// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user ID from session
    $user_id = $_SESSION['id'];
 
    // Retrieve form data
    $food_name = $_POST["foodName"];
    $category = $_POST["category"];
    $ingredients = ($category === 'ingredients' || $category === 'preserves') ? '' : $_POST["ingredients"];
    $making_date = $_POST["makingDate"];
    $expiry_date = $_POST["expiryDate"];
    $amount = $_POST["amount"];
    $latitude = isset($_POST["latitude"]) ? $_POST["latitude"] : null;
    $longitude = isset($_POST["longitude"]) ? $_POST["longitude"] : null;
    $photo_path = ''; // Initialize photo path
 
    // Handle file upload
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
 
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $photo_path = $targetFile;
    } else {
        // Error moving uploaded file
        echo "Sorry, there was an error uploading your file.";
        exit;
    }
 
    // Retrieve booking date
    $booking_date = isset($_POST["bookingDate"]) ? $_POST["bookingDate"] : '';
 
    // Retrieve booking hour
    $booking_hour = isset($_POST["bookingHour"]) ? implode(", ", $_POST["bookingHour"]) : '';
 
    // Prepare SQL statement to insert into shared_food table
    $sql = "INSERT INTO shared_food (user_id, food_name, category, ingredients, making_date, expiry_date, amount, latitude, longitude, photo_path, booking_date, booking_hour) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
 
    // Bind parameters and execute statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssssddssss", $user_id, $food_name, $category, $ingredients, $making_date, $expiry_date, $amount, $latitude, $longitude, $photo_path, $booking_date, $booking_hour);
 
        // Execute the statement
        if ($stmt->execute()) {
            // Success
        } else {
            // Error executing the statement
            echo "Error executing the statement: " . $stmt->error;
        }
 
        $stmt->close();
    } else {
        // Error preparing the statement
        echo "Error preparing statement: " . $conn->error;
    }
}
 
// Close the connection
$conn->close();
?>
 
<!DOCTYPE html>
<html dir="rtl" lang="He">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-he="שיתוף אוכל" data-en="Share Food">שיתוף אוכל</title>
    <link rel="stylesheet" href="share.css">
</head>
 
<body>
 
    <script>
      function validateDateTime() {
            var making_date = new Date(document.getElementById('makingDate').value);
            var expiry_date = new Date(document.getElementById('expiryDate').value);
            var currentDate = new Date();
 
            if (making_date > expiry_date) {
                alert("Date of making must be before the date of expiry.");
                return false;
            }
    
 
            return true;
        }
                function toggleIngredients() {
            var category = document.getElementById('category').value;
            var ingredientsLabel = document.getElementById('ingredientsLabel');
            var ingredientsTextarea = document.getElementById('ingredients');
 
            if (category === 'ingredients' || category === 'preserves') {
                ingredientsLabel.style.display = 'none';
                ingredientsTextarea.style.display = 'none';
            } else {
                ingredientsLabel.style.display = 'block';
                ingredientsTextarea.style.display = 'block';
            }
        }
 
        function displayImage(input) {
            var previewImage = document.getElementById('previewImage');
            previewImage.src = URL.createObjectURL(input.files[0]);
        }
 
        function openConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'block';
        }
 
        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }
 
        function useCurrentLocation() {
            getLocation();
            closeConfirmationModal();
            // location.reload(); // Reload the page after submission
        }
 
        function useCustomLocation() {
    var customLocation = document.getElementById('customLocation').value.trim();
 
    if (customLocation !== "") {
        // Use Google Maps Geocoding API to get latitude and longitude from the custom location
        getLatLngFromAddress(customLocation);
    } else {
        alert("Please enter a custom location.");
        return;
    }
}
 
function getLatLngFromAddress(address) {
    var nominatimEndpoint = "https://nominatim.openstreetmap.org/search";
   
    var requestUrl = `${nominatimEndpoint}?format=json&limit=1&q=${encodeURIComponent(address)}`;
 
    fetch(requestUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Geocoding Response:", data);
 
            if (data.length > 0) {
                var location = data[0];
 
                saveLocation({
                    latitude: parseFloat(location.lat),
                    longitude: parseFloat(location.lon)
                });
            } else {
                console.error("Geocoding error: No results found");
                alert("Error geocoding address. Please enter a valid location.");
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("Error fetching geocoding data. Please try again later.");
        });
}
 
 
 
 
 
function saveLocation(location) {
    // Implement your logic to save the location to the server
    // You can use AJAX to send the location to the server and save it in the database
    // For now, let's simulate saving it to the local storage
 
    document.getElementById("latitude").value = location.latitude;
    document.getElementById("longitude").value = location.longitude;
    localStorage.setItem('savedLocation', JSON.stringify(location));
    upload_form();
}
 
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
 
        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
 
            saveLocation({
                latitude: latitude,
                longitude: longitude
            });
        }
 
        function showError(error) {
            alert("Error getting location: " + error.message);
        }
 
        function submitForm() {
            // Validate date and time
            if (validateDateTime()) {
                savePhoto();
                openConfirmationModal();
            }
            // If there are validation errors, the form will not be submitted
        }
 
        function savePhoto() {
            var photoInput = document.getElementById('photoInput');
            var file = photoInput.files[0];
 
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Save the photo content to the server or database
                    savePhotoContent(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }
 
        function savePhotoContent(photoContent) {
            // Implement your logic to save the photo content to the server or database
            // You can use AJAX to send the photo content to the server
            // For now, let's simulate saving it to the local storage
            localStorage.setItem('savedPhoto', photoContent);
        }
 
        function upload_form() {
            document.getElementById('uploadForm').submit();
        }
 
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('uploadForm').addEventListener('submit', function(event) {
                event.preventDefault();
                submitForm();
            });
        });
    </script>
<nav>
    <ul class="navbar">
        <li><img src="http://localhost/Project/images/robin.png" width="50"></li>
        <li><a href="SharePage.php" class="nav-link" data-he="שיתוף אוכל" data-en="Share Food">שיתוף אוכל</a></li>
        <li><a href="MySharedFood.php" class="nav-link" data-he="שיתופים קודמים" data-en="Previous Shares">שיתופים קודמים</a></li> <!-- Added translation here -->
        <li><a href="UserSelection.html" class="nav-link" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
        <li><a href="logout.php" class="nav-link" data-he="התנתק" data-en="Logout">התנתק</a></li>
        <li><button onclick="toggleTranslation()" data-he="EN" data-en="HE">EN</button></li>
    </ul>
</nav>
    <br>
    <br>
    <div id="pageTitle" data-he="חלוקת אוכל לטובת הסביבה: יחד נקדם שימור ותחזוקה של כדור הארץ!" data-en="Food Sharing for Environmental Preservation: Together we advance Earth preservation and maintenance!">חלוקת אוכל לטובת הסביבה: יחד נקדם שימור ותחזוקה של כדור הארץ!</div>
    <form id="uploadForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <label for="foodName" data-he="סוג האוכל:" data-en="Food Type">סוג האוכל:</label>
        <input type="text" id="foodName" name="foodName" required>

        <br>

        <label id="categoryLabel" for="category" data-he="קטגוריה:" data-en="Category">קטגוריה:</label>
        <select id="category" name="category" onchange="toggleIngredients();" required>
            <option value="" selected disabled data-he="בחירת קטגוריה" data-en="Choose Category">בחירת קטגוריה</option>
            <option value="food" selected data-he="אוכל" data-en="Food">אוכל</option>
            <option value="ingredients" data-he="רכיבים" data-en="Ingredients">רכיבים</option>
            <option value="preserves" data-he="שימורים" data-en="Preserves">שימורים</option>
        </select>
        <br>
        <label id="ingredientsLabel" for="ingredients" data-he="ריכיבים:" data-en="Ingredients">ריכיבים:</label>
        <textarea id="ingredients" name="ingredients" rows="4" cols="50" required></textarea>
        <br>
        <label for="makingDate" data-he="תאריך הכנה:" data-en="Preparation Date">תאריך הכנה:</label>
        <input type="date" id="makingDate" name="makingDate" required>
        <br>
        <label for="expiryDate" data-he="תאריך פג תוקף:" data-en="Expiration Date">תאריך פג תוקף:</label>
        <input type="date" id="expiryDate" name="expiryDate" required>
        <br>
        <label for="amount" data-he="משקל האוכל:" data-en="Food Weight">משקל האוכל:</label>
        <input type="number" id="amount" name="amount" step="1" min="1" required>
        <br>
        <label for="bookingDate" data-he="תאריך אפשרי לאיסוף:" data-en="Possible Pickup Date">תאריך אפשרי לאיסוף:</label>
        <input type="date" id="bookingDate" name="bookingDate" required>
        <br>
        <h4 for="bookingDate" data-he="שעות אפשריות לאיסוף:" data-en="Possible Pickup Hours">שעות אפשריות לאיסוף:</h4>
        <input type="checkbox" id="morning" name="bookingHour[]" value="morning">
        <label class="checkbox-label" for="morning" data-he="בוקר" data-en="Morning">בוקר</label><br>
        <input type="checkbox" id="afternoon" name="bookingHour[]" value="afternoon">
        <label class="checkbox-label" for="afternoon" data-he="אחר הצהריים" data-en="Afternoon">אחר הצהריים</label><br>
        <input type="checkbox" id="evening" name="bookingHour[]" value="evening">
        <label class="checkbox-label" for="evening" data-he="ערב" data-en="Evening">ערב</label>
        <br>
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <br>
        <div id="uploadSquare" onclick="document.getElementById('photoInput').click();">
            העלאת תמונה
            <input type="file" id="photoInput" name="photo" style="display:none;" accept="image/*" onchange="displayImage(this);">
            <img id="previewImage" src="" alt=" ">
        </div>
 
        <br>
    <input type="submit" id="submitButton" value="שיתוף" data-he="שיתוף" data-en="Share">
</form>
    <!-- Confirmation Modal -->
    <div id="confirmationModal">
        <h2>Location Confirmation</h2>
        <p>Do you want to use your current location?</p>
        <button type="button" onclick="useCurrentLocation();">Yes, Use Current Location</button>
        <p>Or enter another location:</p>
        <!-- Add an input field for custom location -->
        <label for="customLocation">Custom Location:</label>
        <input type="text" id="customLocation" name="customLocation" placeholder="Enter custom location">
        <br>
        <button type="button" onclick="useCustomLocation();">Save New Location</button>
    </div>
 
 
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

            // Update submit button value
            var submitButton = document.getElementById('submitButton');
            if (submitButton.dataset.he && submitButton.dataset.en) {
                if (submitButton.value === submitButton.dataset.he) {
                    submitButton.value = submitButton.dataset.en;
                } else {
                    submitButton.value = submitButton.dataset.he;
                }
            }
        }
    </script>
</body>
 
</html>