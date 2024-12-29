<?php
include_once '../include/dbh.inc.php';

class Course {
    private $conn;

    public function __construct() {
    $this->conn = Database::getInstance()->getConnection();
    }


  public function addCourse(
    $courseName, $year, $startTime, $duration, $room, $creditHour, $day, $id, $prerequisiteId = null,
    $labTime = null, $labDuration = null, $labDay = null, $labRoom = null, $secondLectureTime = null,
    $secondLectureDuration = null, $secondLectureDay = null, $secondLectureRoom = null,
    $instructorName = null, $instructorId = null
) {
      // Convert start times to minutes for conflict checking
                    list($startHour, $startMinute) = explode(':', $startTime);
                    $startTimeInMinutes = $startHour * 60 + $startMinute;
                    $endTimeInMinutes = $startTimeInMinutes + ($duration * 60);
                    $labStartInMinutes=$labEndInMinutes=$secondLectureStartInMinutes=$secondLectureEndInMinutes=null;
                    if (!empty($labTime)) {
                        list($labStartHour, $labStartMinute) = explode(':', $labTime);
                        $labStartInMinutes = $labStartHour * 60 + $labStartMinute;
                        $labEndInMinutes = $labStartInMinutes + ($labDuration * 60);
                    echo("Lab: Start=$labStartInMinutes, End=$labEndInMinutes, Day=$labDay, Room=$labRoom");

                    }

                    if (!empty($secondLectureTime)) {
                        list($secondLectureStartHour, $secondLectureStartMinute) = explode(':', $secondLectureTime);
                        $secondLectureStartInMinutes = $secondLectureStartHour * 60 + $secondLectureStartMinute;
                        $secondLectureEndInMinutes = $secondLectureStartInMinutes + ($secondLectureDuration * 60);
                    echo("Second Lecture: Start=$secondLectureStartInMinutes, End=$secondLectureEndInMinutes, Day=$secondLectureDay, Room=$secondLectureRoom");

                    }
                       // Debug logs
                  if ($this->isRoomBooked(
                        $startTimeInMinutes, $endTimeInMinutes, $day, $room, $id
                    )) {
                        echo "Conflict detected in main lecture schedule.";
                        return false;
                    }

                    if (!empty($labRoom) && $this->isRoomBooked(
                        $labStartInMinutes, $labEndInMinutes, $labDay, $labRoom, $id
                    )) {
                        echo "Conflict detected in lab schedule.";
                        return false;
                    }

                    if (!empty($secondLectureRoom) && $this->isRoomBooked(
                        $secondLectureStartInMinutes, $secondLectureEndInMinutes, $secondLectureDay, $secondLectureRoom, $id
                    )) {
                        echo "Conflict detected in second lecture schedule.";
                        return false;
                    }

    // Prepare the SQL query
    $query = "INSERT INTO course (CourseCode, CourseName, Year, StartTime, Duration, Room, InstructorName, CreditHour, Day, PrerequisiteId, InstructorId, LabTime, LabDuration, SecondLectureTime, SecondLectureDuration, SecondLectureDay, LabDay, LabRoom, SecondLectureRoom)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        error_log("MySQL Prepare Error: " . $this->conn->error);
        return false;
    }

    // Bind parameters
    $stmt->bind_param(
        "ssssississisisissss",
        $id,
        $courseName,
        $year,
        $startTime,
        $duration,
        $room,
        $instructorName,
        $creditHour,
        $day,
        $prerequisiteId,
        $instructorId,
        $labTime,
        $labDuration,
        $secondLectureTime,
        $secondLectureDuration,
        $secondLectureDay,
        $labDay,
        $labRoom,
        $secondLectureRoom
    );

