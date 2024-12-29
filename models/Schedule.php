<?php
include_once '../include/dbh.inc.php';

class Schedule {
    
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }




           public function createSchedule($schedule_id, $selectedCourses) {
    // Array to store all time slots for conflict checking
    $timeSlots = [];

    foreach ($selectedCourses as $course_code) {
        $query = "SELECT StartTime, Duration, Day, LabTime, LabDuration, LabDay, SecondLectureTime, SecondLectureDuration, SecondLectureDay 
                  FROM course WHERE CourseCode = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $course_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

   

        list($startHour, $startMinute) = explode(':', $course['StartTime']);
        $mainStartTime = $startHour * 60 + $startMinute;
        $mainEndTime = $mainStartTime + ($course['Duration'] * 60);

        $labStartTime = $labEndTime = null;
        if (!empty($course['LabTime'])) {
            list($labHour, $labMinute) = explode(':', $course['LabTime']);
            $labStartTime = $labHour * 60 + $labMinute;
            $labEndTime = $labStartTime + ($course['LabDuration'] * 60);
        }

        $secondLectureStartTime = $secondLectureEndTime = null;
        if (!empty($course['SecondLectureTime'])) {
            list($secondLectureHour, $secondLectureMinute) = explode(':', $course['SecondLectureTime']);
            $secondLectureStartTime = $secondLectureHour * 60 + $secondLectureMinute;
            $secondLectureEndTime = $secondLectureStartTime + ($course['SecondLectureDuration'] * 60);
        }

        foreach ($timeSlots as $timeSlot) {
            if (
                ($course['Day'] === $timeSlot['day'] &&
                    $mainStartTime < $timeSlot['end'] &&
                    $mainEndTime > $timeSlot['start']) ||
                (!empty($course['LabDay']) && $course['LabDay'] === $timeSlot['day'] &&
                    $labStartTime < $timeSlot['end'] &&
                    $labEndTime > $timeSlot['start']) ||
                (!empty($course['SecondLectureDay']) && $course['SecondLectureDay'] === $timeSlot['day'] &&
                    $secondLectureStartTime < $timeSlot['end'] &&
                    $secondLectureEndTime > $timeSlot['start'])
            ) {
                return "Error: Time conflict detected for course $course_code on " . $timeSlot['day'] . ".";
            }
        }

        $timeSlots[] = ['day' => $course['Day'], 'start' => $mainStartTime, 'end' => $mainEndTime, 'type' => 'main'];
        if (!empty($labStartTime)) {
            $timeSlots[] = ['day' => $course['LabDay'], 'start' => $labStartTime, 'end' => $labEndTime, 'type' => 'lab'];
        }
        if (!empty($secondLectureStartTime)) {
            $timeSlots[] = ['day' => $course['SecondLectureDay'], 'start' => $secondLectureStartTime, 'end' => $secondLectureEndTime, 'type' => 'secondLecture'];
        }
    }

    $values = [];
    foreach ($selectedCourses as $course_code) {
        $values[] = "('$schedule_id', '$course_code')";
    }
    $valuesString = implode(", ", $values);

    $query = "INSERT INTO schedule (schedule_id, course_code) VALUES $valuesString";

    if ($this->conn->query($query)) {
        return true;
    } else {
        return "Error executing query: " . $this->conn->error;
    }
}




    public function getAllSchedules() {
        $query = "SELECT s.schedule_id, s.course_code, c.CourseName
                  FROM schedule s
                  JOIN course c ON s.course_code = c.CourseCode";
        $result = $this->conn->query($query);
        
        // Check if the query was successful
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);  // Return the fetched rows as an associative array
        }
        return null;  // Return null if no schedules found
    }

    // Fetch a specific schedule by its ID
    public function getScheduleById($schedule_id) {
        $query = "SELECT s.schedule_id, s.course_code, c.CourseName
                  FROM schedule s
                  JOIN courses c ON s.course_code = c.CourseCode
                  WHERE s.schedule_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $schedule_id); // 'i' for integer
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();  // Return the fetched row as an associative array
        }
        return null;  // Return null if no schedule found with the provided ID
    }

 


}
?>
