<?php
include_once "include/dbh.inc.php"; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST['course_id'];
    $newCourseName = $_POST['new_course_name'];
    $newYear = $_POST['new_year'];

    $sql = "UPDATE course SET  CourseName = '$newCourseName',  Year = '$newYear' WHERE ID = $courseId";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Course updated successfully!');</script>";
        header("Location: Instructor.php"); 
    } else {
        echo "<script>alert('Error updating course: " . $conn->error . "');</script>";
    }

    $conn->close();
}
?>
