<?php
include('menu.php');
include_once "../controllers/AdminController.php";

// Ensure session is active and the user is an admin
if (!isset($_SESSION['ID']) || $_SESSION['Usertype'] != 'Admin') {
    echo "<script>alert('Access restricted to admins only. Please login.');</script>";
    header("Refresh: 0;URL=login.php");
    exit;
}

$adminController = new AdminController();
$scheduleId = $_POST['schedule_id'] ?? null;

// Initialize variables
$courses = [];
$weekSummary = [];
$monthSummary = [];
$todayEvents = [];
$tomorrowEvents = [];

// If a schedule ID is provided, fetch data for that schedule
if ($scheduleId) {
    $data = $adminController->getScheduleData($scheduleId);
    if (!empty($data['courses'])) {
        $courses = $data['courses'];
        $weekSummary = $data['weekSummary'];
        $monthSummary = $data['monthSummary'];

        // Fetch today's and tomorrow's events
        $events = $adminController->getTodayAndTomorrowEvents($scheduleId);
        $todayEvents = $events['todayEvents'];
        $tomorrowEvents = $events['tomorrowEvents'];
    } 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Schedule Management</title>
    <link rel="stylesheet" href="../assets/css/scheduleadmin.css"> 
</head>
<body>
  <div class="sidebar">
    <section>
        <h3 id="current-date">
            <?php echo date('l, F j, Y');  ?>
        </h3>
        <div id="today-tomorrow">
            <h4>Today:</h4>
            <ul id="today-events">
                <?php
                if (!empty($todayEvents)) {
                    foreach ($todayEvents as $event) {
                        echo "<li>{$event['CourseName']} at {$event['StartTime']} in Room {$event['Room']}</li>";
                    }
                } else {
                    echo "<li>No events for today</li>";
                }
                ?>
            </ul>

            <!-- Tomorrow's Events -->
            <h4>Tomorrow:</h4>
            <ul id="tomorrow-events">
                <?php
                if (!empty($tomorrowEvents)) {
                    foreach ($tomorrowEvents as $event) {
                        echo "<li>{$event['CourseName']} at {$event['StartTime']} in Room {$event['Room']}</li>";
                    }
                } else {
                    echo "<li>No events for tomorrow</li>";
                }
                ?>
            </ul>
        </div>
    </section>
</div>

    <div class="main-content">
        <div class="calendar-nav">
            <div>
                <button>Day</button>
                <button>Week</button>
                <button>Month</button>
            </div>
        </div>

        <div class="form-container" >
            <form action="" method="POST">
                <label for="schedule_id">Enter Schedule ID:</label>
                <input type="text" id="schedule_id" name="schedule_id" required>
                <input type="hidden" id="schedule_id1" name="schedule_id1" value="<?php echo $course['CourseCode']; ?>">

                <button type="submit">Submit</button>
            </form>
        </div>

        <!-- Display the courses based on the entered schedule_id -->
        <?php if (!empty($courses)): ?>
            <h3>Courses in Schedule ID: <?php echo htmlspecialchars($scheduleId); ?></h3>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Sunday</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    foreach ($courses as $course) {
        // Initialize array to store content for each day
        $scheduleRow = [
            'Sunday' => '', 
            'Monday' => '', 
            'Tuesday' => '', 
            'Wednesday' => '', 
            'Thursday' => '', 
            'Friday' => '', 
            'Saturday' => ''
        ];

        // Use the day string directly
        $mainDay = $course['day']; // No mapping needed

        // Populate the main lecture content
            $scheduleRow[$mainDay] = "
                            <div class='day-content'>Lecture</div>
                        <div class='day-content'>Room: {$course['Room']}</div>
                        <div class='day-content'>{$course['StartTime']} - " . 
                        date('H:i', strtotime("+" . $course['Duration'] . " hours", strtotime($course['StartTime']))) . "</div>
                        <div class='day-content'>Instructor: {$course['InstructorName']}</div>";

                    // Add Lab to its specific day
                        if (!empty($course['LabDay'])) {
                          $labDay = $course['LabDay'];
                          $scheduleRow[$labDay] .= "
                           <div class='day-content'>Lab</div>
                           <div class='day-content'>Room: {$course['LabRoom']}</div>
                              <div class='day-content'> {$course['labTime']} - " . 
                              date('H:i', strtotime("+" . $course['labDuration'] . " hours", strtotime($course['labTime']))) . "</div>
                              <div class='day-content'>Instructor: {$course['InstructorName']}</div>";
                      }

                      // Add Second Lecture to its specific day
                      if (!empty($course['secondLectureDay'])) {
                          $secondLectureDay = $course['secondLectureDay'];
                          $scheduleRow[$secondLectureDay] .= "
                            <div class='day-content'>Lecture</div>
                            <div class='day-content'>Room: {$course['SecondLectureRoom']}</div>
                              <div class='day-content'> {$course['secondLectureTime']} - " . 
                              date('H:i', strtotime("+" . $course['secondLectureDuration'] . " hours", strtotime($course['secondLectureTime']))) . "</div>
                              <div class='day-content'>Instructor: {$course['InstructorName']}</div>";
                      }

                    // Render the row
                    echo "<tr>";
                    echo "<td class='subject-column'>{$course['CourseName']}</td>";
                    foreach ($scheduleRow as $day => $content) {
                        echo "<td class='empty'>" . ($content ? $content : "—") . "</td>";
                    }
                    echo "</tr>";
                }
    ?>
</tbody>

            </table>
               
        <?php elseif (isset($scheduleId)): ?>
            <p>No courses found for Schedule ID: <?php echo htmlspecialchars($scheduleId); ?>.</p>
        <?php endif; ?>

        <!-- Other tables (week/month) are unchanged -->
            <table class="week-table" style="display:none">
    <thead>
        <tr>
            <th>Week</th>
            <th>Summary</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="summary-column">Week 1</td> <!-- Fixed week number -->
            <td>
                <?php
                // Combine all course summaries for the week
                foreach ($weekSummary as $course => $count) {
                    echo "{$course} (taken {$count} times)<br>"; // Add line breaks for readability
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
                        <table class="month-table" style="display:none">
    <thead>
        <tr>
            <th>Month</th>
            <th>Summary</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="month-column">Current Month</td> <!-- Fixed month name -->
            <td>
                <?php
                // Combine all course summaries for the current month
                foreach ($monthSummary as $course => $count) {
                    echo "{$course} (taken {$count} times)<br>"; // Add line breaks for readability
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<div class="action-buttons">
                    <h3>Actions for Schedule</h3>
                    <!-- Edit Button -->
                    <button onclick="redirectToEditSchedule()" class="action-button">Edit Schedule</button>

                    <!-- Delete Button -->
                    <button onclick="confirmScheduleDeletion()" class="action-button">Delete Schedule</button>
                </div>
    </div>


</body>
 <script>
        const dayButton = document.querySelector('.calendar-nav button:nth-child(1)');
        const weekButton = document.querySelector('.calendar-nav button:nth-child(2)');
        const monthButton = document.querySelector('.calendar-nav button:nth-child(3)');
        const dayView = document.querySelector('.schedule-table');
        const weekView = document.querySelector('.week-table');
        const monthView = document.querySelector('.month-table');

        dayButton.addEventListener('click', () => {
            dayView.style.display = 'inline-table';
            weekView.style.display = 'none';
            monthView.style.display = 'none';
        });

        weekButton.addEventListener('click', () => {
            dayView.style.display = 'none';
            weekView.style.display = 'inline-table';
            monthView.style.display = 'none';
        });

        monthButton.addEventListener('click', () => {
            dayView.style.display = 'none';
            weekView.style.display = 'none';
            monthView.style.display = 'inline-table';
        });

        const currentDateElement = document.getElementById('current-date');
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = today.toLocaleDateString(undefined, options);


      function redirectToEditSchedule() {
        const scheduleId = "<?php echo htmlspecialchars($scheduleId); ?>";
        if (scheduleId) {
            window.location.href = `edit_schedule.php?schedule_id=${scheduleId}`;
        } else {
            alert("Schedule ID is not available for editing.");
        }
    }

    function confirmScheduleDeletion() {
        const scheduleId = "<?php echo htmlspecialchars($scheduleId); ?>";
        if (scheduleId) {
            if (confirm("Are you sure you want to delete this schedule?")) {
                window.location.href = `delete_schedule.php?schedule_id=${scheduleId}`;
            }
        } else {
            alert("Schedule ID is not available for deletion.");
        }
    }
    </script>
</html>
