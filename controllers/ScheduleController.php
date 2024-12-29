<?php
include_once '../models/ModelFactory.php';

class ScheduleController {

    private $schedule;

    public function __construct() {
        $this->schedule = ModelFactory::create('Schedule');  
    }

        public function createSchedule($schedule_id, $course_code) {
            // Call the model function to create the schedule
            $result = $this->schedule->createSchedule($schedule_id, $course_code);

            // Check the result
            if ($result === true) {
                // Success case
                echo "<script>alert('Schedule Created Successfully!');</script>";
                header("Location: Scheduletemplate.php");
                exit();
            } else {
                // Error case: Display the error returned from the model
                echo "<script>alert('Error: $result');</script>";
            }
        }


    public function displaySchedules() {
        $schedules = $this->schedule->getAllSchedules();

        if ($schedules) {
            return $schedules;  
        } else {
            echo "<script>alert('No schedules found!');</script>";
            return [];
        }
    }

    public function displaySchedule($schedule_id) {
        $schedule = $this->schedule->getScheduleById($schedule_id);

        if ($schedule) {
            return $schedule;  
        } else {
            echo "<script>alert('Schedule not found!');</script>";
            return null;
        }
    }



}
?>
