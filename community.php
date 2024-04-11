<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-he="רובין פוד" data-en="Robin Food">רובין פוד</title>
    <link rel="stylesheet" href="css/community.css">
    <!-- Google Translate API script -->
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit&hl=he"></script>
    <!-- Include SweetAlert2 for displaying messages -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Include Email.js library -->
    <script src="https://cdn.emailjs.com/dist/email.min.js"></script>
</head>
<body>

    <header class="nav">
        <div class="nav-menu" id="navMenu">
            <ul>
        
        <li><img src="http://localhost/Project/images/robin.png" width="50" class="nav-link"></li>
                <li><a href="index.php" class="link" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
                <li><a href="about.php" class="link" data-he="אודות" data-en="About">אודות</a></li>
                <li><a href="community.php" class="link active" data-he="הקהילה שלנו" data-en="Our Community">הקהילה שלנו</a></li>
                <li><button onclick="toggleTranslation()" class="link" data-he="EN" data-en="HE">EN</button></li>
            </ul>
        </div>
    </header>

    <div class="wrapper">
        <div class="content-container">
            <div class="text-section">
                <h2 data-he="ברוכים הבאים לקהילת רובין פוד - המקום המושלם עבור כל מי שאוהב את האוכל ורוצה לחלוק את הפשטות והגאונות שבעולם הקולינריה! כאן תוכלו למצוא השראה, לשתף רעיונות, לקבל טיפים ולהתמיד עם חברים שאוהבים לבשל ולטעום כמוך. אנו מזמינים אתכם להשפיע, לשתף ולהתנסות במגוון המטבחים והטעמים מרחבי העולם. אין דבר יותר מרגש מלגלות חוויות קולינריות חדשות יחד עם חברים כאלה שאוהבים אותו דבר. אז בואו, נתחיל לבשל ולחלוק את הכישרון יחד! 🍳🌮🥗" data-en="Welcome to the Robin Food community - the perfect place for anyone who loves food and wants to share the simplicity and genius of the culinary world! Here you can find inspiration, share ideas, get tips, and connect with friends who love to cook and taste like you. We invite you to influence, share, and experience a variety of cuisines and flavors from around the world. There's nothing more exciting than discovering new culinary experiences with friends who love the same thing. So let's start cooking and sharing our talent together! 🍳🌮🥗">ברוכים הבאים לקהילת רובין פוד - המקום המושלם עבור כל מי שאוהב את האוכל ורוצה לחלוק את הפשטות והגאונות שבעולם הקולינריה! כאן תוכלו למצוא השראה, לשתף רעיונות, לקבל טיפים ולהתמיד עם חברים שאוהבים לבשל ולטעום כמוך. אנו מזמינים אתכם להשפיע, לשתף ולהתנסות במגוון המטבחים והטעמים מרחבי העולם. אין דבר יותר מרגש מלגלות חוויות קולינריות חדשות יחד עם חברים כאלה שאוהבים אותו דבר. אז בואו, נתחיל לבשל ולחלוק את הכישרון יחד! 🍳🌮🥗</h2>
            </div>
        </div>
    </div>

    <div class="feedback-section">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"] ?? '';
        $opinion = $_POST["opinion"] ?? '';
        $rating = isset($_POST["rating"]) ? $_POST["rating"] : '';
        $email = $_POST["email"] ?? '';

        if (!empty($name) && !empty($opinion) && !empty($rating) && !empty($email)) {
            // Display a message indicating feedback was submitted
            echo "<script>alert('Your feedback was submitted successfully!');</script>";
            // Redirect the user to prevent form resubmission on page refresh
            echo "<script>window.location.href = window.location.pathname;</script>";
            exit(); // Terminate script execution
        } else {
            // Display a message indicating missing fields
            echo "<script>alert('Please fill out all fields.');</script>";
        }
    }
?>

    
    </div>
 
    <div class="feedback-form">
        <h2 data-he="הוסף פידבק:" data-en="Add Feedback:">הוסף פידבק:</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="name" data-he="שם:" data-en="Name:">שם:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label for="opinion" data-he="דעתך על החוויה:" data-en="Your opinion about the experience:">דעתך על החוויה:</label><br>
            <textarea id="opinion" name="opinion" rows="4" cols="50" required></textarea><br><br>
            <label for="email" data-he="אימייל:" data-en="Email:">אימייל:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="rating" data-he="דרג את החוויה:" data-en="Rate the experience:">דרג את החוויה:</label><br>
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5">5 stars</label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">4 stars</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">3 stars</label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">2 stars</label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">1 star</label>
            </div><br><br>
            <input type="submit" value="שלח פידבק" data-he="שלח פידבק" data-en="Send Feedback" onclick="sendFeedback()">
        </form>
    </div>

    <div class="connect-us-section">
        <h2 data-he="פרטי התקשרות:" data-en="Contact details">פרטי התקשרות:</h2>
        <p data-he="כתובת: רח' אבא הילל סילבר 15, רמת גן" data-en="Address: 15 Abba Hillel Silver St., Ramat Gan">כתובת: רח' אבא הילל סילבר 15, רמת גן</p>
        <p data-he="טלפון: 03-7305333" data-en="Phone: 03-7305333">טלפון: 03-7305333</p>
        <p data-he="דואר אלקטרוני: contact@robin-food.com" data-en="Email: contact@robin-food.com">דואר אלקטרוני: contact@robin-food.com</p>
    </div>
 
    <script>
        function toggleTranslation() {
            var isHebrew = document.documentElement.lang === 'he';
            var elements = document.querySelectorAll('[data-he], [data-en]');
            elements.forEach(element => {
                if (isHebrew) {
                    element.innerText = element.getAttribute('data-en');
                } else {
                    element.innerText = element.getAttribute('data-he');
                }
            });
            // Toggle the language attribute of the document
            document.documentElement.lang = isHebrew ? 'en' : 'he';
        }
        function sendFeedback() {
        swal({
            title: 'Sending Feedback',
            text: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            onOpen: () => {
                document.querySelector('.swal2-actions').innerHTML = '';
            }
        });
        
        setTimeout(() => {
            swal('Success', 'Your feedback was sent!', 'success');
        }, 2000); // Simulating sending feedback for demo purpose
    }
    </script>

</body>
</html>