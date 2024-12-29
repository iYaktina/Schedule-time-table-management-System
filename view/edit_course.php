<?php
include_once '../controllers/CourseController.php';
include_once '../controllers/InstructorController.php';
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idchecker = $_POST['course_id'];
    $courseCode = $_POST['courseCode'];
    $courseName = $_POST['course_name'] ;
    $year = $_POST['year'] ;
    $startTime = $_POST['courseTime'] ;
    $duration = $_POST['courseDuration'] ;
    $room = $_POST['courseRoom'] ;
    $creditHours = $_POST['creditHours'] ;
    $day = $_POST['day'] ;
    $prerequisiteId = $_POST['prerequisiteId'] ?? null;

    // Lab fields
    $hasLab = isset($_POST['hasLab']);
    $labTime = $hasLab ? $_POST['labTime'] : null;
    $labDuration = $hasLab ? $_POST['labDuration'] : null;
    $labDay = $hasLab ? $_POST['labday'] : null;
    $labRoom = $hasLab ? $_POST['labRoom'] : null;

    // Second lecture fields
    $hasSecondLecture = isset($_POST['secondLecture']);
    $secondLectureTime = $hasSecondLecture ? $_POST['secondLectureTime'] : null;
    $secondLectureDuration = $hasSecondLecture ? $_POST['secondLectureDuration'] : null;
    $secondLectureDay = $hasSecondLecture ? $_POST['secondlecday'] : null;
    $secondLectureRoom = $hasSecondLecture ? $_POST['secondLectureRoom'] : null;

    // Instructor fields
    $hasInstructor = isset($_POST['hasInstructor']);
    $instructorId = $hasInstructor ? $_POST['instructorId'] : null;
    $instructorName = null;

    if ($hasInstructor && $instructorId) {
        $instructorController = new InstructorController();
        $instructorName = $instructorController->getInstructorNameById($instructorId);
    }

     $courseController = new CourseController();
     $isUpdated = $courseController->updateCourse(
        $idchecker,
        $courseName,
        $year,
        $duration,
        $startTime,
        $room,
        $creditHours,
        $day,
        $courseCode,
        $prerequisiteId,
        $instructorId,
        $instructorName,
        $labTime,
        $labDuration,
        $labDay,
        $labRoom,
        $secondLectureTime,
        $secondLectureDuration,
        $secondLectureDay,
        $secondLectureRoom
    );

    if ($isUpdated) {
        echo "<script>alert('Course updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating course. Please try again.');</script>";
    }
    header("Refresh: 0; URL=admin.php");
    exit;
}
?>

