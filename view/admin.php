<?php
    include('menu.php');
    include_once '../controllers/AdminController.php';

    if (!isset($_SESSION['ID'])) {
        echo "<script>alert('Please Login!');</script>";
        header("Refresh: 0;URL=login.php");
    }
    if($_SESSION['Usertype'] != 'Admin'){
        echo "<script>alert('Classified for admins only');</script>";
        header("Refresh: 0;URL=index.php");
    }
    $adminController = new AdminController();
    $instructors = $adminController->getInstructors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(form => form.style.display = 'none');
            document.getElementById(formId).style.display = 'block';
        }

      function validatePassword(Fieldid) {
    const passwordField = document.getElementById(Fieldid);

    if (passwordField.value.length < 6) {
        alert('Password must be at least 6 characters long.');
        passwordField.focus(); 
        return false; 
    }
    return true; 
}
function togglePasswordVisibility(fieldId, buttonId) {
    const passwordField = document.getElementById(fieldId);
    const toggleButton = document.getElementById(buttonId);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text'; 
        toggleButton.innerHTML = '🙈'; 
    } else {
        passwordField.type = 'password'; 
        toggleButton.innerHTML = '👁️'; 
    }
}

 function validateTime() {
    const courseTime = document.getElementById('courseTime').value;
    const courseDuration = document.getElementById('courseDuration').value;
    const courseDay = document.getElementById('courseDay').value; // Assuming you have a field for the day of the course
    const labTime = document.getElementById('labTime').value; // Assuming lab time is input field
    const labDuration = document.getElementById('labDuration').value; // Assuming lab duration is input field
    const labDay = document.getElementById('labday').value; // Assuming lab day is input field
    const secondLectureTime = document.getElementById('secondLectureTime').value; // Assuming second lecture time
    const secondLectureDuration = document.getElementById('secondLectureDuration').value; // Assuming second lecture duration
    const secondLectureDay = document.getElementById('secondlecday').value; // Assuming second lecture day

    const [startHour, startMinute] = courseTime.split(':').map(Number);
    const duration = parseInt(courseDuration, 10);

    // Check if the course starts within the allowed time range (9 AM - 4 PM)
    if (startHour < 9 || startHour >= 16 || (startHour === 16 && startMinute > 0)) {
        alert("The course must start between 9 AM and 4 PM.");
        return false;
    }

    // Check if the duration exceeds the maximum allowed (3 hours)
    if (duration > 3) {
        alert("The maximum duration for the course is 3 hours or less.");
        return false;
    }

    // Calculate the end time of the course
    const endHour = startHour + Math.floor((startMinute + duration * 60) / 60);
    const endMinute = (startMinute + duration * 60) % 60;

    // Check if the course ends before 6 PM
    if (endHour > 18 || (endHour === 18 && endMinute > 0)) {
        alert("The course must end before 6 PM.");
        return false;
    }

    // Check if the course start time is aligned to 00, 15, or 30 minutes
    if (startMinute !== 0 && startMinute !== 15 && startMinute !== 30) {
        alert("Minutes must be 00, 15, or 30.");
        return false;
    }

    // Validate lab time if provided
    if (labTime && !validateLabOrSecondLectureTime(labTime, labDuration, labDay, courseDay, 'Lab')) {
        return false;
    }

    // Validate second lecture time if provided
    if (secondLectureTime && !validateLabOrSecondLectureTime(secondLectureTime, secondLectureDuration, secondLectureDay, courseDay, 'Second Lecture')) {
        return false;
    }

    // Check for conflicts between the main course and lab/second lecture
    if (isTimeConflict(courseDay, startHour, startMinute, duration, labDay, labTime, labDuration, secondLectureDay, secondLectureTime, secondLectureDuration)) {
        alert("There is a conflict between the course, lab, or second lecture.");
        return false;
    }

    return true;
}

