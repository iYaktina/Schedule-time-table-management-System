        <?php
            include_once "include/dbh.inc.php";
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
            <link rel="stylesheet" href="./css/login.css"> 
        </head>
        <body>
            <?php include('menu.php'); ?>

            <div class="login-container">
                        <div class="header">
                <h2>Login</h2>
                 <img src="./images/star.png" alt="Star Icon" class="page-icon">
                 </div>
                <form action="" method="POST" onsubmit=""> 

                    <label class="coloringclass2" for="text">Name</label>
                    <input type="text" id="text" name="Name" placeholder="Username" required>


                    <label class="coloringclass2" for="password">Enter password</label>
                    <input type="password" id="password" name="password" placeholder="*******" required>
                    <button type="submit">Sign In</button>
                </form>
                <p>Create a new account ? <a href="Signup.php">Sign Up</a></p>
            </div>

            <?php
       if($_SERVER["REQUEST_METHOD"]=="POST"){

         $Username=$_POST["Name"];
         $Password=$_POST["password"];
       
      
    
       
       $sql = "SELECT * FROM user WHERE Username='$Username' AND Password='$Password'";
       $result = $conn->query($sql);
       
       if ($result->num_rows > 0) {
           $user = $result->fetch_assoc(); 
           $_SESSION['ID'] = $user['ID'];
           $_SESSION['Name'] = $user['Username'];
           $_SESSION['Email'] = $user['Email'];
           $_SESSION['Password'] = $user['Password'];
           $_SESSION['Usertype']= $user['Usertype'];
           echo "<script>alert('Login Successful!');</script>"; 
           header("Refresh: 0;URL=index.php"); 
       } else {
        echo "<script>alert('Invalid Username or Password');</script>"; 
       }
    }

        ?>
        </body>
        </html>
