<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Navigation</title>
    <link rel="stylesheet" href="./css/menu.css"> 
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">STMS</a> 
        </div>
        <ul class="nav-links">
       <?php
            if (isset($_SESSION['ID'])) {
                if($_SESSION['Usertype']=='User'){
                echo '
                    <li><a href="profile.php">View Profile</a></li>
                    <li><a href="schedule.php">View Schedule</a></li>
                    <li><a href="logout.php">Logout</a></li>
                ';
                }
                else   if($_SESSION['Usertype']=='Instructor'){
                    echo '
                        <li><a href="Instructor.php">Instructor Page</a></li>
                        <li><a href="profile.php">View Profile</a></li>
                        <li><a href="schedule.php">View Schedule</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    ';
                    }
                else{
                    echo '
                    <li><a href="admin.php">Admin Page</a></li>
                    <li><a href="profile.php">View Profile</a></li>
                    <li><a href="schedule.php">View Schedule</a></li>
                    <li><a href="logout.php">Logout</a></li>
                ';

                }
            }
