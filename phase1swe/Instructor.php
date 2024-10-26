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
<body>

<div class="profile-container">
    <div class="header">
        <h2>Instructor Panel</h2>
        <img src="./images/22.png" alt="Profile Icon" class="profile-icon">
    </div>

    <div class="button-container">
        <button onclick="showForm('addCourseForm')">Add Course</button>
        <button onclick="showForm('editCourseForm')">Edit Course</button>
        <button onclick="showForm('deleteCourseForm')">Delete Course</button>
        <button onclick="showForm('courseList')">View Courses</button>
    </div>
    <div id="addCourseForm" class="form-container">
    <h3>Add Course</h3>
    <form action="add_course.php" method="POST" onsubmit="return validateTime();">

        <label for="course_name">Course Name</label>
        <input type="text" id="course_name" name="course_name" placeholder="Enter Course Name" required>

        <label for="year">Year</label>
        <input type="text" id="year" name="year" placeholder="Enter Year" required>

        <label for="courseTime">Course Start Time</label>
        <input type="time" id="courseTime" name="courseTime" required>

        <label for="courseDuration">Course Duration (hours)</label>
        <input type="number" id="courseDuration" name="courseDuration" placeholder="Enter Duration in hours" required>

        <label for="courseRoom">Course Room </label>
        <input type="text" id="courseRoom" name="courseRoom" placeholder="Enter Room" required>

        <label for="instructorName">Instructor Name </label>
        <input type="text" id="instructorName" name="instructorName"   value="<?php echo $_SESSION['Name']; ?>" readonly>

        <button type="submit">Add Course</button>
    </form>
</div>
