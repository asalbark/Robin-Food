<!DOCTYPE html>
<html dir="rtl" lang="He">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>רובין פוד</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <!-- Google Translate API script -->
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit&hl=he"></script>
 
</head>
<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo img">
                <img src="images\robin.png" alt="Image Description">
            </div>
            <div class="nav-menu" id="navMenu">
    <ul>
        <li><a href="#" class="link active" data-he="דף ראשי" data-en="Home">דף ראשי</a></li>
        <li><a href="about.php" class="link" data-he="אודות" data-en="About">אודות</a></li>
        <li><a href="community.php" class="link" data-he="הקהילה שלנו" data-en="Our Community">הקהילה שלנו</a></li>
        <li><button onclick="toggleTranslation()" data-he="EN" data-en="HE">EN</button></li>
    </ul>
</div>

            <div class="nav-button">
                <button class="btn white-btn" id="login" onclick="login()" data-he="התחברות" data-en="Login">התחברות</button>
                <button class="btn" id="register" onclick="register()" data-he="הרשמה" data-en="register">הרשמה </button>
            </div>
        </nav>
        <!-- Rest of your content -->
        <div class="form-box">
        <!-- Login form -->
        <form action="login.php" method="post" class="form login-container">
            <h2 data-he="התחברות" data-en="Login">התחברות</h2>
            <div class="input-box">
                <label class="detail" data-he="דואר אלקטרוני" data-en="Email">דואר אלקטרוני</label>
                <div class="icon-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
            </div>
            <div class="input-box">
                <label class="detail" data-he="סיסמה" data-en="Password">סיסמה</label>
                <div class="icon-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
            </div>
            <div class="button">
                <button type="submit" data-he="התחברות" data-en="Login">התחברות</button>
            </div>
        </form>
            <!-- Register form -->
            <form action="register.php" method="post" class="form register-container">
    <h2 data-he="הרשמה" data-en="Register">הרשמה</h2>
    <div class="input-box">
        <label class="detail" data-he="שם פרטי" data-en="First Name">שם פרטי</label>
        <div class="icon-container">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Enter first name" name="first_name" id="first_name" required>
        </div>
    </div>
    <div class="input-box">
        <label class="detail" data-he="שם משפחה" data-en="Last Name">שם משפחה</label>
        <div class="icon-container">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Enter last name" name="last_name" id="last_name" required>
        </div>
    </div>
    <div class="input-box">
        <label class="detail" data-he="אימייל" data-en="Email">אימייל</label>
        <div class="icon-container">
            <i class="fas fa-envelope"></i>
            <input type="email" placeholder="Enter Email" name="User_email" id="email" required>
        </div>
        <div id="email_error"></div>
    </div>
    <div class="input-box">
        <label class="detail" data-he="עיר" data-en="Location">עיר</label>
        <div class="icon-container">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" placeholder="Enter location" name="location" id="location" required>
        </div>
    </div>
    <div class="input-box">
        <label class="detail" data-he="טלפון" data-en="Phone Number">טלפון</label>
        <div class="icon-container">
            <i class="fas fa-phone"></i>
            <input type="text" placeholder="Enter Phone Number" name="User_Phone" id="User_Phone" required>
        </div>
    </div>
    <div class="input-box">
        <label class="detail" data-he="סיסמה" data-en="Password">סיסמה</label>
        <div class="icon-container">
            <i class="fas fa-lock"></i>
            <input type="password" placeholder="Enter password" name="password" id="password" required>
        </div>
    </div>
    <div class="button">
        <button type="submit" name="submit" data-he="הרשמה" data-en="Register">הרשמה</button>
    </div>
</form>

  

    <script>
        function toggleTranslation() {
            var elements = document.querySelectorAll('[data-he], [data-en]');
            elements.forEach(function(element) {
                if (element.getAttribute('lang') === 'he') {
                    element.textContent = element.getAttribute('data-en');
                    element.setAttribute('lang', 'en');
                } else {
                    element.textContent = element.getAttribute('data-he');
                    element.setAttribute('lang', 'he');
                }
            });
        }
    </script>
 
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Clear login form fields on page load
        var loginForm = document.querySelector(".login-container form");
        var registerForm = document.querySelector(".register-container form");
        
        loginForm.reset();
        registerForm.reset();

        // Hide login and register forms on page load
        document.querySelector(".login-container").style.display = "none";
        document.querySelector(".register-container").style.display = "none";
    });

    function myMenuFunction() {
        var i = document.getElementById("navMenu");
        if (i.className === "nav-menu") {
            i.className += " responsive";
        } else {
            i.className = "nav-menu";
        }
    }

    function login() {
        var x = document.querySelector(".login-container");
        var y = document.querySelector(".register-container");
        var a = document.getElementById("login");
        var b = document.getElementById("register");
        x.style.left = "4px";
        y.style.right = "-520px";
        a.classList.add("white-btn");
        b.classList.remove("white-btn");
        y.style.opacity = 0;  // Add this line to hide the register form
        x.style.opacity = 1;
        // Show login form when login button is clicked
        x.style.display = "block";
        // Hide register form when login button is clicked
        y.style.display = "none";
    }

    function register() {
        var x = document.querySelector(".login-container");
        var y = document.querySelector(".register-container");
        var a = document.getElementById("login");
        var b = document.getElementById("register");
        x.style.left = "-510px";
        y.style.right = "5px";
        a.classList.remove("white-btn");
        b.classList.add("white-btn");
        x.style.opacity = 0;  // Add this line to hide the login form
        y.style.opacity = 1;
        // Show register form when register button is clicked
        y.style.display = "block";
        // Hide login form when register button is clicked
        x.style.display = "none";
    }

    // Google Translate initialization function
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({ pageLanguage: 'he', includedLanguages: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE }, 'google_translate_element');
    }
</script>

 
</body>
</html>