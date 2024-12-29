<?php
include_once '../controllers/UserController.php';  // Include UserController to handle user logic
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../assets/css/signup.css"> 
</head>
<script>
function validate() {
    var pw = document.getElementById("password").value;
    var cpw = document.getElementById("confirm-password").value;
    
    if (pw !== cpw ) {
        document.getElementById("ttt").innerText = "Password doesn't match";
        return false;
    } else if(pw.length < 6) {
        document.getElementById("ttt").innerText = "Password must be at least 6 characters";
        return false;
    } else {
        return true;
    }
}
</script>
<body>
    <?php include('menu.php'); ?>

    <div class="signup-container">
        <div class="header">
            <h2>Sign Up</h2>
            <img src="../assets/images/star.png" alt="Star Icon" class="page-icon">
        </div>
        <form action="" method="POST" onsubmit="return validate()">

            <label class="coloringclass2" for="text">Name</label>
            <input type="text" id="text" name="Name" placeholder="Username" required>

            <label class="coloringclass2" for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="example@gmail.com" required>

            <label class="coloringclass2" for="password">Create a password</label>
            <input type="password" id="password" name="password" placeholder="must be 6 characters" required>

            <label class="coloringclass2" for="confirm-password">Confirm password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="repeat password" required>

            <div class="checkbox-section">
                <div class="checkbox-item">
                    <input type="checkbox" id="daily-reports" name="daily-reports">
                    <label class="coloringclass2" for="daily-reports">Daily reports <div class="coloringclass"> Get a daily activity report via email</div></label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="weekly-summary" name="weekly-summary" checked>
                    <label for="weekly-summary">Weekly summary <div class="coloringclass"> Get a weekly activity report via email</div></label>
                </div>
            </div>
            <label id="ttt"></label><br><br>
            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Gather form data
        $username = htmlspecialchars($_POST["Name"]);
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        $confirmPassword = htmlspecialchars($_POST["confirm-password"]);

        // Initialize UserController
        $userController = new UserController();

        // Call the method to register the user
        $userController->registerUser($username, $email, $password, $confirmPassword);
    }
    ?>
</body>
</html>
