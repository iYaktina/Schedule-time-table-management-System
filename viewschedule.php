<?php
    include_once "include/dbh.inc.php";

     include('menu.php');
    if (!isset($_SESSION['ID'])) {
                echo "<script>alert('Please Login!');</script>"; 
                header("Refresh: 0;URL=login.php");
            }

?>