// Function to validate lab and second lecture time (same logic for both)
function validateLabOrSecondLectureTime(time, duration, lectureDay, courseDay, lectureType) {
    const [startHour, startMinute] = time.split(':').map(Number);
    const durationInt = parseInt(duration, 10);

    // Check if the lecture starts within the allowed time range (9 AM - 4 PM)
    if (startHour < 9 || startHour >= 16 || (startHour === 16 && startMinute > 0)) {
        alert(`${lectureType} must start between 9 AM and 4 PM.`);
        return false;
    }

    // Check if the duration exceeds the maximum allowed (3 hours)
    if (durationInt > 3) {
        alert(`${lectureType} maximum duration is 3 hours or less.`);
        return false;
    }

    // Calculate the end time of the lab or second lecture
    const endHour = startHour + Math.floor((startMinute + durationInt * 60) / 60);
    const endMinute = (startMinute + durationInt * 60) % 60;

    // Check if the lab/second lecture ends before 6 PM
    if (endHour > 18 || (endHour === 18 && endMinute > 0)) {
        alert(`${lectureType} must end before 6 PM.`);
        return false;
    }

    // Check if the lab/second lecture day matches the course day
    if (lectureDay !== courseDay) {
        alert(`${lectureType} day must match the course day.`);
        return false;
    }

    return true;
}

// Function to check if the selected course time conflicts with the lab or second lecture time
function isTimeConflict(courseDay, startHour, startMinute, duration, labDay, labTime, labDuration, secondLectureDay, secondLectureTime, secondLectureDuration) {
    const courseStartTimeInMinutes = startHour * 60 + startMinute;
    const courseEndTimeInMinutes = courseStartTimeInMinutes + (duration * 60);

    // Validate lab time conflict if lab time is provided
    if (labTime) {
        const [labStartHour, labStartMinute] = labTime.split(':').map(Number);
        const labDurationInt = parseInt(labDuration, 10);
        const labStartTimeInMinutes = labStartHour * 60 + labStartMinute;
        const labEndTimeInMinutes = labStartTimeInMinutes + (labDurationInt * 60);

        // Check for conflict with course time
        if (courseDay === labDay && 
            ((courseStartTimeInMinutes < labEndTimeInMinutes && courseEndTimeInMinutes > labStartTimeInMinutes))) {
            return true;
        }
    }

    // Validate second lecture time conflict if second lecture time is provided
    if (secondLectureTime) {
        const [secondLectureStartHour, secondLectureStartMinute] = secondLectureTime.split(':').map(Number);
        const secondLectureDurationInt = parseInt(secondLectureDuration, 10);
        const secondLectureStartTimeInMinutes = secondLectureStartHour * 60 + secondLectureStartMinute;
        const secondLectureEndTimeInMinutes = secondLectureStartTimeInMinutes + (secondLectureDurationInt * 60);

        // Check for conflict with course time
        if (courseDay === secondLectureDay &&
            ((courseStartTimeInMinutes < secondLectureEndTimeInMinutes && courseEndTimeInMinutes > secondLectureStartTimeInMinutes))) {
            return true;
        }
    }

    return false;
}

   function toggleLabFields() {
    const labFields = document.getElementById('labFields');
    const hasLab = document.getElementById('hasLab');

  
    if (hasLab.checked) {
        labFields.style.display = 'block';
        document.getElementById('labTime').setAttribute('required', 'true');
        document.getElementById('labDuration').setAttribute('required', 'true');
        document.getElementById('labRoom').setAttribute('required', 'true');
        document.getElementById('labday').setAttribute('required', 'true');
    } else {
        labFields.style.display = 'none';
        document.getElementById('labTime').value = ''; // Set value to null
        document.getElementById('labTime').removeAttribute('required');

        document.getElementById('labDuration').value = ''; // Set value to null
        document.getElementById('labDuration').removeAttribute('required');

        document.getElementById('labRoom').value = ''; // Set value to null
        document.getElementById('labRoom').removeAttribute('required');

        document.getElementById('labday').value = ''; // Set value to null
        document.getElementById('labday').removeAttribute('required');
    }
}

