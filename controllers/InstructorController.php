<?php
include_once '../models/ModelFactory.php';

class InstructorController {
    private $instructor;

    public function __construct() {
        $this->instructor = ModelFactory::create('Instructor');  
    }

    public function getCoursesAndSummaries($instructorId) {
        $courses = $this->instructor->getAssignedCourses($instructorId);

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
            $monthSummary[$course] = $weeklyCount * 4; // Assuming 4 weeks in a month
        }

        return [
            'courses' => $courses,
            'weekSummary' => $weekSummary,
            'monthSummary' => $monthSummary
        ];
    }



      public function getInstructorNameById($instructorid){
        return $this->instructor->getInstructorNameById($instructorid);
        

    }
}
