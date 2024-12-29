<?php
include_once '../models/ModelFactory.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = ModelFactory::create('User');
    }
  
   public function login($usernameOrEmail, $password) {
        $loggedInUser = $this->user->login($usernameOrEmail, $password);

        if ($loggedInUser) {
            $_SESSION['ID'] = $loggedInUser['ID'];
            $_SESSION['Name'] = $loggedInUser['Username'];
            $_SESSION['Email'] = $loggedInUser['Email'];
            $_SESSION['Usertype'] = $loggedInUser['Usertype'];
            header("Location: ../view/index.php");
            exit();
        } else {
            return "Invalid username/email or password!";
        }
    }

   
    private function sendWelcomeEmail($email, $username) {
    require '../include/PHPMailer/src/PHPMailer.php';
    require '../include/PHPMailer/src/SMTP.php';
    require '../include/PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'xdmegaa@gmail.com'; // Your SMTP username
        $mail->Password = 'muhk kwvi nkew eqcq';   // Your SMTP password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Typically 587 for TLS or 465 for SSL

        $mail->setFrom('xdmegaa@gmail.com', 'STMS');
        $mail->addAddress($email, $username);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Our Service!';
        $mail->Body = "
            <h1>Welcome, $username!</h1>
            <p>Thank you for signing up for our service. We're excited to have you on board.</p>
            <p>Feel free to explore and reach out if you need any help.</p>
            <p>Best Regards,</p>
            <p>STMS</p>
        ";
        $mail->AltBody = "Welcome, $username!\nThank you for signing up for our service. We're excited to have you on board.\nBest Regards,\nYour App Team";

        $mail->send();
    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
     



 public function registerUser($username, $email, $password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            echo "<script>alert('Passwords do not match.');</script>";
            return;
        }

         $signupResult = $this->user->signup($username, $email, $password);
          if ($signupResult) {
            $this->sendWelcomeEmail($email,$username);
            echo "<script>alert('User Created Successfully.');</script>";
        } else {
            echo "<script>alert('User Already Exists.');</script>";
        }
    }
   public function updateUserProfile($userID, $newName, $newEmail, $newPassword) {
        if (empty($newName) || empty($newEmail) ) {
            throw new Exception("All fields are required.");
        }

        if (strlen($newPassword) < 6&&$newPassword!=null) {
            throw new Exception("Password must be at least 6 characters long.");
        }if($newPassword===null){
              $result =   $this->user->updateUsernopw($userID, $newName, $newEmail);
        }else{
              $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
         $result = $this->user->updateUser($userID, $newName, $newEmail, $hashedPassword);
        }
      

        if ($result) {
            return true;
        } else {
            throw new Exception("Error updating profile.");
        }
    }

      public function handleScheduleSelection($studentId, $scheduleId = null) {
        if (!$scheduleId) {
            return [
                'schedules' =>   $this->user->getSchedules(),
                'selectedSchedule' =>   $this->user->getSelectedSchedule($studentId)
            ];
        }

        // Save the selected schedule
        $isSaved = $this->user->saveSelectedSchedule($studentId, $scheduleId);
        if ($isSaved) {
            echo "<script>alert('Schedule selected successfully!');</script>";
            header("Location: schedule.php");
            exit;
        } else {
            echo "<script>alert('Failed to save schedule. Please try again.');</script>";
        }
    }
      
     public function getStudentSchedule($studentId) {

        $selectedScheduleId = $this->user->getSelectedSchedule($studentId);

        if (!$selectedScheduleId) {
            echo "<script>alert('You have not selected a schedule yet. Please select a schedule.');</script>";
            header("Refresh: 0;URL=Scheduleselect.php");
            exit;
        }

        $courses = $this->user->getCoursesForSchedule($selectedScheduleId);

        // Generate weekly and monthly summaries
        $weekSummary = [];
        foreach ($courses as $course) {
            $courseName = $course['CourseName'];

            if (!isset($weekSummary[$courseName])) {
                $weekSummary[$courseName] = 0;
            }

            if (!empty($course['day'])) {
                $weekSummary[$courseName]++;
            }
            if (!empty($course['secondLectureDay'])) {
                $weekSummary[$courseName]++;
            }
            if (!empty($course['LabDay'])) {
                $weekSummary[$courseName]++;
            }
        }

        $monthSummary = [];
        foreach ($weekSummary as $course => $weeklyCount) {
            $monthSummary[$course] = $weeklyCount * 4;
        }

        return [
            'selectedScheduleId' => $selectedScheduleId,
            'courses' => $courses,
            'weekSummary' => $weekSummary,
            'monthSummary' => $monthSummary
        ];
    }
     
}