function toggleSecondLectureFields() {
    const secondLectureFields = document.getElementById('secondLectureFields');
    const secondLecture = document.getElementById('secondLecture');

   
    if (secondLecture.checked) {
        secondLectureFields.style.display = 'block';
        document.getElementById('secondLectureTime').setAttribute('required', 'true');
        document.getElementById('secondLectureDuration').setAttribute('required', 'true');
        document.getElementById('secondLectureRoom').setAttribute('required', 'true');
        document.getElementById('secondlecday').setAttribute('required', 'true');
    } else {
        secondLectureFields.style.display = 'none';
        document.getElementById('secondLectureTime').value = ''; // Set value to null
        document.getElementById('secondLectureTime').removeAttribute('required');

        document.getElementById('secondLectureDuration').value = ''; // Set value to null
        document.getElementById('secondLectureDuration').removeAttribute('required');

        document.getElementById('secondLectureRoom').value = ''; // Set value to null
        document.getElementById('secondLectureRoom').removeAttribute('required');

        document.getElementById('secondlecday').value = ''; // Set value to null
        document.getElementById('secondlecday').removeAttribute('required');
    }
}
        function toggleInstructorFields() {
    const hasInstructor = document.getElementById('hasInstructor').checked;
    document.getElementById('instructorFields').style.display = hasInstructor ? 'block' : 'none';
        }





        function toggleLabFieldsEdit() {
            const labFields = document.getElementById('labFields1');
            const hasLab = document.getElementById('hasLab1');

            if (hasLab.checked) {
                labFields.style.display = 'block';
                document.getElementById('labTime1').setAttribute('required', 'true');
                document.getElementById('labDuration1').setAttribute('required', 'true');
                document.getElementById('labRoom1').setAttribute('required', 'true');
                document.getElementById('labday1').setAttribute('required', 'true');
            } else {
                labFields.style.display = 'none';
                document.getElementById('labTime1').value = ''; // Set value to null
                document.getElementById('labTime1').removeAttribute('required');

                document.getElementById('labDuration1').value = ''; // Set value to null
                document.getElementById('labDuration1').removeAttribute('required');

                document.getElementById('labRoom1').value = ''; // Set value to null
                document.getElementById('labRoom1').removeAttribute('required');

                document.getElementById('labday1').value = ''; // Set value to null
                document.getElementById('labday1').removeAttribute('required');
            }
        }

        function toggleSecondLectureFieldsEdit() {
            const secondLectureFields = document.getElementById('secondLectureFields1');
            const secondLecture = document.getElementById('secondLecture1');

            if (secondLecture.checked) {
                secondLectureFields.style.display = 'block';
                document.getElementById('secondLectureTime1').setAttribute('required', 'true');
                document.getElementById('secondLectureDuration1').setAttribute('required', 'true');
                document.getElementById('secondLectureRoom1').setAttribute('required', 'true');
                document.getElementById('secondlecday1').setAttribute('required', 'true');
            } else {
                secondLectureFields.style.display = 'none';
                document.getElementById('secondLectureTime1').value = ''; // Set value to null
                document.getElementById('secondLectureTime1').removeAttribute('required');

                document.getElementById('secondLectureDuration1').value = ''; // Set value to null
                document.getElementById('secondLectureDuration1').removeAttribute('required');

                document.getElementById('secondLectureRoom1').value = ''; // Set value to null
                document.getElementById('secondLectureRoom1').removeAttribute('required');

                document.getElementById('secondlecday1').value = ''; // Set value to null
                document.getElementById('secondlecday1').removeAttribute('required');
            }
        }

        function toggleInstructorFieldsEdit() {
            const instructorFields = document.getElementById('instructorFields1');
            const hasInstructor = document.getElementById('hasInstructor1');

            if (hasInstructor.checked) {
                instructorFields.style.display = 'block';
                document.getElementById('instructorSelect1').setAttribute('required', 'true');
            } else {
                instructorFields.style.display = 'none';
                document.getElementById('instructorSelect1').value = ''; // Set value to null
                document.getElementById('instructorSelect1').removeAttribute('required');
            }
        }
    </script>
</head>
<body>

