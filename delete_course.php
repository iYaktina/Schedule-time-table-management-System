<?php
include_once "include/dbh.inc.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST['course_id'];

    $sql = "DELETE FROM course WHERE ID = $courseId";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Course deleted successfully!');</script>";
        header("Location: Instructor.php"); 
    } else {
        echo "<script>alert('Error deleting course: " . $conn->error . "');</script>";
    }

    $conn->close();
}
?>
