<?php
    include('menu.php');
    include_once '../include/dbh.inc.php';
    if (!isset($_SESSION['ID'])) {
        echo "<script>alert('Please Login!');</script>";
        header("Refresh: 0;URL=login.php");
    }
    if($_SESSION['Usertype'] != 'Admin'){
        echo "<script>alert('Classified for admins only');</script>";
        header("Refresh: 0;URL=index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
   
</head>
<script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(form => form.style.display = 'none');
            document.getElementById(formId).style.display = 'block';
        }
        
                document.querySelector('form').addEventListener('submit', function (event) {
                    // Get all selected checkboxes
                    const selectedCourses = document.querySelectorAll('input[name="selected_courses[]"]:checked');

                    // Check if the number of selected courses is within the range
                    if (selectedCourses.length < 4 || selectedCourses.length > 5) {
                        alert('You must select at least 4 courses and at most 5 courses.');
                        event.preventDefault(); // Prevent form submission
                    }
                });



    function sendAnnouncement() {
        if (confirm('Are you sure you want to announce the schedule selection?')) {
            fetch('sendAnnouncement.php', {
                method: 'POST',
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send the announcement.');
            });
        }
    }

function endScheduleSelection() {
    if (confirm('Are you sure you want to close schedule selection?')) {
        fetch('endSelection.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Schedule selection period has been closed.');
                location.reload();
            } else {
                alert('Failed to close schedule selection. Please try again.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Something went wrong.');
        });
    }
}

        </script>
<body>

<div class="profile-container">
    <div class="header">
        <h2>Admin Panel</h2>
        <img src="../assets/images/star.png" alt="Profile Icon" class="profile-icon">
    </div>

    <div class="button-container">
        <button onclick="sendAnnouncement()" >Announcement Button</button>
        <button onclick="showForm('addSchedule')">Create a Schedule</button>
        <button id="endSelectionButton" onclick="endScheduleSelection()">End Selection Period</button>
    </div>

    <div id="addSchedule" class="form-container">

    <form action="schedule_template.php" method="POST">
            <label for="schedule_id">Template ID:</label>
            <input type="text" id="schedule_id" name="schedule_id" required placeholder="Enter Schedule Template ID">
            
            <h3>All Courses</h3>
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Room</th>
                    <th>Year</th>
                    <th>Time</th>
                    <th>Instructor Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all courses from the database
                $result = Database::getInstance()->getConnection()->query("SELECT CourseCode, CourseName, Room, Year, StartTime, InstructorName, Duration FROM course");
                while ($row = $result->fetch_assoc()) {
                    list($startHour, $startMinute) = explode(':', $row['StartTime']);
                    $duration = $row['Duration'];

                    $endHour = $startHour + $duration;
                    $endMinute = $startMinute;

                    if ($endHour >= 24) {
                        $endHour -= 24;
                    }

                    $startTimeFormatted = $row['StartTime'];
                    $endTimeFormatted = sprintf('%02d:%02d', $endHour, $endMinute);
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_courses[]' value='{$row['CourseCode']}'></td>";
                    echo "<td>{$row['CourseCode']}</td>";
                    echo "<td>{$row['CourseName']}</td>";
                    echo "<td>{$row['Room']}</td>";
                    echo "<td>{$row['Year']}</td>";
                    echo "<td>{$startTimeFormatted} - {$endTimeFormatted}</td>";
                    echo "<td>{$row['InstructorName']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Limit number of courses to 5 -->
        <div>
            <h4>Select up to 5 courses for the schedule</h4>
            <button type="submit">Create Schedule </button>
        </div>
    </form>
    <button type="button" onclick="window.location.href='?';">Back</button>
</div>



</div>

</body>
</html>