<div class="profile-container">
    <div class="header">
        <h2>Admin Panel</h2>
        <img src="../assets/images/star.png" alt="Profile Icon" class="profile-icon">
    </div>

    <div class="button-container">
        <button onclick="showForm('addUser')">Add User</button>
        <button onclick="showForm('userList')">Get Users</button>
        <button onclick="showForm('addCourse')">Add Course</button>
        <button onclick="showForm('CourseList')">Get Courses</button>

    </div>

    <div id="addUser" class="form-container">
        <h3>Add User</h3>
        <form action="add_user.php" method="POST" onsubmit="return validatePassword('password')">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>
            
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <button type="button" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')">👁️</button>
             </div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter Email" required>
                        
                    <label for="usertype">Usertype</label>
            <select id="usertype" name="usertype" required>
                <option value="admin">Admin</option>
                <option value="instructor">Instructor</option>
                <option value="user">User</option>
            </select>

            <button type="submit">Add User</button>
                  <button type="button" onclick="window.location.href='?';">Back</button>
        </form>
    </div>

       <div id="addCourse" class="form-container">
    <h3>Add Course</h3>
    <form action="add_course.php" method="POST" onsubmit="return validateTime();">
         <label for="course_name">Course Name</label>
        <input type="text" id="course_name" name="course_name" placeholder="Enter Course Name" required>

        <label for="year">Year</label>
        <input type="text" id="year" name="year" placeholder="Enter Year" required>

        <label for="courseTime">Course Start Time</label>
        <input type="time" id="courseTime" name="courseTime" required>

        <label for="courseDuration">Course Duration (hours)</label>
        <input type="number" id="courseDuration" name="courseDuration" placeholder="Enter Duration in hours" required>

        <label for="courseRoom">Course Room</label>
        <input type="text" id="courseRoom" name="courseRoom" placeholder="Enter Room" required>

        <label for="creditHours">Course Credit Hours</label>
        <input type="number" id="creditHours" name="creditHours" placeholder="Enter Credit Hours" required>

        <label for="day">Day</label>
        <select id="day" name="day" required>
            <option value="">Select a day</option>
            <option value="Sunday">Sunday</option> 
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
        </select>

        <label for="courseCode">Course Code</label>
        <input type="text" id="courseCode" name="courseCode" placeholder="Enter Course Code" required>

        <label for="prerequisiteId">Prerequisite Course:</label>
        <select id="prerequisiteId" name="prerequisiteId">
            <option value=""></option>
            <?php 
               include_once '../include/dbh.inc.php'; 
               $query = "SELECT * FROM course";
               $result = mysqli_query(Database::getInstance()->getConnection(), $query);      
               if ($result && mysqli_num_rows($result) > 0) {
                   while ($row = mysqli_fetch_assoc($result)) {
                       echo "<option value='" . $row['CourseCode'] . "'>" .$row['CourseName'] . "  -  ". $row['CourseCode'] . "</option>";
                   }
               } else {
                   echo "<option value=''>No courses available</option>";
               }
            ?>
        </select>
        
        <!-- Add Lab Fields if needed -->
        <label for="hasLab">Does the course have a lab?</label>
        <input type="checkbox" id="hasLab" name="hasLab" onchange="toggleLabFields()">

        <div id="labFields" style="display:none;">
            <label for="labTime">Lab Start Time</label>
            <input type="time" id="labTime" name="labTime">

            <label for="labDuration">Lab Duration (hours)</label>
            <input type="number" id="labDuration" name="labDuration" placeholder="Enter Lab Duration in hours">
            
            <label for="labRoom">Lab Room </label>
            <input type="text" id="labRoom" name="labRoom" placeholder="Enter Lab Room">

            <label for="labday">Day</label>
            <select id="labday" name="labday" >
                <option value="">Select a day</option>
                <option value="Sunday">Sunday</option> 
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
            </select>
        </div>

        <label for="secondLecture">Does this course have a second lecture?</label>
        <input type="checkbox" id="secondLecture" name="secondLecture" onchange="toggleSecondLectureFields()">

        <div id="secondLectureFields" style="display:none;">
            <label for="secondLectureTime">Second Lecture Start Time</label>
            <input type="time" id="secondLectureTime" name="secondLectureTime">

            <label for="secondLectureDuration">Second Lecture Duration (hours)</label>
            <input type="number" id="secondLectureDuration" name="secondLectureDuration" placeholder="Enter Second Lecture Duration in hours">

            <label for="secondLectureRoom">Second Lecture Room</label>
            <input type="text" id="secondLectureRoom" name="secondLectureRoom" placeholder="Enter Second Lecture Room">

            <label for="secondlecday">Day</label>
            <select id="secondlecday" name="secondlecday" >
                <option value="">Select a day</option>
                <option value="Sunday">Sunday</option> 
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
            </select>
        </div>
                  <!-- Instructor Fields -->
                    <label for="hasInstructor">Does this course have an instructor?</label>
                    <input type="checkbox" id="hasInstructor" name="hasInstructor" onchange="toggleInstructorFields()" <?php echo !empty($course['InstructorName']) ? 'checked' : ''; ?>>

                                    <div id="instructorFields" style="display: <?php echo !empty($course['InstructorName']) ? 'block' : 'none'; ?>;">
                        <label for="instructorSelect">Select Instructor</label>
                        <select id="instructorSelect" name="instructorId">
                            <option value="">Select an Instructor</option>
                             <?php if (!empty($instructors)) : ?>
                                <?php foreach ($instructors as $instructor) : ?>
                                    <?php $selected = isset($course['InstructorId']) && $course['InstructorId'] == $instructor['ID'] ? 'selected' : ''; ?>
                                    <option value="<?php echo $instructor['ID']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($instructor['Username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">No instructors available</option>
                            <?php endif; ?>
                        </select>
                    </div>
        <button type="submit">Add Course</button>
        <button type="button" onclick="window.location.href='?';">Back</button>
    </form>
