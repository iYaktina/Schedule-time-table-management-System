<?php
include_once 'include/dbh.inc.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = $_POST['course_name'];
    $year = $_POST['year']; 
    $courseDuration = $_POST['courseDuration'];
    $courseTime = $_POST['courseTime'];
    $courseRoom = $_POST['courseRoom'];
    $instructorName = $_POST['instructorName'];

    list($startHour, $startMinute) = explode(':', $courseTime);
    $startTimeInMinutes = $startHour * 60 + $startMinute; 
    $endTimeInMinutes = $startTimeInMinutes + ($courseDuration * 60); 

    error_log("Checking room: $courseRoom");
    error_log("Start Time (in minutes): $startTimeInMinutes");
    error_log("End Time (in minutes): $endTimeInMinutes");

    $sql = "SELECT * FROM course
            WHERE Room = '$courseRoom' 
            AND (
                (HOUR(StartTime) * 60 + MINUTE(StartTime) < $endTimeInMinutes AND 
                (HOUR(StartTime) * 60 + MINUTE(StartTime) + Duration * 60) > $startTimeInMinutes)
            )";
}