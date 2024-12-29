<?php
include_once '../controllers/AdminController.php';
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $usertype = $_POST['usertype'];

    $adminController = new AdminController();
    if ($adminController->addUser($username, $password, $email, $usertype)) {
        echo "<script>alert('User created successfully!');</script>";
        header("Refresh: 0; URL=admin.php");
        exit();
    } else {
        echo "<script>alert('Error adding user.');</script>";
    }
}

?>
