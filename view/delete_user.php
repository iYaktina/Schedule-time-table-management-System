<?php
include_once "../controllers/AdminController.php";

session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    $adminController = new AdminController();

    if ($adminController->deleteUser($userId)) {
        echo "<script>alert('User deleted successfully!');</script>";
        header("Refresh: 0; URL=admin.php");
    } else {
        echo "<script>alert('Error deleting user.');</script>";
    }
} else {
    echo "<script>alert('Invalid request. No user selected to delete.');</script>";
}
?>
