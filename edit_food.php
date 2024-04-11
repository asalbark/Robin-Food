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

// Initialize variables
$food_name = $category = $ingredients = $making_date = $expiry_date = $amount = $photo_path = "";
$id = $food_name_err = $category_err = $ingredients_err = $making_date_err = $expiry_date_err = $amount_err = $photo_path_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate ID
    $id = $_POST['id'];

    // Validate Food Name
    if (empty(trim($_POST["foodName"]))) {
        $food_name_err = "Please enter the food name.";
    } else {
        $food_name = trim($_POST["foodName"]);
    }

    // Validate Category
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please enter the category.";
    } else {
        $category = trim($_POST["category"]);
    }

    // Validate Ingredients
    if (empty(trim($_POST["ingredients"]))) {
        $ingredients_err = "Please enter the ingredients.";
    } else {
        $ingredients = trim($_POST["ingredients"]);
    }

    // Validate Making Date
    if (empty(trim($_POST["makingDate"]))) {
        $making_date_err = "Please enter the making date.";
    } else {
        $making_date = trim($_POST["makingDate"]);
    }

    // Validate Expiry Date
    if (empty(trim($_POST["expiryDate"]))) {
        $expiry_date_err = "Please enter the expiry date.";
    } else {
        $expiry_date = trim($_POST["expiryDate"]);
    }

    // Validate Amount
    if (empty(trim($_POST["amount"])) || !is_numeric($_POST["amount"])) {
        $amount_err = "Please enter a valid amount.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    // Validate Photo Path
    if (empty(trim($_POST["photoPath"]))) {
        $photo_path_err = "Please enter the photo path.";
    } else {
        $photo_path = trim($_POST["photoPath"]);
    }

    // Check if there are no errors before updating the record
    if (empty($food_name_err) && empty($category_err) && empty($ingredients_err) && empty($making_date_err) && empty($expiry_date_err) && empty($amount_err) && empty($photo_path_err)) {
        // Prepare an update statement
        $sql = "UPDATE shared_food SET food_name=?, category=?, ingredients=?, making_date=?, expiry_date=?, amount=?, photo_path=? WHERE id=?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssssssi", $food_name, $category, $ingredients, $making_date, $expiry_date, $amount, $photo_path, $id);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to MySharedFood page after successful update
                header("location: MySharedFood.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id =  trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM shared_food WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    // Fetch result row as an associative array
                    $row = $result->fetch_assoc();

                    // Retrieve individual field value
                    $food_name = $row["food_name"];
                    $category = $row["category"];
                    $ingredients = $row["ingredients"];
                    $making_date = $row["making_date"];
                    $expiry_date = $row["expiry_date"];
                    $amount = $row["amount"];
                    $photo_path = $row["photo_path"];
                } else {
                    // URL doesn't contain valid id parameter
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();

        // Close connection
        $conn->close();
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-he="עדכון השיתוף" data-en="Update Sharing">עדכון השיתוף</title>
    <link rel="stylesheet" href="css/edit.css">
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
    <br>
    <br>
    <h2 data-he="עדכון השיתוף" data-en="Update Sharing">עדכון השיתוף</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div>
            <label for="foodName" data-he="סוג האוכל" data-en="Food Type">סוג האוכל</label>
            <input type="text" id="foodName" name="foodName" value="<?php echo $food_name; ?>">
            <span class="error"><?php echo $food_name_err; ?></span>
        </div>
        <div>
            <label for="category" data-he="קטגוריה" data-en="Category">קטגוריה</label>
            <input type="text" id="category" name="category" value="<?php echo $category; ?>">
            <span class="error"><?php echo $category_err; ?></span>
        </div>
        <div>
            <label for="ingredients" data-he="רכיבים" data-en="Ingredients">רכיבים</label>
            <textarea id="ingredients" name="ingredients"><?php echo $ingredients; ?></textarea>
            <span class="error"><?php echo $ingredients_err; ?></span>
        </div>
        <div>
            <label for="makingDate" data-he="תאריך הכנה" data-en="Preparation Date">תאריך הכנה</label>
            <input type="date" id="makingDate" name="makingDate" value="<?php echo $making_date; ?>">
            <span class="error"><?php echo $making_date_err; ?></span>
        </div>
        <div>
            <label for="expiryDate" data-he="תאריך פג תוקף" data-en="Expiry Date">תאריך פג תוקף</label>
            <input type="date" id="expiryDate" name="expiryDate" value="<?php echo $expiry_date; ?>">
            <span class="error"><?php echo $expiry_date_err; ?></span>
        </div>
        <div>
            <label for="amount" data-he="משקל" data-en="Weight">משקל</label>
            <input type="text" id="amount" name="amount" value="<?php echo $amount; ?>">
            <span class="error"><?php echo $amount_err; ?></span>
        </div>
        <div>
            <label for="photoPath" data-he="העלאת תמונה" data-en="Upload Image">העלאת תמונה</label>
            <input type="text" id="photoPath" name="photoPath" value="<?php echo $photo_path; ?>">
            <span class="error"><?php echo $photo_path_err; ?></span>
        </div>
        <div class="center-btn">
            <button type="submit" data-he="עדכן" data-en="Update">עדכן</button>
        </div>
    </form>
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
