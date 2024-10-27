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