</div>

<div id="CourseList" class="form-container">
 <h3>All Courses</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Room</th>
                    <th>Year</th>
                    <th>Time</th>
                    <th>Instructor Name</th>
                    <th>Actions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = Database::getInstance()->getConnection()->query("SELECT CourseCode, CourseName, Room, Year,StartTime,InstructorName,Duration FROM course");
                while ($row = $result->fetch_assoc()) {
                      list($startHour, $startMinute) = explode(':', $row['StartTime']);
                $duration = $row['Duration']; 

                $endHour = $startHour + $duration;
                $endMinute = $startMinute;

                if ($endHour >= 24) {
                    $endHour -= 24;
                }

                $startTimeFormatted = $row['StartTime']; 
                $endTimeFormatted = sprintf('%02d:%02d', $endHour, $endMinute); 
                    echo "<tr>";
                    echo "<td>{$row['CourseCode']}</td>";
                    echo "<td>{$row['CourseName']}</td>";
                    echo "<td>{$row['Room']}</td>";
                    echo "<td>{$row['Year']}</td>";
                    echo "<td>{$startTimeFormatted} - {$endTimeFormatted}</td>"; 
                    echo "<td>{$row['InstructorName']}</td>";
                    echo "<td><a href='?course_id={$row['CourseCode']}' class='edit-button'>Edit</a></td>";
                    echo "<td>
                              <a href='delete_course.php?course_id={$row['CourseCode']}' class='edit-button' >
                                 Delete
                                </a>
                            </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
            <button type="button"  onclick="window.location.href='?';">Back</button>
</div>
  
<div id="editCourseForm" class="form-container" style="display: <?php echo isset($_GET['course_id']) ? 'block' : 'none'; ?>">
        <h3>Edit Course</h3>
        <?php
            if (isset($_GET['course_id'])) {
                $conn = Database::getInstance()->getConnection();
                $id = $_GET['course_id'];
                $id = $conn->real_escape_string($id); // Escape special characters for safety

                // Updated query to include all fields
                $query = "SELECT CourseCode, CourseName, Year, StartTime, Duration, Room, InstructorName,InstructorId, CreditHour, Day, PrerequisiteId, LabTime, LabDuration, LabRoom, LabDay, SecondLectureTime, SecondLectureDuration, SecondLectureRoom, SecondLectureDay FROM course WHERE CourseCode = '$id'";

                $course_result = $conn->query($query);

                if (!$course_result) {
                    die("Query failed: " . $conn->error . " | Query: " . $query);
                }

                $course = $course_result->fetch_assoc();

                // Handle NULL values: Replace with empty strings for display in the form
                foreach ($course as $key => $value) {
                    if (is_null($value)) {
                        $course[$key] = ""; // Set to an empty string if the value is NULL
                    }
                }
            }
            ?>
        <form action="edit_course.php" method="POST" onsubmit="return validateTime();">
        <!-- Hidden field to store Course ID -->
        <input type="hidden" id="course_id" name="course_id" value="<?php echo $course['CourseCode']; ?>">

        <label for="course_name">Course Name</label>
        <input type="text" id="course_name" name="course_name" placeholder="Enter Course Name" value="<?php echo $course['CourseName']; ?>" required>

        <label for="year">Year</label>
        <input type="text" id="year" name="year" placeholder="Enter Year" value="<?php echo $course['Year']; ?>" required>

        <label for="courseTime">Course Start Time</label>
        <input type="time" id="courseTime" name="courseTime" value="<?php echo $course['StartTime']; ?>" required>

        <label for="courseDuration">Course Duration (hours)</label>
        <input type="number" id="courseDuration" name="courseDuration" placeholder="Enter Duration in hours" value="<?php echo $course['Duration']; ?>" required>

        <label for="courseRoom">Course Room</label>
        <input type="text" id="courseRoom" name="courseRoom" placeholder="Enter Room" value="<?php echo $course['Room']; ?>" required>

        <label for="creditHours">Course Credit Hours</label>
        <input type="number" id="creditHours" name="creditHours" placeholder="Enter Credit Hours" value="<?php echo $course['CreditHour']; ?>" required>

        <label for="day">Day</label>
        <select id="day" name="day" required>
            <option value="">Select a day</option>
            <?php $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            foreach ($days as $day) {
                $selected = $day == $course['Day'] ? 'selected' : '';
                echo "<option value='$day' $selected>$day</option>";
            } ?>
        </select>

        <label for="courseCode">Course Code</label>
        <input type="text" id="courseCode" name="courseCode" placeholder="Enter Course Code" value="<?php echo $course['CourseCode']; ?>" required>

        <label for="prerequisiteId">Prerequisite Course:</label>
        <select id="prerequisiteId" name="prerequisiteId">
            <option value=""></option>
            <?php
               $query = "SELECT * FROM course";
               $result = mysqli_query(Database::getInstance()->getConnection(), $query);      
               if ($result && mysqli_num_rows($result) > 0) {
                   while ($row = mysqli_fetch_assoc($result)) {
                       $selected = $row['CourseCode'] == $course['PrerequisiteId'] ? 'selected' : '';
                       echo "<option value='" . $row['CourseCode'] . "' $selected>" . $row['CourseName'] . "  -  " . $row['CourseCode'] . "</option>";
                   }
               } else {
                   echo "<option value=''>No courses available</option>";
               }
            ?>
        </select>

        <!-- Lab Fields -->
        <label for="hasLab">Does the course have a lab?</label>
        <input type="checkbox" id="hasLab1" name="hasLab" onchange="toggleLabFieldsEdit()" <?php echo !empty($course['LabTime']) ? 'checked' : ''; ?> >

        <div id="labFields1" style="display: <?php echo !empty($course['LabTime']) ? 'block' : 'none'; ?>;">
            <label for="labTime">Lab Start Time</label>
            <input type="time" id="labTime1" name="labTime" value="<?php echo $course['LabTime']; ?>">

            <label for="labDuration">Lab Duration (hours)</label>
            <input type="number" id="labDuration1" name="labDuration" placeholder="Enter Lab Duration in hours" value="<?php echo $course['LabDuration']; ?>">

            <label for="labRoom">Lab Room </label>
            <input type="text" id="labRoom1" name="labRoom" placeholder="Enter Lab Room" value="<?php echo $course['LabRoom']; ?>">

            <label for="labday">Day</label>
            <select id="labday1" name="labday">
                <option value="">Select a day</option>
                <?php foreach ($days as $day) {
                    $selected = $day == $course['LabDay'] ? 'selected' : '';
                    echo "<option value='$day' $selected>$day</option>";
                } ?>
            </select>
        </div>

        <label for="secondLecture">Does this course have a second lecture?</label>
        <input type="checkbox" id="secondLecture1" name="secondLecture" onchange="toggleSecondLectureFieldsEdit()" <?php echo !empty($course['SecondLectureTime']) ? 'checked' : ''; ?>>

        <div id="secondLectureFields" style="display: <?php echo !empty($course['SecondLectureTime']) ? 'block' : 'none'; ?>;">
            <label for="secondLectureTime">Second Lecture Start Time</label>
            <input type="time" id="secondLectureTime" name="secondLectureTime" value="<?php echo $course['SecondLectureTime']; ?>">

            <label for="secondLectureDuration">Second Lecture Duration (hours)</label>
            <input type="number" id="secondLectureDuration" name="secondLectureDuration" placeholder="Enter Second Lecture Duration in hours" value="<?php echo $course['SecondLectureDuration']; ?>">

            <label for="secondLectureRoom">Second Lecture Room</label>
            <input type="text" id="secondLectureRoom" name="secondLectureRoom" placeholder="Enter Second Lecture Room" value="<?php echo $course['SecondLectureRoom']; ?>">

            <label for="secondlecday">Day</label>
            <select id="secondlecday" name="secondlecday">
                <option value="">Select a day</option>
                <?php foreach ($days as $day) {
                    $selected = $day == $course['SecondLectureDay'] ? 'selected' : '';
                    echo "<option value='$day' $selected>$day</option>";
                } ?>
            </select>
        </div>
                  <!-- Instructor Fields -->
                    <label for="hasInstructor">Does this course have an instructor?</label>
                    <input type="checkbox" id="hasInstructor1" name="hasInstructor" onchange="toggleInstructorFieldsEdit()" <?php echo !empty($course['InstructorName']) ? 'checked' : ''; ?>>

                        <div id="instructorFields1" style="display: <?php echo !empty($course['InstructorName']) ? 'block' : 'none'; ?>;">
                        <label for="instructorSelect">Select Instructor</label>
                        <select id="instructorSelect" name="instructorId">
                            
                              <?php if (!empty($course['instructorId']) && !empty($course['InstructorName'])) : ?>
                                <!-- If an instructor is selected, show it as the first option -->
                                <option value="<?php echo $course['instructorId']; ?>" selected>
                                    <?php echo htmlspecialchars($course['InstructorName']); ?>
                                </option>
                            <?php else : ?>
                                <!-- Default option if no instructor is selected -->
                                <option value="" selected>Select an Instructor</option>
                            <?php endif; ?>
                             <?php if (!empty($instructors)) : ?>
                                <?php foreach ($instructors as $instructor) : ?>
                                    <?php $selected = isset($course['InstructorId']) && $course['InstructorId'] == $instructor['ID'] ? 'selected' : ''; ?>
                                    <option value="<?php echo $instructor['ID']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($instructor['Username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">No instructors available</option>
                            <?php endif; ?>
                        </select>
                    </div>
        <button type="submit">Save Changes</button>
        <button type="button" onclick="window.location.href='?';">Cancel</button>
    </form>
    </div>
    <div id="userList" class="form-container">
    <h3>All Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Actions</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $result = Database::getInstance()->getConnection()->query("SELECT ID, Username, Email, Usertype FROM user");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['ID']}</td>";
                    echo "<td>{$row['Username']}</td>";
                    echo "<td>{$row['Email']}</td>";
                    echo "<td>{$row['Usertype']}</td>";
                    echo "<td><a href='?user_id={$row['ID']}' class='edit-button'>Edit</a></td>";
                    echo "<td>
                              <a href='delete_user.php?user_id={$row['ID']}' class='edit-button' >
                                 Delete
                                </a>
                            </td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
          <button type="button"  onclick="window.location.href='?';">Back</button>
