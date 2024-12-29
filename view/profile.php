        <?php
            include_once "../controllers/UserController.php";
            include('menu.php');

            if (!isset($_SESSION['ID'])) {
                echo "<script>alert('Please Login!');</script>"; 
                header("Refresh: 0;URL=login.php"); 
            }
          
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Profile</title>
           <link rel="stylesheet" href="../assets/css/profile.css">
            <script>
        
      function toggleEdit(fieldId, hiddenFieldId) {
    const field = document.getElementById(fieldId);
    const hiddenField = document.getElementsByName(hiddenFieldId)[0];

    if (field.disabled) {
        field.disabled = false; 
        field.focus();
    } else {
        field.disabled = true; 
        field.value = hiddenField.value; 
    }
}

function toggleEditAndPasswordVisibility(fieldId, hiddenFieldId) {
    const field = document.getElementById(fieldId);
    const hiddenField = document.getElementsByName(hiddenFieldId)[0];
    const toggleButton = document.getElementById('togglePassword');

    if (field.disabled) {
        field.disabled = false; 
        field.focus();
        if (fieldId === 'passwordField') {
            field.type = 'text'; 
            toggleButton.innerHTML = '👁️'; 
        }
    } else {
        field.disabled = true; 
        field.value = hiddenField.value; 
        if (fieldId === 'passwordField') {
            field.type = 'password'; 
            toggleButton.innerHTML = '👁️'; 
        }
    }
}

function validatePassword() {
    const passwordField = document.getElementById('passwordField');

    if (passwordField.value.length < 6) {
        alert('Password must be at least 6 characters long.');
        passwordField.focus(); 
        return false; 
    }
    return true; 
}

    </script>
        </head>
        <body>
         
  <div class="profile-container">
        <div class="header">
            <h2>Profile</h2>
            <img src="../assets/images/icons.png" alt="Profile Icon" class="profile-icon"> 
        </div>

      <form action="" method="POST" onsubmit="return validatePassword();">
    <div class="input-group">
        <label for="name">Name</label>
        <input type="text" id="nameField" name="Name" value="<?php echo $_SESSION['Name']; ?>" disabled>
        <input type="hidden" name="hiddenName" value="<?php echo $_SESSION['Name']; ?>">
        <button type="button" class="edit-icon" onclick="toggleEdit('nameField', 'hiddenName')">✏️</button>
    </div>

    <div class="input-group">
        <label for="email">Email</label>
        <input type="text" id="emailField" name="Email" value="<?php echo $_SESSION['Email']; ?>" disabled>
        <input type="hidden" name="hiddenEmail" value="<?php echo $_SESSION['Email']; ?>">
        <button type="button" class="edit-icon" onclick="toggleEdit('emailField', 'hiddenEmail')">✏️</button>
    </div>

  <div class="input-group">
    <label for="password">Password</label>
    <input type="password" id="passwordField" name="Password" value="********" disabled>
    <input type="hidden" name="hiddenPassword" value="password">
    <button type="button" class="eye-icon" id="togglePassword" onclick="toggleEditAndPasswordVisibility('passwordField', 'hiddenPassword')">👁️</button>
</div>

    <button type="submit" id="editButton">Confirm Edit</button> 
</form>

    </div>

    <?php
            try {
                $userController = new UserController();

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $userID = $_SESSION['ID'];
                    $newName = $_POST["Name"] ?? $_SESSION['Name'];
                    $newEmail = $_POST["Email"] ?? $_SESSION['Email'];
                     $newPassword = isset($_POST["Password"]) ? $_POST["Password"] : null;

                    // Determine if the password needs to be updated
                    if ($newPassword === "********") {
                        $newPassword = null; // No password update
                    }

                    if ($userController->updateUserProfile($userID, $newName, $newEmail, $newPassword)) {
                        $_SESSION['Name'] = $newName;
                        $_SESSION['Email'] = $newEmail;
                        echo "<script>alert('Profile updated successfully!');</script>";
                        exit();
                    }
                       header("Refresh: 0;URL=profile.php");
                       exit();
                }
            } catch (Exception $e) {
                echo "<script>alert('" . $e->getMessage() . "');</script>";
            }
?>

        </body>
        </html>
