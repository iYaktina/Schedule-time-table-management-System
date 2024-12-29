<?php
include('menu.php');
include_once '../include/dbh.inc.php';
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
    <link rel="stylesheet" href="../assets/css/instructor.css">
    <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(form => form.style.display = 'none');
            document.getElementById(formId).style.display = 'block';
        }

      
    </script>
</head>
<body>

<div class="profile-container">
    <div class="header">
        <h2>Instructor Panel</h2>
        <img src="../assets/images/star.png" alt="Profile Icon" class="profile-icon">
    </div>

    <div class="button-container">
        <button onclick="showForm('addCourseForm')">Add Course</button>
        <button onclick="showForm('courseList')">View Courses</button>
    </div>

    <div id="addCourseForm" class="form-container">
        <h3>Add Course</h3>
        <form action="assign_courses.php" method="POST" >
           <label for="course">Select a Course:</label>
    <select name="course" id="course" required>
        <option value="">--Select a Course--</option>
        <?php
        // Display courses without an instructor
         $query = "SELECT CourseCode, courseName FROM Course WHERE instructorId IS NULL";
            $result = mysqli_query(Database::getInstance()->getConnection(), $query);

            if (mysqli_num_rows($result) > 0) {
                // Loop through the courses and display them in the dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['CourseCode'] . "'>" . $row['courseName'] . "</option>";
                }
            } else {
                echo "<option value=''>No available courses</option>";
            }
        ?>
    </select>
    <br>
    <input type="submit" value="Assign Course">
        </form>
    </div>



    <div id="courseList" class="form-container">
        <h3>All Courses</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Room</th>
                    <th>Year</th>
                    <th>Time</th>
                    <th>Instructor Name</th>
                    <th>Actions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $iID=$_SESSION['ID'];
                $result = Database::getInstance()->getConnection()->query("SELECT CourseCode, CourseName, Room, Year,StartTime,InstructorName,Duration FROM course Where instructorId = $iID ");
                while ($row = $result->fetch_assoc()) {
                      list($startHour, $startMinute) = explode(':', $row['StartTime']);
                $duration = $row['Duration']; 

                $endHour = $startHour + $duration;
                $endMinute = $startMinute;

                if ($endHour >= 24) {
                    $endHour -= 24;
                }

                $startTimeFormatted = $row['StartTime']; 
                $endTimeFormatted = (float)$row['StartTime'] + (float)$row['Duration'];
                    echo "<tr>";
                    echo "<td>{$row['CourseCode']}</td>";
                    echo "<td>{$row['CourseName']}</td>";
                    echo "<td>{$row['Room']}</td>";
                    echo "<td>{$row['Year']}</td>";
                    echo "<td>{$startTimeFormatted} - {$endTimeFormatted}</td>"; 
                    echo "<td>{$row['InstructorName']}</td>";
                    echo "<td>
                              <a href='unassign_courses.php?course_id={$row['CourseCode']}' class='edit-button' >
                                 Delete
                                </a>
                            </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
            <button type="button"  onclick="window.location.href='?';">Back</button>
    </div>

 

</body>
</html>