</div>

<div id="editForm" class="form-container" style="display: <?php echo isset($_GET['user_id']) ? 'block' : 'none'; ?>" >
    <h3>Edit User</h3>
    <?php
        if (isset($_GET['user_id'])) {
            $id = $_GET['user_id'];
            $user_result = Database::getInstance()->getConnection()->query("SELECT Username, Password, Email, Usertype FROM user WHERE ID = $id");
            $user = $user_result->fetch_assoc();
        }
    ?>
    <form action="edit_user.php" method="POST" onsubmit="return validatePassword('password1');">
        <input type="hidden" name="user_id" value="<?php echo isset($id) ? $id : ''; ?>">

        <label for="username">New Username</label>
        <input type="text" id="username" name="username" value="<?php echo isset($user['Username']) ? $user['Username'] : ''; ?>">

        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password1" name="password" value="" required>
            <button type="button" id="togglePassword1" onclick="togglePasswordVisibility('password1', 'togglePassword1')">👁️</button>
        </div>
       

        <label for="email">New Email</label>
        <input type="email" id="email" name="email" value="<?php echo isset($user['Email']) ? $user['Email'] : ''; ?>">

        <label for="usertype">New Usertype</label>
        <input type="text" id="usertype" name="usertype" value="<?php echo isset($user['Usertype']) ? $user['Usertype'] : ''; ?>">

        <button type="submit">Edit User</button>
        <button type="button" onclick="window.location.href='admin.php';">Back</button>
    </form>
</div>


</div>

</body>
</html>
