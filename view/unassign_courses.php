<?php
include_once '../controllers/CourseController.php';
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Instructor') {
    echo "You must be logged in as an instructor.";
    exit;
}
$courseController = new CourseController();
if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];
    $isUnassigned = $courseController->unassignInstructor($courseId);
    $message = $isUnassigned ? "Course unassigned successfully!" : "Failed to unassign course.";
    echo "<script>alert('$message');</script>";
    if ($isUnassigned) {
        header("Location: Instructor.php");
        exit;
    }

}
?>

