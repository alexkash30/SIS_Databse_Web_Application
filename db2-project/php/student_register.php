<?php

// Check if user is logged into a student account
if (!isset($_COOKIE['student_id']) || $_COOKIE['student_id'] === '') {
    die("Please login to admin account");
    // goto error_login;
}

$student_id = $_COOKIE['student_id'];

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_course_id = $_POST['course_id'];
    $selected_section_id = $_POST['section_id'];
    $selected_year = $_POST['year'];
    $selected_semester = $_POST['semester'];

    //Check available space in the selected section
    $query_enrollment = "SELECT COUNT(*) as num_students FROM take WHERE course_id='$selected_course_id' AND section_id='$selected_section_id' AND semester='$selected_semester' AND year=$selected_year";
    if (!($enrollment_result = $mysqli->query($query_enrollment))) {
        die("Query failed $mysqli->error");
    }
    $enrollment_data = $enrollment_result->fetch_assoc();
    $num_students = $enrollment_data['num_students'];

    //check if there is available space
    if ($num_students >= 15) {
        $error_msg = 'Class is full';
        goto student_register;
    }

    // Check prereq
    $query = "SELECT * from take where student_id='$student_id'";
    if (!($classes_result = $mysqli->query($query))) {
        die("Query failed $mysqli->error");
    }

    $classes_taken = $classes_result->fetch_all(MYSQLI_ASSOC);

    $query = "SELECT * from prereq where course_id='$selected_course_id'";
    if (!($prereq_result = $mysqli->query($query))) {
        die("Query failed $mysqli->error");
    }
    $prereq_classes = $prereq_result->fetch_all(MYSQLI_ASSOC);

    function checkClasses($prereq_classes, $classes_taken)
    {
        foreach ($prereq_classes as $prereq) {
            $satisfied = false;
            foreach ($classes_taken as $class) {
                if ($prereq['prereq_id'] == $class['course_id'] && $class['grade']) {
                    $satisfied = true;
                    break;
                }
            }
            if (!$satisfied) {
                return false;
            }
        }
        return true;
    }

    $satisfied = checkClasses($prereq_classes, $classes_taken);

    if (!$satisfied) {
        $error_msg = 'Prereqs not satisfied';
        goto student_register;
    }

    $query = "SELECT * from undergraduate where student_id='$student_id'";
    if (!($query_result = $mysqli->query($query))) {
        die("Query failed . $mysqli->error");
    }

    if ($query_result->num_rows) {
        $row = $query_result->fetch_assoc();
        if ($row['hold'] === 'HOLD') {
            $error_msg = 'You have an advising hold. Meet with advisor.';
            goto student_register;
        }
    }

    $query = "INSERT into take (student_id, course_id, section_id, semester, year) values ('$student_id', '$selected_course_id', '$selected_section_id', '$selected_semester', $selected_year)";

    if (!($query_result = $mysqli->query($query))) {
        die("Query failed . $mysqli->error");
    }

    header("Location: dashboard.php");

    die("Unknown error");
}
?>

<?php

student_register:

$query = "SELECT * FROM student where student_id='$student_id';";

if (!($student_result = $mysqli->query($query)) || !($student_result->num_rows)) {
    die("Account not found");
}

$student = $student_result->fetch_assoc();
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>
<style>
    table,
    th,
    td {
        border: 1px solid black;
    }
</style>

<body>
    <h1>University Management System</h1>
    <?php

    echo '<h2> User: ' . $student['email'] . '</h2>';
    ?>
    <h3> Courses Available: </h3>
    <table>
        <tr>
            <th>Course Name</th>
            <th>Section Name</th>
            <th>Instructor Name</th>
            <th>Days</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Students</th>
            <th>Register</th>
        </tr>
        <?php

        $query = "SELECT * FROM semester where status='Current'";

        if (!($current_result = $mysqli->query($query)) || !$current_result->num_rows) {
            die("Query failed: $mysqli->error");
        }


        $current_semester = $current_result->fetch_assoc();

        function createRows($mysqli, $query, $current_semester)
        {
            $courses = $mysqli->query($query . " and s.semester='{$current_semester['semester']}' and s.year={$current_semester['year']}");
            if (!$courses) {
                die("Courses query failed Sections: " . $mysqli->error);
            }



            while ($row = $courses->fetch_array(MYSQLI_ASSOC)) {

                $query = "SELECT * from take where course_id='{$row['course_id']}' and section_id='{$row['section_id']}' and semester='{$row['semester']}' and year='{$row['year']}'";

                if (!($query_result = $mysqli->query($query))) {
                    die("Query Failed");
                }

                echo '<tr>';
                echo '<td>' . $row['course_name'] . '</td>';
                echo '<td>' . $row['section_id'] . '</td>';
                // echo '<td>' . $row['semester'] . '</td>';
                // echo '<td>' . $row['year'] . '</td>';
                // echo '<td>' . ($row['instructor_id'] ? $row['instructor_id'] : "<form action=\"./section_assign.php\" method=\"post\"><button name=\"update\" value=\"{$row['course_id']};{$row['section_id']};{$row['semester']};{$row['year']}\">Update</button></form>") . '</td>';
                echo '<td>' . ($row['instructor_name'] ? $row['instructor_name'] : 'Not Assigned') . '</td>';
                // echo '<td>' . ($row['time_slot_id'] ? $row['time_slot_id'] : 'Not Assigned') . '</td>';
                echo '<td>' . ($row['day'] ? $row['day'] : 'Not Assigned') . '</td>';
                echo '<td>' . ($row['start_time'] ? $row['start_time'] : 'Not Assigned') . '</td>';
                echo '<td>' . ($row['end_time'] ? $row['end_time'] : 'Not Assigned') . '</td>';
                echo "<td>{$query_result->num_rows}/15</td>";
                echo '<td>
                        <form action="" method="post">';
                echo        '<input type="hidden" name="course_id" value="' . $row['course_id'] . '"/>';
                echo        '<input type="hidden" name="section_id" value="' . $row['section_id'] . '"/>';
                echo        '<input type="hidden" name="semester" value="' . $row['semester'] . '"/>';
                echo        '<input type="hidden" name="year" value="' . $row['year'] . '"/>';
                echo        '<input type="submit" value="Register" />
                        </form></td>';
                echo '</tr>';
            }
        }

        $query = "SELECT * from course c, section s, instructor i, time_slot t where c.course_id=s.course_id and s.instructor_id=i.instructor_id and s.time_slot_id=t.time_slot_id";
        createRows($mysqli, $query, $current_semester);

        $query = "SELECT * from course c, section s, time_slot t where c.course_id=s.course_id and s.instructor_id is null and s.time_slot_id=t.time_slot_id";
        createRows($mysqli, $query, $current_semester);

        $query = "SELECT * from course c, section s, instructor i where c.course_id=s.course_id and s.instructor_id=i.instructor_id and s.time_slot_id is null";
        createRows($mysqli, $query, $current_semester);

        $query = "SELECT * from course c, section s where c.course_id=s.course_id and instructor_id is null and time_slot_id is null";
        createRows($mysqli, $query, $current_semester);
        ?>
    </table>
    <?php
    if ($error_msg) {
        echo "<p> $error_msg </p>";
    }
    ?>
</body>

</html>