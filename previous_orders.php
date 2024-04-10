<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-he="הזמנות קודמות" data-en="Previous Orders">הזמנות קודמות</title>
    <link rel="stylesheet" href="preOrder.css"> <!-- Assuming you have a CSS file for styling -->
</head>
<body>

<nav>
    <ul class="navbar">
        <li><img src="http://localhost/Project/images/robin.png" class="nav-link" width="50"></li>
        <li><a href="UserSelection.html" class="nav-link" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
        <li><a href="SharePage.php" class="nav-link" data-he="שיתוף אוכל" data-en="Share Food">שיתוף אוכל</a></li>
        <li><a href="logout.php" class="nav-link" data-he="התנתק" data-en="Logout">התנתק</a></li>
        <li><button onclick="toggleTranslation()" data-he="EN" data-en="HE">EN</button></li>
    </ul>
</nav>
<br>
<br>

<h1 data-he="הזמנות קודמות" data-en="Previous Orders">הזמנות קודמות</h1>

<br>
<br>

<?php 
session_start();

// Include the database connection or config file
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Fetch previous orders for the current user from the database
$user_id = $_SESSION['id'];
$sql = "SELECT orders.*, shared_food.* FROM orders
        INNER JOIN shared_food ON orders.food_id = shared_food.id
        WHERE orders.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<?php if ($result->num_rows > 0): ?>
    <div class="orders-container">
        <?php $count = 0; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php if ($count % 3 == 0): ?>
                <div class="row">
            <?php endif; ?>
            <div class="order-item card">
                <h3 class="order-id" data-he="מספר הזמנה <?php echo $row['id']; ?>" data-en="Order ID <?php echo $row['id']; ?>">מספר הזמנה <?php echo $row['id']; ?></h3>
                <p class="food-info" data-he="סוג האוכל: <?php echo $row['food_name']; ?>" data-en="Food Type: <?php echo $row['food_name']; ?>">סוג האוכל: <?php echo $row['food_name']; ?></p>
                <p class="food-info" data-he="קטגוריה: <?php echo $row['category']; ?>" data-en="Category: <?php echo $row['category']; ?>">קטגוריה: <?php echo $row['category']; ?></p>
                <p class="food-info" data-he="רכיבים: <?php echo $row['ingredients']; ?>" data-en="Ingredients: <?php echo $row['ingredients']; ?>">רכיבים: <?php echo $row['ingredients']; ?></p>
                <p class="food-info" data-he="תאריך הכנה: <?php echo $row['making_date']; ?>" data-en="Preparation Date: <?php echo $row['making_date']; ?>">תאריך הכנה: <?php echo $row['making_date']; ?></p>
                <p class="food-info" data-he="תאריך פג תוקף: <?php echo $row['expiry_date']; ?>" data-en="Expiry Date: <?php echo $row['expiry_date']; ?>">תאריך פג תוקף: <?php echo $row['expiry_date']; ?></p>
                <p class="food-info" data-he="תאריך איסוף: <?php echo $row['booking_date']; ?>" data-en="Pickup Date: <?php echo $row['booking_date']; ?>">תאריך איסוף: <?php echo $row['booking_date']; ?></p>
                <p class="food-info" data-he="משקל:<?php echo $row['amount']; ?> גרם" data-en="Weight:<?php echo $row['amount']; ?> grams">משקל:<?php echo $row['amount']; ?> גרם</p>
                <?php if (!empty($row['photo_path'])): ?>
                    <img src="<?php echo $row['photo_path']; ?>" alt="Food Photo" class="food-photo">
                <?php else: ?>
                    <p data-he="תמונת המזון לא זמינה" data-en="Food photo not available">תמונת המזון לא זמינה</p>
                <?php endif; ?>
                
                <!-- Add the "Receive" button -->
            </div>

            <?php if (($count + 1) % 3 == 0 || ($count + 1) == $result->num_rows): ?>
                </div>
            <?php endif; ?>
            <?php $count++; ?>
        <?php endwhile; ?>
    </div>

    <script>
        // Add event listener to "Receive" buttons
        document.querySelectorAll('.receive-btn').forEach(button => {
            button.addEventListener('click', function() {
                console.log("Button clicked"); // Check if the event listener is triggered

                // Get the food ID from the button's data attribute
                const foodId = this.getAttribute('data-food-id');
                
                // Send an AJAX request to delete the food item
                fetch('food_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ foodId: foodId })
                })
                .then(response => {
                    if (response.ok) {
                        // If deletion is successful, remove the order item from the UI
                        this.closest('.order-item').remove();
                    } else {
                        // Handle error
                        console.error('Failed to delete food item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
<?php else: ?>
    <p data-he="אין הזמנות קודמות" data-en="No previous orders">אין הזמנות קודמות</p>
<?php endif; ?>

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
</body>
</html>
