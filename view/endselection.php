<?php
include_once "../controllers/AdminController.php";
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminController = new AdminController();

    // Call the function to end schedule selection
    $success = $adminController->endScheduleSelection();

    // Return JSON response
    echo json_encode(['success' => $success]);
    exit;
}

// If accessed without POST, redirect
header("Location: dashboard.php");
exit;
