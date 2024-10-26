<?php
    include_once "include/dbh.inc.php";
    include('menu.php');

    if (!isset($_SESSION['ID'])) {
        echo "<script>alert('Please Login!');</script>"; 
        header("Refresh: 0; URL=login.php"); 
    }

    if ($_SESSION['Usertype'] == 'User') {
        echo "<script>alert('Classified for instructors and admins only');</script>"; 
        header("Refresh: 0; URL=index.php"); 
    }
?>
