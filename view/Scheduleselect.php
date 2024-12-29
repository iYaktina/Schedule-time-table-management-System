<?php
include('menu.php');
include_once "../controllers/UserController.php";
include_once "../controllers/AdminController.php";
if (!isset($_SESSION['ID']) || ($_SESSION['Usertype'] != 'User'&& $_SESSION['Usertype'] != 'user')) {
    echo "<script>alert('Access restricted to students only. Please login.');</script>";
    header("Refresh: 0;URL=login.php");
    exit;
}
$adminController = new AdminController();
$scheduleAccess = $adminController->getScheduleAccessStatus();

if (!$scheduleAccess) {
    echo "<script>alert('Schedule selection is not open yet.');</script>";
    header("Refresh: 0;URL=index.php");
    exit;
}
$userController = new UserController();
$studentId = $_SESSION['ID'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedScheduleId = $_POST['schedule'];
    $userController->handleScheduleSelection($studentId, $selectedScheduleId);
}

// Fetch schedules and current selection
$data = $userController->handleScheduleSelection($studentId);
$schedules = $data['schedules'];
$selectedSchedule = $data['selectedSchedule'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Schedule</title>
    <link rel="stylesheet" href="../assets/css/scheduleadmin.css">
</head>
<body>
<div class="main-content">
    <h2>Select Your Schedule</h2>

    <?php if (!empty($schedules)): ?>
        <form method="POST">
            <div class="form-container">
                <h2>Available Schedules</h2>
                <select name="schedule" required>
                    <option value="">-- Select a Schedule --</option>
                    <?php foreach ($schedules as $schedule): ?>
                        <option value="<?= $schedule['schedule_id']; ?>" 
                            <?= ($selectedSchedule == $schedule['schedule_id']) ? 'selected' : ''; ?>>
                            <?= $schedule['schedule_id']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Save Schedule</button>
            </div>
        </form>
    <?php else: ?>
        <p>No schedules available for your year.</p>
    <?php endif; ?>
</div>
</body>
</html>
