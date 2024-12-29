<?php
include_once '../controllers/ScheduleController.php';
session_start();

if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to Admins only. ');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedCourses = $_POST['selected_courses'] ?? [];
    $schedule_id = $_POST['schedule_id']; 

    if (count($selectedCourses) >= 4 && count($selectedCourses) <= 5) {
        $scheduleController = new ScheduleController();
        $result = $scheduleController->createSchedule($schedule_id, $selectedCourses);

        if ($result === true) {
            echo "<script>alert('Schedule Template Created Successfully!');</script>";
            header("Location: Scheduletemplate.php");
            exit();
        } 
    } else {
        echo "<script>alert('You can select a maximum of 5 courses and a minimum of 4 courses.');</script>";
        header("Location: Scheduletemplate.php");
        exit();
    }
}
?>