    // Execute and handle result
    if ($stmt->execute()) {
        return true;
    } else {
        error_log("MySQL Execute Error: " . $this->conn->error);
        return false;
    }
}


                public function editCourse(
                    $idchecker,
                    $courseName,
                    $year,
                    $startTime,
                    $duration,
                    $room,
                    $creditHour,
                    $day,
                    $id,
                    $prerequisiteId = null,
                    $instructorId = null,
                    $instructorName = "",
                    $labStartTime = null,
                    $labDuration = null,
                    $labRoom = null,
                    $labDay = null,
                    $secondLectureStartTime = null,
                    $secondLectureDuration = null,
                    $secondLectureRoom = null,
                    $secondLectureDay = null
                ) {
                    // Convert start times to minutes for conflict checking
                    list($startHour, $startMinute) = explode(':', $startTime);
                    $startTimeInMinutes = $startHour * 60 + $startMinute;
                    $endTimeInMinutes = $startTimeInMinutes + ($duration * 60);

                    if (!empty($labStartTime)) {
                        list($labStartHour, $labStartMinute) = explode(':', $labStartTime);
                        $labStartInMinutes = $labStartHour * 60 + $labStartMinute;
                        $labEndInMinutes = $labStartInMinutes + ($labDuration * 60);
                    }

                    if (!empty($secondLectureStartTime)) {
                        list($secondLectureStartHour, $secondLectureStartMinute) = explode(':', $secondLectureStartTime);
                        $secondLectureStartInMinutes = $secondLectureStartHour * 60 + $secondLectureStartMinute;
                        $secondLectureEndInMinutes = $secondLectureStartInMinutes + ($secondLectureDuration * 60);
                    }

                        // Debug logs
                    if ($this->isRoomBooked(
                        $startTimeInMinutes, $endTimeInMinutes, $day, $room, $id
                    )) {
                        echo "Conflict detected in main lecture schedule.";
                        return false;
                    }

                    if (!empty($labRoom) && $this->isRoomBooked(
                        $labStartInMinutes, $labEndInMinutes, $labDay, $labRoom, $id
                    )) {
                        echo "Conflict detected in lab schedule.";
                        return false;
                    }

                    if (!empty($secondLectureRoom) && $this->isRoomBooked(
                        $secondLectureStartInMinutes, $secondLectureEndInMinutes, $secondLectureDay, $secondLectureRoom, $id
                    )) {
                        echo "Conflict detected in second lecture schedule.";
                        return false;
                    }

                    // Update the course in the database
                    $query = "UPDATE course SET
                            CourseCode = ?,
                            CourseName = ?,
                            Year = ?,
                            StartTime = ?,
                            Duration = ?,
                            Room = ?,
                            InstructorName = ?,
                            CreditHour = ?,
                            Day = ?,
                            PrerequisiteId = ?,
                            InstructorId = ?,
                            LabTime = ?,
                            LabDuration = ?,
                            LabRoom = ?,
                            LabDay = ?,
                            SecondLectureTime = ?,
                            SecondLectureDuration = ?,
                            SecondLectureRoom = ?,
                            SecondLectureDay = ?
                            WHERE CourseCode = ?";

                    $stmt = $this->conn->prepare($query);
                    $stmt->bind_param(
                        "ssisississisisisisss",
                        $id,
                        $courseName,
                        $year,
                        $startTime,
                        $duration,
                        $room,
                        $instructorName,
                        $creditHour,
                        $day,
                        $prerequisiteId,
                        $instructorId,
                        $labStartTime,
                        $labDuration,
                        $labRoom,
                        $labDay,
                        $secondLectureStartTime,
                        $secondLectureDuration,
                        $secondLectureRoom,
                        $secondLectureDay,
                        $idchecker
                    );

                    if ($stmt->execute()) {
                        echo "Course updated successfully.";
                        return true;
                    } else {
                        echo "Error updating course: " . $this->conn->error;
                        return false;
                    }
                }


                 public function isRoomBooked(
                                                $startTimeInMinutes,
                                                $endTimeInMinutes,
                                                $day,
                                                $room,
                                                $excludeCourseCode = null
                                            ) {
                                                // SQL query for conflict checking
                                                $query = "SELECT *  FROM course WHERE (
                                                        -- Main lecture conflicts with everything
                                                 (
                                                            Room = ? AND Day = ? AND
                                                            (
                                                                -- Main lecture vs. main lecture
                                                                (HOUR(StartTime) * 60 + MINUTE(StartTime) < ? AND
                                                                (HOUR(StartTime) * 60 + MINUTE(StartTime) + Duration * 60) > ?)
                                                            )
                                                        )
                                                        OR
                                                        (
                                                            -- Main lecture conflicts with lab
                                                            LabRoom = ? AND LabDay = ? AND
                                                            (
                                                                (HOUR(LabTime) * 60 + MINUTE(LabTime) < ? AND
                                                                (HOUR(LabTime) * 60 + MINUTE(LabTime) + LabDuration * 60) > ?)
                                                            )
                                                        )
                                                        OR
                                                        (
                                                            -- Main lecture conflicts with second lecture
                                                            SecondLectureRoom = ? AND SecondLectureDay = ? AND
                                                            (
                                                                (HOUR(SecondLectureTime) * 60 + MINUTE(SecondLectureTime) < ? AND
                                                                (HOUR(SecondLectureTime) * 60 + MINUTE(SecondLectureTime) + SecondLectureDuration * 60) > ?)
                                                            )
                                                        )
                                                        
                                                            
                                                    )
                                                    AND CourseCode != ?
                                                ";

                                                // Prepare the SQL statement
                                                $stmt = $this->conn->prepare($query);

                                                // Bind parameters dynamically
                                                $stmt->bind_param(
                                                    "sssssssssssss",
                                                    // Main lecture vs. main lecture
                                                    $room, $day, $endTimeInMinutes, $startTimeInMinutes,
                                                    // Main lecture vs. lab
                                                    $room, $day, $endTimeInMinutes, $startTimeInMinutes,
                                                    // Main lecture vs. second lecture
                                                    $room, $day, $endTimeInMinutes, $startTimeInMinutes,
                                                    $excludeCourseCode
                                                    // Lab vs. main lecture
                                          
                                                );

                                                // Execute the query
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                // Return true if conflicts exist
                                                return $result->num_rows > 0;
                                            }



                public function deleteCourseById($courseId) {
                $query = "DELETE FROM course WHERE CourseCode = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("s", $courseId);
                return $stmt->execute();
            }



       public function getCoursesWithoutInstructor() {
        $query = "SELECT id, courseName FROM course WHERE instructorId IS NULL";
        $result = $this->conn->query($query);

        $courses = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
        }
        return $courses;
    }

    public function assignInstructorToCourse($courseId, $instructorId, $instructorName) {
        $query = "UPDATE course SET instructorId = ?, instructorName = ? WHERE CourseCode = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param('iss', $instructorId, $instructorName, $courseId);
        return $stmt->execute();
    }

    public function unassignInstructorFromCourse($courseId) {
    $query = "UPDATE Course SET instructorId = NULL, instructorName = NULL WHERE CourseCode = ?";
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return false;
    }
    $stmt->bind_param('s', $courseId);
    return $stmt->execute();
}
public function getCoursesByInstructor($instructorId) {
    $query = "SELECT id, courseName FROM Course WHERE instructorId = ?";
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return [];
    }
    $stmt->bind_param('i', $instructorId);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}
}

?>
