<?php
include 'config.php';

// Your PHP code here
?>
<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin.css">
</head>
<header class="header">
        <h1 class="header-title">מרכז השליטה של המנהל</h1>
    </header>
<body>
 
 
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <img src="images/robin.png" alt="Image Description" class="logo-img">
                <h1 class="logo-text">רובין פוד</h1>
            </div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showUsersTable()">
                        <i class="fas fa-user nav-icon"></i>
                        <span class="nav-text">משתמשים</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showFoodSharesTable()">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <span class="nav-text">שיתופי האוכל</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showUserGraph()">
                        <i class="fas fa-tasks nav-icon"></i>
                        <span class="nav-text">מיקום המשתמשים</span>
                    </a>
                </li>
                <li class="nav-item">
    <a href="#" class="nav-link" onclick="logout()">
        <i class="fas fa-tasks nav-icon"></i>
        <span class="nav-text">התנתק</span>
    </a>
</li>
        </nav>
 
        <div class="content">
            <div id="usersTable">
                <!-- Table for users -->
                <table class="data-table">
                    <!-- Table headings -->
                    <tr>
                        <th>מספר משתמש</th>
                        <th>שם פרטי</th>
                        <th>שם משפחה</th>
                        <th>אימייל</th>
                        <th>טלפון</th>
                    </tr>
                    <!-- PHP loop for user data -->
                    <?php
                    // Query to select all users
                    $sql_users = "SELECT * FROM register";
                    $result_users = $conn->query($sql_users);
 
                    if ($result_users) {
                        if ($result_users->num_rows > 0) {
                            // Output data of each row
                            while($row = $result_users->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["User_Fname"] . "</td>";
                                echo "<td>" . $row["User_Lname"] . "</td>";
                                echo "<td>" . $row["User_email"] . "</td>";
                                echo "<td>" . $row["User_Phone"] . "</td>";
                                // Add more <td> tags to display other user details as needed
                                echo "</tr>";
                            }
                            $total_users = $result_users->num_rows;
                        } else {
                            echo "<tr><td colspan='5'>0 results</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Error retrieving users</td></tr>";
                    }
                    ?>
                </table>
 
                <!-- Placeholder for total users -->
                <div class="total-users">סה"כ משתמשים: <?php echo $total_users; ?></div>
            </div>
 
            <div id="foodSharesTable" style="display: none;">
                <!-- Table for food shares -->
                <table class="data-table">
                    <!-- Table headings -->
                    <tr>
                    <th>מספר</th>
            <th>מספר משתמש</th>
            <th>שם האוכל</th>
            <th>קטגוריה</th>
            <th>רכיבים</th>
            <th>תאריך הכנה</th>
            <th>תאריך פג תוקף</th>
            <th>משקל</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>תמונה</th>
                    </tr>
                    <?php
                    // Query to select all food shares
                    $sql_food_shares = "SELECT * FROM shared_food";
                    $result_food_shares = $conn->query($sql_food_shares);
 
                    if ($result_food_shares) {
                        if ($result_food_shares->num_rows > 0) {
                            // Output data of each row
                            while($row = $result_food_shares->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["user_id"] . "</td>";
                                echo "<td>" . $row["food_name"] . "</td>";
                                echo "<td>" . $row["category"] . "</td>";
                                echo "<td>" . $row["ingredients"] . "</td>";
                                echo "<td>" . $row["making_date"] . "</td>";
                                echo "<td>" . $row["expiry_date"] . "</td>";
                                echo "<td>" . $row["amount"] . "</td>";
                                echo "<td>" . $row["latitude"] . "</td>";
                                echo "<td>" . $row["longitude"] . "</td>";
                                echo "<td><img src='" . $row["photo_path"] . "' width='100' height='100'></td>";
                                echo "<td><button onclick='deleteFoodItem(" . $row["id"] . ")' class='delete-btn'>Delete</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>0 results</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>Error retrieving food shares</td></tr>";
                    }
                    ?>
                </table>
            </div>
 
           
            <div id="userGraph" style="display: none;">
  <h2 class="section-title">מיקום המשתמשים</h2>
  <!-- Canvas for user graph -->
  <canvas id="userGraphCanvas"></canvas>
</div>
 
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Initialize a map centered at a specific location (e.g., Israel)
    var map = L.map('userMap').setView([31.0461, 34.8516], 8); // Israel coordinates, zoom level 8

    // Add tile layer (map tiles)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Create markers for each user location
    <?php
    foreach ($locations as $index => $location) {
        // Example latitude and longitude (replace with actual data)
        $latitude = 31.0461;
        $longitude = 34.8516;
        echo "L.marker([$latitude, $longitude]).addTo(map)";
        echo ".bindPopup('" . addslashes($location) . "');"; // Use location as marker popup content
    }
    ?>
</script>

            </div>
        </div>
    </div>
               
 
    <script>
        function deleteFoodItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    // Check if the response indicates success
                    if (xhr.responseText === 'success') {
                        // Reload the page after successful deletion
                        location.reload();
                    } else {
                        // Handle error
                        alert(xhr.responseText);
                    }
                } else {
                    // Handle network or server error
                    alert('Error deleting item.');
                }
            }
        };
        xhr.open("GET", "delete_food_item.php?id=" + itemId, true);
        xhr.send();
    }
}

        function showUsersTable() {
            document.getElementById("usersTable").style.display = "block";
            document.getElementById("foodSharesTable").style.display = "none";
            document.getElementById("userGraph").style.display = "none";
        }
 
        function showFoodSharesTable() {
            document.getElementById("usersTable").style.display = "none";
            document.getElementById("foodSharesTable").style.display = "block";
            document.getElementById("userGraph").style.display = "none";
        }
 
        function showUserGraph() {
            document.getElementById("usersTable").style.display = "none";
            document.getElementById("foodSharesTable").style.display = "none";
            document.getElementById("userGraph").style.display = "block";
        }
        function logout() {
    // Send an AJAX request to the logout PHP script
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                // Redirect to the login page after logout
                window.location.href = 'index.php';
            } else {
                // Handle error
                alert('Error logging out.');
            }
        }
    };
    xhr.open('GET', 'logout.php', true);
    xhr.send();
}


       
    </script>
</body>
</html>
 
<?php
$conn->close();
?>