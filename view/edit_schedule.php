<?php
include('menu.php');
include_once "../controllers/AdminController.php";

// Ensure session is active and the user is an admin
if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to admins only. Please login.');</script>";
    header("Refresh: 0;URL=login.php");
    exit;
}

$scheduleId = $_GET['schedule_id'] ?? null;
$adminController = new AdminController();

$selectedCourses = [];
if ($scheduleId) {
    $selectedCourses = $adminController->getSelectedCoursesForSchedule($scheduleId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSelectedCourses = $_POST['selected_courses'] ?? [];
    try {
         $adminController->deleteSchedule($scheduleId);
         $adminController->updateScheduleCourses($scheduleId,$newSelectedCourses);

        echo "<script>alert('Schedule updated successfully!');</script>";
        header("Refresh: 0; URL=admin.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
    <link rel="stylesheet" href="../assets/css/scheduleedit.css">
</head>
<body>


    <form action="" method="POST" >
        <!-- Hidden field to pass schedule ID -->
        <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($scheduleId); ?>">

        <h3>Edit Schedule for ID: <?php echo htmlspecialchars($scheduleId); ?></h3>
        
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
                $result = Database::getInstance()->getConnection()->query("SELECT CourseCode, CourseName, Room, Year, StartTime, InstructorName, Duration FROM course");
                while ($row = $result->fetch_assoc()) {
                    // Determine if the course is part of the schedule
                    $isChecked = in_array($row['CourseName'], $selectedCourses) ? 'checked' : '';

                    // Calculate end time
                    list($startHour, $startMinute) = explode(':', $row['StartTime']);
                    $duration = $row['Duration'];

                    $endHour = $startHour + $duration;
                    $endMinute = $startMinute;

                    if ($endHour >= 24) {
                        $endHour -= 24;
                    }

                    $startTimeFormatted = $row['StartTime'];
                    $endTimeFormatted = sprintf('%02d:%02d', $endHour, $endMinute);

                    // Display course row
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_courses[]' value='{$row['CourseCode']}' $isChecked></td>";
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

        <div>
            <h4>Select up to 5 courses for the schedule</h4>
            <button type="submit">Update Schedule</button>
            <button type="button" onclick="window.location.href='Scheduletemplate.php';">Back</button>

        </div>
    </form>

</body>
</html>
