<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Fetch shared food items for the current user
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM shared_food WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
    } else {
        echo "Error retrieving shared food items: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shared Food</title>
    <link rel="stylesheet" href="css/MyShare.css">
</head>
<body>
<nav>
        <ul class="navbar">
        <li><img src="http://localhost/Project/images/robin.png" width="50" class="nav-link"></li>
            <li><a href="SharePage.php" class="nav-link" data-he="שיתוף אוכל" data-en="Share Food">שיתוף אוכל</a></li>
            <li><a href="UserSelection.html" class="nav-link" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
            <li><a href="logout.php" class="nav-link" data-he="התנתק" data-en="Logout">התנתק</a></li>
            <li><button onclick="toggleTranslation()" data-he="EN" data-en="HE">EN</button></li>
        </ul>
    </nav>


    <h2 class="section-heading" data-he="היסטוריית שיתופים" data-en="Sharing History">היסטוריית שיתופים</h2>
    <table>
        <tr>
            <th data-he="סוג האוכל" data-en="Food Type">סוג האוכל</th>
            <th data-he="קטגוריה" data-en="Category">קטגוריה</th>
            <th data-he="רכיבים" data-en="Ingredients">רכיבים</th>
            <th data-he="תאריך הכנה" data-en="Preparation Date">תאריך הכנה</th>
            <th data-he="תאריך פג תוקף" data-en="Expiry Date">תאריך פג תוקף</th>
            <th data-he="משקל" data-en="Weight">משקל</th>
            <th data-he="תאריך איסוף" data-en="Pickup Date">תאריך איסוף</th>
            <th data-he="שעת איסוף" data-en="Pickup Time">שעת איסוף</th>
            <th data-he="תמונה" data-en="Image">תמונה</th>
            <th data-he="פעולה" data-en="Action">פעולה</th>
        </tr>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?php echo $row['food_name']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['ingredients']; ?></td>
            <td><?php echo $row['making_date']; ?></td>
            <td><?php echo $row['expiry_date']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['booking_date']; ?></td>
            <td><?php echo $row['booking_hour']; ?></td>
            <td><img src="<?php echo $row['photo_path']; ?>" alt="Food Photo" style="width:100px;height:100px;"></td>
                <td>
                    <a href="edit_food.php?id=<?php echo $row['id']; ?>" class="edit-btn" data-he="עריכה" data-en="Edit">עריכה</a>
                    <a href="delete_food.php?id=<?php echo $row['id']; ?>" class="delete-btn" data-he="מחיקה" data-en="Delete">מחיקה</a>
                </td>
            </tr>
    <?php endwhile; ?>
</table>



</body>
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
    // Function to fetch and update notifications
    function fetchNotifications() {
        $.ajax({
            url: 'fetch_notifications.php',
            type: 'GET',
            success: function(response) {
                var count = response.count;
                $('#notificationCount').text(count);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Fetch notifications every 30 seconds
    setInterval(fetchNotifications, 30000);

    // Initial fetch when the page loads
    $(document).ready(function() {
        fetchNotifications();
    });

    // Function to fetch messages
    function fetchMessages() {
        $.ajax({
            url: 'fetch_messages.php',
            type: 'GET',
            success: function(response) {
                // Handle received messages
                // For example, update the UI to display messages
            },
            error: function(xhr, status, error) {
                console.error('Error fetching messages:', error);
            }
        });
    }

    // Fetch messages every 30 seconds
    setInterval(fetchMessages, 30000);

    // Initial fetch when the page loads
    $(document).ready(function() {
        fetchMessages();
    });
</script>
</html>
