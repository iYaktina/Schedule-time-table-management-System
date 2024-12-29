<?php
include_once '../models/ModelFactory.php';

class CourseController {
      private $course;
     public function __construct() {
        // Instantiate the Course model
        $this->course = ModelFactory::create('Course');
    }

    public function addCourse(
    $courseName, $year, $courseTime, $courseDuration, $courseRoom, $creditHours, $selected_day,
    $courseCode, $prerequisiteId = null, $labTime = null, $labDuration = null, $labDay = null, $labRoom = null,
    $secondLectureTime = null, $secondLectureDuration = null, $secondLectureDay = null, $secondLectureRoom = null) {
    return $this->course->addCourse(
        $courseName, 
        $year, 
        $courseTime, 
        $courseDuration, 
        $courseRoom, 
        $creditHours, 
        $selected_day, 
        $courseCode, 
        $prerequisiteId, 
        $labTime, 
        $labDuration, 
        $labDay, 
        $labRoom, 
        $secondLectureTime, 
        $secondLectureDuration, 
        $secondLectureDay, 
        $secondLectureRoom
    );
}

     public function updateCourse(
        $idchecker, $courseName, $year, $courseDuration, $courseTime, 
        $courseRoom, $creditHours, $selected_day, $courseCode, 
        $prerequisiteId = null, $newInstructorId = null, $newInstructorname = null, 
        $labTime = null, $labDuration = null, $labDay=null, $labRoom = null, 
        $secondLectureTime = null, $secondLectureDuration = null,$secondLectureDay=null, $secondLectureRoom = null) {

    $updateResult = $this->course->editCourse(
        $idchecker,
        $courseName, 
        $year, 
        $courseTime,  
        $courseDuration, 
        $courseRoom, 
        $creditHours, 
        $selected_day, 
        $courseCode, 
        $prerequisiteId, 
        $newInstructorId, 
        $newInstructorname,
        $labTime, // Added lab time
        $labDuration,
        $labRoom, 
        $labDay,
        $secondLectureTime, // Added second lecture time
        $secondLectureDuration,
        $secondLectureRoom,
        $secondLectureDay
    );

    if ($updateResult) {
        echo "<script>alert('Course updated successfully!');</script>";
        header("Location: Admin.php");
        exit();
    } else {
        echo "<script>alert('Error updating course.');</script>";
    }
}

     public function deleteCourse($courseId) {
        if (!empty($courseId)) {

            $result = $this->course->deleteCourseById($courseId);
            
            if ($result) {
                echo "<script>alert('Delete Successful!');</script>";
                header("Location: Admin.php");
                exit();
            } else {
                echo "<script>alert('Error deleting course.');</script>";
            }
        } else {
            echo "<script>alert('No course selected to delete.');</script>";
        }
    }

      public function getCoursesWithoutInstructor() {
        return $this->course->getCoursesWithoutInstructor();
    }

    public function assignInstructor($courseId, $instructorId, $instructorName) {
        return $this->course->assignInstructorToCourse($courseId, $instructorId, $instructorName);
    }

    public function unassignInstructor($courseId) {
    return $this->course->unassignInstructorFromCourse($courseId);
}   
         public function getCoursesByInstructor($instructorId) {
        return $this->course->getCoursesByInstructor($instructorId);
    }

}
?>
