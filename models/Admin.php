<?php
    include_once '../include/dbh.inc.php';
class Admin {
    
    private $conn;
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

     public function getAllUsers() {
        $query = "SELECT Username, Email FROM user WHERE UserType = 'User' ";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update schedule access status
    public function updateScheduleAccess($status) {
        $query = "UPDATE settings SET schedule_access = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $status);
        return $stmt->execute();
    }

    // Get current schedule access status
    public function getScheduleAccessStatus() {
        $query = "SELECT schedule_access FROM settings";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['schedule_access'];
    }

    public function saveToDatabase() {
        $query = "INSERT INTO user (Username, Password, Email, UserType) VALUES 
                  ('{$this->username}', '{$this->password}', '{$this->email}', '{$this->userType}')";
        return $this->conn->query($query);
    }

  public function getCoursesByScheduleId($scheduleId) {
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

        public function getTodayEvents($day, $scheduleId) {
            $query = "SELECT * FROM course 
                    JOIN schedule ON course.CourseCode = schedule.course_code
                    WHERE (course.Day = ?  OR course.LabDay = ?  OR course.secondLectureDay = ? ) AND schedule.schedule_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssss", $day,$day,$day, $scheduleId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }


    public function signup($username, $email, $password, $usertype = 'User') {
    // Check if the user already exists
    if ($this->userExists($username, $email)) {
        return false; // User already exists
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $query = "INSERT INTO user (Username, Email, Password, UserType) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssss', $username, $email, $hashedPassword, $usertype);

    return $stmt->execute(); // Return true on success, false on failure
}

public function editUser($userid, $username, $email, $password, $usertype) {
    // Check if the username or email already exists in other users
    $query = "SELECT ID FROM user WHERE (Username = ? OR Email = ?) AND ID != ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssi', $username, $email, $userid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return false; // Username or email already exists for a different user
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Proceed with the update
    $query = "UPDATE user SET Username = ?, Email = ?, Password = ?, UserType = ? WHERE ID = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ssssi', $username, $email, $hashedPassword, $usertype, $userid);

    return $stmt->execute(); // Return true on success, false on failure
}


private function userExists($username, $email) {
    $query = "SELECT id FROM user WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();

    error_log("Query executed: {$query}");
    error_log("Parameters: username={$username}, email={$email}");
    error_log("Rows found: " . $stmt->num_rows);

    return $stmt->num_rows > 0;
}


 public function deleteuser($userId) {
    $query = "DELETE FROM user  WHERE ID = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('i', $userId);
    return $stmt->execute(); 
}
 public function deleteschedule($scheduleid) {
    $query = "DELETE FROM schedule  WHERE schedule_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $scheduleid);
    return $stmt->execute(); 
}

 public function createSchedule($schedule_id, $selectedCourses) {
    $values = [];
    foreach ($selectedCourses as $course_code) {
        $values[] = "('$schedule_id', '$course_code')";
    }

    $valuesString = implode(", ", $values);

    $query = "INSERT INTO schedule (schedule_id, course_code) VALUES $valuesString";

    if ($this->conn->query($query)) {
        return true;
    } else {
        die("Error executing query: " . $this->conn->error);  
    }
}



 public function updateScheduleCourses($schedule_id, $selectedCourses) {
    $values = [];
    foreach ($selectedCourses as $course_code) {
        $values[] = "('$schedule_id', '$course_code')";
    }

    $valuesString = implode(", ", $values);

    $query = "INSERT INTO schedule (schedule_id, course_code) VALUES $valuesString";

    if ($this->conn->query($query)) {
        $this->sendScheduleUpdateEmailsToAllUsers($schedule_id);
        return true;
    } else {
        die("Error executing query: " . $this->conn->error);
    }
}

private function sendScheduleUpdateEmailsToAllUsers($schedule_id) {
    // Fetch all unique users
    $query = "SELECT DISTINCT Email, Username FROM user WHERE Usertype='User'";
    $result = $this->conn->query($query);

    if ($result && $result->num_rows > 0) {
        require '../include/PHPMailer/src/PHPMailer.php';
        require '../include/PHPMailer/src/SMTP.php';
        require '../include/PHPMailer/src/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'xdmegaa@gmail.com'; // Your SMTP username
            $mail->Password = 'muhk kwvi nkew eqcq'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email sender details
            $mail->setFrom('xdmegaa@gmail.com', 'University Admin');

            // Email subject and body
            $subject = 'Schedule Update Notification';
            $body = "Dear user,<br><br>The schedule with ID <b>$schedule_id</b> has been updated. Please log in to your account to review the changes.<br><br>Best regards,<br>University Administration";

            // Send emails to all unique users
            while ($row = $result->fetch_assoc()) {
                $mail->addAddress($row['Email'], $row['Username']);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->isHTML(true);

                // Attempt to send the email
                $mail->send();

                // Clear addresses for the next iteration
                $mail->clearAddresses();
            }

        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
        }
    }
}



 public function sendScheduleAnnouncement() {
    $users = $this->getAllUsers();
    $uniqueUsers = [];
    foreach ($users as $user) {
        $email = $user['Email'];
        if (!isset($uniqueUsers[$email])) {
            $uniqueUsers[$email] = $user;
        }
    }

    require '../include/PHPMailer/src/PHPMailer.php';
    require '../include/PHPMailer/src/SMTP.php';
    require '../include/PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        // Set up SMTP server (replace with your SMTP credentials)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'xdmegaa@gmail.com'; // Your SMTP username
        $mail->Password = 'muhk kwvi nkew eqcq';   // Your SMTP password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set the email sender
        $mail->setFrom('xdmegaa@gmail.com', 'University Admin'); // Replace with your sender email

        // Email subject and body
        $subject = 'Schedule Selection is Now Open';
        $body = 'Dear User,<br><br>We are excited to announce that the schedule selection process is now open. Please log in to your account to select your schedule.<br><br>Best regards,<br>University Team';

        // Send email to all unique users
        foreach ($uniqueUsers as $user) {
            $mail->addAddress($user['Email'], $user['Username']);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->isHTML(true);

            // Send the email
            $mail->send();

            // Clear addresses for the next iteration
            $mail->clearAddresses();
        }

        // Update schedule access in the settings table via the model
        return $this->updateScheduleAccess(true);
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}


public function getAllInstructors() {
    $query = "SELECT ID, Username FROM user WHERE UserType = 'Instructor'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
}
