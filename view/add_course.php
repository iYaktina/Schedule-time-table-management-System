<?php
include_once '../controllers/CourseController.php';
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Course Details
    $courseName = $_POST['course_name'];
    $year = $_POST['year'];
    $courseDuration = $_POST['courseDuration'];
    $courseTime = $_POST['courseTime'];
    $courseRoom = $_POST['courseRoom'];
    $creditHours = $_POST['creditHours'];
    $selected_day = $_POST['day'];
    $courseCode = $_POST['courseCode'];
    $prerequisiteId = isset($_POST['prerequisiteId']) ? $_POST['prerequisiteId'] : null; // Default to null if not set

    // Optional Lab and Second Lecture Details
    $hasLab = isset($_POST['hasLab']) ? true : false;
    $labTime = $hasLab ? $_POST['labTime'] : null;
    $labDuration = $hasLab ? $_POST['labDuration'] : null;
    $labday= $hasLab ? $_POST['labday'] : null;
    $labRoom= $hasLab ? $_POST['labRoom'] : null;

    $hasSecondLecture = isset($_POST['secondLecture']) ? true : false;
    $secondLectureTime = $hasSecondLecture ? $_POST['secondLectureTime'] : null;
    $secondLectureDuration = $hasSecondLecture ? $_POST['secondLectureDuration'] : null;
    $secondlecday = $hasSecondLecture ? $_POST['secondlecday'] : null;
    $secondlecRoom = $hasSecondLecture ? $_POST['secondLectureRoom'] : null;

    // Course Controller
    $courseController = new CourseController();

    // Call the controller method to add the course
    $isAdded = $courseController->addCourse(
        $courseName,
        $year,
        $courseTime,
        $courseDuration,
        $courseRoom,
        $creditHours,
        $selected_day,
        $courseCode,
        $prerequisiteId ?? null,
        $labTime ?? null,
        $labDuration ?? null,
        $labday ?? null,
        $labRoom ?? null,
        $secondLectureTime ?? null,
        $secondLectureDuration ?? null,
        $secondlecday ?? null,
        $secondlecRoom ?? null
         );

    // Provide Feedback
    $message = $isAdded ? 'Course Created successfully!' : 'Course Creation Failed.';
    echo "<script>alert('$message');</script>";

    if ($isAdded) {
        header("Location: admin.php");
        exit();
    }
}

?>
