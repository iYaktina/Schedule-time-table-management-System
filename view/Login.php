<?php
include_once '../controllers/UserController.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css"> 
</head>
<body>
    <?php include('menu.php'); ?>

    <div class="login-container">
        <div class="header">
            <h2>Login</h2>
            <img src="../assets/images/star.png" alt="Star Icon" class="page-icon">
        </div>
        
        <form action="" method="POST">
            <label class="coloringclass2" for="text">Name</label>
            <input type="text" id="text" name="Name" placeholder="Username" required>

            <label class="coloringclass2" for="password">Enter password</label>
            <input type="password" id="password" name="password" placeholder="*******" required>

            <button type="submit">Sign In</button>
        </form>

        <p>Create a new account? <a href="Signup.php">Sign Up</a></p>
    </div>

    <?php
    // Handle form submission and login attempt
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST["Name"];
        $password = $_POST["password"];

        // Initialize the controller
        $userController = new UserController();
        $message = $userController->login($username, $password);

        // Show message (success or failure)
        echo "<script>alert('$message');</script>";

        // Redirect if login was successful
        if ($message === "Login Successful!") {
            header("Refresh: 0; URL=index.php");
            exit();
        }
    }
    ?>
</body>
</html>
