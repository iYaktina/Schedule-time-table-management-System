<?php
include('menu.php');
include_once "../controllers/UserController.php";
if (!isset($_SESSION['ID']) || ($_SESSION['Usertype'] != 'User'&& $_SESSION['Usertype'] != 'user')) {
    echo "<script>alert('Access restricted to students only. Please login.');</script>";
    header("Refresh: 0;URL=login.php");
    exit;
}

$userController = new UserController();
$studentId = $_SESSION['ID'];

// Get schedule and course data
$data = $userController->getStudentSchedule($studentId);
$selectedScheduleId = $data['selectedScheduleId'];
$courses = $data['courses'];
$weekSummary = $data['weekSummary'];
$monthSummary = $data['monthSummary'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule </title>
    <link rel="stylesheet" href="../assets/css/scheduleadmin.css"> 
</head>
<body>
  <div class="sidebar">
    <section>
        <h3 id="current-date">
            <?php echo date('l, F j, Y'); ?>
        </h3>
        <div id="today-tomorrow">
            <!-- Today's Events -->
            <h4>Today:</h4>
            <ul id="today-events">
                <?php
                $today = date('l');
                $todayEventsFound = false; // Flag to track if any events are found for today

                foreach ($courses as $event) {
                    if ($event['day'] === $today) {
                        $todayEventsFound = true;
                        echo "<li>{$event['CourseName']} at {$event['StartTime']} in Room {$event['Room']}</li>";
                    }
                    if ($event['LabDay'] === $today) {
                        $todayEventsFound = true;
                        echo "<li>{$event['CourseName']} at {$event['labTime']} in Room {$event['LabRoom']}</li>";
                    }
                     if ($event['secondLectureDay'] === $today) {
                        $todayEventsFound = true;
                        echo "<li>{$event['CourseName']} at {$event['secondLectureTime']} in Room {$event['SecondLectureRoom']}</li>";
                    }
                }

                if (!$todayEventsFound) {
                    echo "<li>No events scheduled for today.</li>";
                }
                ?>
            </ul>

            <!-- Tomorrow's Events -->
            <h4>Tomorrow:</h4>
            <ul id="tomorrow-events">
                <?php
                $tomorrow = date('l', strtotime('+1 day'));
                $tomorrowEventsFound = false; // Flag to track if any events are found for tomorrow

                foreach ($courses as $event) {
                    if ($event['day'] === $tomorrow) {
                        $tomorrowEventsFound = true;
                        echo "<li>{$event['CourseName']} at {$event['StartTime']} in Room {$event['Room']}</li>";
                    }
                    if ($event['LabDay'] === $tomorrow) {
                        $tomorrowEventsFound = true;
                        echo "<li>{$event['CourseName']} at {$event['labTime']} in Room {$event['LabRoom']}</li>";
                    }
                     if ($event['secondLectureDay'] === $tomorrow) {
                        $tomorrowEventsFound = true;
                        echo "<li>{$event['CourseName']} at {$event['secondLectureTime']} in Room {$event['SecondLectureRoom']}</li>";
                    }
                }

                if (!$tomorrowEventsFound) {
                    echo "<li>No events scheduled for tomorrow.</li>";
                }
                ?>
            </ul>
        </div>
    </section>
</div>


<div class="main-content">
    <div class="calendar-nav">
        <div>
            <button id="day-button">Day</button>
            <button id="week-button">Week</button>
            <button id="month-button">Month</button>
        </div>
    </div>

    <!-- Day View -->
   <h3>My Schedule <?php echo $selectedScheduleId ?></h3>
    <?php if (!empty($courses)): ?>
        <table class="schedule-table" id="day-view" style="display: table;">
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

                    // Populate the main lecture content
                    $mainDay = $course['day'];
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
    <?php else: ?>
        <p>No courses in your selected schedule.</p>
    <?php endif; ?>
    <!-- Week View -->
    <table class="week-table" id="week-view" style="display: none;">
        <thead>
            <tr>
                <th>Week</th>
                <th>Summary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Current Week</td>
                <td>
                    <?php
                    foreach ($weekSummary as $course => $count) {
                        echo "{$course} (taken {$count} times)<br>";
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Month View -->
    <table class="month-table" id="month-view" style="display: none;">
        <thead>
            <tr>
                <th>Month</th>
                <th>Summary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Current Month</td>
                <td>
                    <?php
                    foreach ($monthSummary as $course => $count) {
                        echo "{$course} (taken {$count} times)<br>";
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    const dayButton = document.getElementById('day-button');
    const weekButton = document.getElementById('week-button');
    const monthButton = document.getElementById('month-button');

    const dayView = document.getElementById('day-view');
    const weekView = document.getElementById('week-view');
    const monthView = document.getElementById('month-view');

    dayButton.addEventListener('click', () => {
        dayView.style.display = 'table';
        weekView.style.display = 'none';
        monthView.style.display = 'none';
    });

    weekButton.addEventListener('click', () => {
        dayView.style.display = 'none';
        weekView.style.display = 'table';
        monthView.style.display = 'none';
    });

    monthButton.addEventListener('click', () => {
        dayView.style.display = 'none';
        weekView.style.display = 'none';
        monthView.style.display = 'table';
    });
</script>
</body>
</html>
