<?php
include_once '../controllers/AdminController.php';
session_start();
  if (!isset($_SESSION['ID'])) {
        echo "<script>alert('Please Login!');</script>";
        header("Refresh: 0;URL=login.php");
    }
    if($_SESSION['Usertype'] != 'Admin'){
        echo "<script>alert('Classified for admins only');</script>";
        header("Refresh: 0;URL=index.php");
    }

if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];
    $adminController = new AdminController();
    $result = $adminController->deleteSchedule($schedule_id);

    if ($result) {
        echo "<script>alert('Schedule deleted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to delete schedule.');</script>";
    }
    header("Refresh: 0; URL=scheduleadmin.php");
    exit;
}
 else {
    echo "<script>alert('No schedule was selected.');</script>";
    header("Refresh: 0; URL=scheduleadmin.php");
    exit;
}

?>
