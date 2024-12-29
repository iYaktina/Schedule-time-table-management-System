<?php
include_once '../controllers/AdminController.php';
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];     
    $email = $_POST['email'];
    $usertype = $_POST['usertype'];



    $adminController = new AdminController();
    if ($adminController->editUser($userId,$username, $password, $email, $usertype)) {
        echo "<script>alert('User Updated successfully!');</script>";
        header("Refresh: 0; URL=admin.php");
        exit();
    } else {
        echo "<script>alert('Username or email already exists for another user. Please try again.');</script>";
         header("Refresh: 0; URL=admin.php?user_id=$userId");
    }
 
}
?>

