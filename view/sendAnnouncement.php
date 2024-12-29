<?php
include_once "../controllers/AdminController.php";
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
$adminController = new AdminController();
if ($adminController->sendScheduleAnnouncement()) {
    echo "Announcement sent successfully!";
} else {
    echo "Failed to send the announcement.";
}
?>
