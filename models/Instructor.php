<?php
    include_once '../include/dbh.inc.php';
class Instructor {
    
    private $conn;
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
        
    public function getInstructorNameById($instructorid){
        $query = "SELECT Username FROM user WHERE ID=? AND Usertype='Instructor'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $instructorid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
    return $row['Username'] ?? null;

    }
       public function getAssignedCourses($instructorId) {
        $query = "
            SELECT 
                c.CourseName, 
                c.Room, 
                c.StartTime, 
                c.Duration, 
                c.InstructorName, 
                c.day, 
                c.labTime, 
                c.labDuration, 
                c.secondLectureTime, 
                c.secondLectureDuration, 
                c.LabDay, 
                c.secondLectureDay,
                c.LabRoom,
                c.SecondLectureRoom
            FROM course c
            WHERE c.instructorId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $instructorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
