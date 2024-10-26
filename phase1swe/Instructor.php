<?php
    include_once "include/dbh.inc.php";
    include('menu.php');

    if (!isset($_SESSION['ID'])) {
        echo "<script>alert('Please Login!');</script>"; 
        header("Refresh: 0; URL=login.php"); 
    }

    if ($_SESSION['Usertype'] == 'User') {
        echo "<script>alert('Classified for instructors and admins only');</script>"; 
        header("Refresh: 0; URL=index.php"); 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Panel</title>
    <link rel="stylesheet" href="./css/instructor.css">
   <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(form => form.style.display = 'none');
            document.getElementById(formId).style.display = 'block';
        }

        function validateTime() {
            const courseTime = document.getElementById('courseTime').value;
            const courseDuration = document.getElementById('courseDuration').value;

            const [hours, minutes] = courseTime.split(':').map(Number);
            const duration = parseInt(courseDuration, 10);

            if (hours < 9 || hours >= 16 || (hours === 16 && minutes > 0)) {
                alert("The course must start between 9 AM and 4 PM.");
                return false;
            }
            if(courseDuration >3){
                alert("The maximum duration for the course is 3 hours or less");
                return false;
            }
            const endHour = hours + Math.floor((minutes + duration * 60) / 60);
            const endMinute = (minutes + duration * 60) % 60;

            if (endHour > 18 || (endHour === 18 && endMinute > 0)) {
                alert("The course must end before 6 PM.");
                return false;
            }

            if (minutes !== 0 && minutes !== 15 && minutes !== 30) {
                alert("Minutes must be 00, 15, or 30.");
                return false;
            }

            return true;
        }
    </script>
</head>