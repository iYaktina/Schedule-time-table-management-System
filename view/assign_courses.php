<?php
include_once '../controllers/CourseController.php';
session_start();
if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Instructor') {
    echo "You must be logged in as an instructor.";
    exit;
}

$courseController = new CourseController();
$courses = $courseController->getCoursesWithoutInstructor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = $_POST['course'];
    $instructorId = $_SESSION['ID'];
    $instructorName = $_SESSION['Name'];

    $isAssigned = $courseController->assignInstructor($courseId, $instructorId, $instructorName);

    $message = $isAssigned ? "Course assigned successfully!" : "Failed to assign course.";
    echo "<script>alert('$message');</script>";
    if($isAssigned)
        header("Location: Instructor.php");

}
?>

