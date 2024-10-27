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
        

    $result = $conn->query($sql);
    
    error_log("SQL Query: $sql");
    if ($result === FALSE) {
        error_log("Error in query: " . $conn->error);
    } else {
        error_log("Number of conflicting courses: " . $result->num_rows);
    }

    if ($result->num_rows > 0) {
        echo "<script>alert('The selected room is already booked for this time slot. Please choose a different time or room.');</script>";
        header("Refresh: 0; URL=Instructor.php");
        exit();
    }

    $sql = "INSERT INTO course (CourseName, Year, StartTime, Duration, Room, InstructorName) 
            VALUES ('$courseName', '$year', '$courseTime', '$courseDuration', '$courseRoom', '$instructorName')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Course added successfully!');</script>";
        header("Location: Instructor.php"); 
        exit();
    } else {
        echo "<script>alert('Error adding course: " . $conn->error . "');</script>";
    }

    $conn->close();
}
?>
