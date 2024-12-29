<?php
include_once '../controllers/CourseController.php';

session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];
    $courseController = new CourseController();
    $courseController->deleteCourse($courseId);
} else {
    echo "<script>alert('Invalid request.');</script>";
}
?>
