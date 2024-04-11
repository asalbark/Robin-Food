<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM register WHERE User_email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row['id']; // Assuming 'id' is the column name for user ID in the database
        $_SESSION['email'] = $email;
        
        // Redirect user to appropriate page based on user type
        if ($row['isAdmin'] == 1) {
            header("Location: adminPage.php");
        } else {
            header("Location: UserSelection.html");
        }
        exit;
    } else {
        // Invalid email or password
        echo "<script>alert('Invalid email or password');</script>";
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
    <link rel="stylesheet" href="login.css">
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
        <form action="login.php" method="post" class="form login-container">
            <h2>התחברות</h2>
            <div class="input-box">
                <label class="detail">דואר אלקטרוני</label>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <label class="detail">סיסמה</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="button">
                <button type="submit">התחברות</button>
            </div>
        </form>
        
        
    </div>
</body>
</html>