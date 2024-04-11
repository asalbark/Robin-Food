<?php
require 'config.php';

if(isset($_POST["submit"])) {
    $UserFname = $_POST["first_name"];
    $UserLname = $_POST["last_name"];
    $User_Phone = $_POST["User_Phone"];
    $User_email = $_POST["User_email"];
    $User_location = $_POST["location"];
    $password = $_POST["password"];

    // Check if email or phone already exist
    $check_duplicate = mysqli_prepare($conn, "SELECT * FROM register WHERE User_email = ? OR User_Phone = ?");
    mysqli_stmt_bind_param($check_duplicate, "ss", $User_email, $User_Phone);
    mysqli_stmt_execute($check_duplicate);
    mysqli_stmt_store_result($check_duplicate);

    if(mysqli_stmt_num_rows($check_duplicate) > 0) {
        echo "<script>alert('Email or Phone Has Already Been Taken');</script>";
    } else {
        // Insert user data into the database
        $insert_query = mysqli_prepare($conn, "INSERT INTO register (User_Fname, User_Lname, User_Phone, User_email, User_location, password) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert_query, "ssssss", $UserFname, $UserLname, $User_Phone, $User_email, $User_location, $password);
        
        if(mysqli_stmt_execute($insert_query)){
            // Redirect to login page
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Error: Registration failed!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>רובין פוד</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="register.css">
    <!-- Google Translate API script -->
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>
    <header>
        <h1>רובין פוד</h1>
    </header>
    <nav>
        <ul class="navbar">
            <li><img src="http://localhost/Project/images/robin.png" class="nav-link" width="35"></li>
            <li><a href="login.php" class="nav-link">התחברות</a></li>
            <li><a href="register.php" class="nav-link">הרשמה</a></li>
            <li><a href="#index.php" class="nav-link">דף ראשי</a></li>
        </ul>
    </nav>
    <form action="" method="post" class="form register-container">
        <h2>הרשמה</h2>
        <div class="input-box">
            <label class="detail">שם פרטי</label>
            <input type="text" placeholder="Enter first name" name="first_name" id="first_name" required>
        </div>
        <div class="input-box">
            <label class="detail">שם משפחה</label>
            <input type="text" placeholder="Enter last name" name="last_name" id="last_name" required>
        </div>
        <div class="input-box">
            <label class="detail">אימייל</label>
            <input type="email" placeholder="Enter Email" name="User_email" id="User_email" required>
            <div id="email_error"></div>
        </div>
        <div class="input-box">
            <label class="detail">עיר</label>
            <input type="text" placeholder="Enter location" name="location" id="location" required>
        </div>
        <div class="input-box">
            <label class="detail">טלפון</label>
            <input type="text" placeholder="Enter Phone Number" name="User_Phone" id="User_Phone" required>
        </div>
        <div class="input-box">
            <label class="detail">סיסמה</label>
            <input type="password" placeholder="Enter password" name="password" id="password" required>
        </div>
        <p>על ידי יצירת חשבון אתה מסכים לנו <a href="#">תנאים ופרטיות</a>.</p>
        <div class="button">
            <button type="submit" name="submit">הרשמה</button>
        </div>
    </form>
</body>
</html>
