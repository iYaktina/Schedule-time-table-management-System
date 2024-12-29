<?php
include_once '../models/ModelFactory.php';

class AdminController {
    private $admin;
            public function __construct() {
                    $this->admin = ModelFactory::create('Admin');
                }
                public function getSelectedCoursesForSchedule($scheduleId) {
                    return $this->admin->getCoursesByScheduleId($scheduleId);
                }
                
                public function updateScheduleCourses($scheduleId, $newCourses) {
                    return $this->admin->updateScheduleCourses($scheduleId, $newCourses);
                }


                 public function createSchedule($schedule_id,$course_code) {
        $result = $this->admin->createSchedule($schedule_id,$course_code);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
      public function getScheduleData($scheduleId) {
        $courses = $this->admin->getCoursesByScheduleId($scheduleId);

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
            'courses' => $courses,
            'weekSummary' => $weekSummary,
            'monthSummary' => $monthSummary
        ];
    }

    public function getTodayAndTomorrowEvents($scheduleId) {
        $today = date('l');
        $tomorrow = date('l', strtotime('+1 day'));

        $todayEvents = $this->admin->getTodayEvents($today,$scheduleId);
        $tomorrowEvents = $this->admin->getTodayEvents($tomorrow,$scheduleId);

        return [
            'todayEvents' => $todayEvents,
            'tomorrowEvents' => $tomorrowEvents
        ];
    }
       public function addUser($username, $password, $email, $usertype) {
         $signupResult = $this->admin->signup($username, $email, $password,$usertype);
          if ($signupResult) {
            return true;
        } else {
            return false;
        }
    }
         public function deleteUser($userId) {
         $deletionResult = $this->admin->deleteuser($userId);
          if ($deletionResult) {
            return true;
        } else {
            return false;
        }
    }   

       public function deleteSchedule($scheduleId) {
         $deletionResult = $this->admin->deleteschedule($scheduleId);
          if ($deletionResult) {
            return true;
        } else {
            return false;
        }
    }

         public function editUser($userid,$username, $password, $email, $usertype) {
         $editResult = $this->admin->edituser($userid,$username, $email, $password,$usertype);
          if ($editResult) {
            return true;
        } else {
            return false;
        }
    }
    public function sendScheduleAnnouncement() {
         return $this->admin->sendScheduleAnnouncement();
    }
     
      public function getScheduleAccessStatus() {
        return $this->admin->getScheduleAccessStatus();
    }
     public function endScheduleSelection() {
   
    $result = $this->admin->updateScheduleAccess(0);

    // Return success or failure
    return $result ? true : false;
}

public function getInstructors() {

    return $this->admin->getAllInstructors();
}

}
