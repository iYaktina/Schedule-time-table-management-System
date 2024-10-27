<?php
            include_once "include/dbh.inc.php";
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
           <link rel="stylesheet" href="./css/profile.css">
            <script>
        
       function toggleEdit(fieldId) {
            const field = document.getElementById(fieldId);
            field.disabled = !field.disabled; 
            if (!field.disabled) {
                field.focus();
            }
        }
    </script>
        </head>
 <body>
         
  <div class="profile-container">
        <div class="header">
            <h2>Profile</h2>
            <img src="./images/22.png" alt="Profile Icon" class="profile-icon"> 
        </div>

        <form action="" method="POST">
            <div class="input-group">
                <label for="name">Name</label>
                <input type="text" id="nameField" name="Name" value="<?php echo $_SESSION['Name']; ?>" disabled>
                <button type="button" class="edit-icon" onclick="toggleEdit('nameField')">✏️</button>
            </div>

            <div class="input-group">
                <label for="id">ID</label>
                <input type="text" id="idField" name="ID" value="<?php echo $_SESSION['ID']; ?>" disabled>
                <button type="button" class="edit-icon" onclick="toggleEdit('idField')">✏️</button>
            </div>

            <button type="submit" id="editButton">Confirm Edit</button> 
        </form>
    </div>
