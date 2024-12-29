<?php
    include_once '../include/dbh.inc.php';
class User {
    
    private $conn;
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function login($usernameOrEmail, $password) {
        $query = "SELECT * FROM user WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        
        if ($user && password_verify($password, $user['Password'])) {
            return $user;  
        }
        return null;  
    }

    public function signup($username, $email, $password,$usertype='User') {
        if ($this->userExists($username, $email)) {
            return false;  
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO user (Username, Email, Password,Usertype) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssss', $username, $email, $hashedPassword,$usertype);
        
        return $stmt->execute();  
    }

     public function updateUser($userID, $newName, $newEmail, $hashedPassword) {
        // Check if the username or email already exists in other users
        $query = "SELECT ID FROM user WHERE (Username = ? OR Email = ?) AND ID != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssi', $newName, $newEmail, $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return false; // Username or email already exists for a different user
        }
        $userID = $this->conn->real_escape_string($userID);
        $newName = $this->conn->real_escape_string($newName);
        $newEmail = $this->conn->real_escape_string($newEmail);
      
        $sql = "UPDATE user SET Username='$newName', Email='$newEmail', Password='$hashedPassword' WHERE ID='$userID'";


    return $this->conn->query($sql);
    }

        public function updateUsernopw($userID, $newName, $newEmail) {
            // Check if the username or email already exists in other users
        $query = "SELECT ID FROM user WHERE (Username = ? OR Email = ?) AND ID != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssi', $newName, $newEmail, $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return false; // Username or email already exists for a different user
        }
        $userID = $this->conn->real_escape_string($userID);
        $newName = $this->conn->real_escape_string($newName);
        $newEmail = $this->conn->real_escape_string($newEmail);
      

                 $sql = "UPDATE user SET Username='$newName', Email='$newEmail' WHERE ID='$userID'";
           
    return $this->conn->query($sql);
    }

     private function userExists($username, $email) {
        $query = "SELECT id FROM user WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        return $stmt->num_rows > 0;  
    }

      public function getSchedules() {
        $query = "SELECT DISTINCT schedule_id FROM schedule";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

  public function getSelectedSchedule($studentId) {
    $query = "SELECT scheduleId FROM StudentSchedule WHERE studentId = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['scheduleId'] ?? null;
}

     public function getCoursesForSchedule($scheduleId) {
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
            FROM schedule s
            JOIN course c ON s.course_code = c.CourseCode
            WHERE s.schedule_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $scheduleId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function saveSelectedSchedule($studentId, $scheduleId) {
        $selectedSchedule = $this->getSelectedSchedule($studentId);
        if ($selectedSchedule) {
            $query = "UPDATE StudentSchedule SET scheduleId = ? WHERE studentId = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $scheduleId, $studentId);
            return $stmt->execute();
        } else {
            $query = "INSERT INTO StudentSchedule (studentId, scheduleId) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("is", $studentId, $scheduleId);
            return $stmt->execute();
        }
    }
}
