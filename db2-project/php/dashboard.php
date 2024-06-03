<?php

$email = $_POST['email'];
$password = $_POST['password'];
$grade = $_POST['grade'];
$credit = $_POST['credits'];
$total_credits = $_POST['total_credits'];

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

if (!$email || !$password) {
    if ($_COOKIE['student_id']) {
        $query = 'select * from student where student_id=' . $_COOKIE['student_id'] . ';';
        goto student_dashboard;
    } else if ($_COOKIE['instructor_id']) {
        $query = 'select * from instructor where instructor_id=' . $_COOKIE['instructor_id'] . ';';
        goto instructor_dashboard;
    } else if ($_COOKIE['admin_id']) {
        $admin_email = $_COOKIE['admin_id'];
        goto admin_dashboard;
    }
    header("Location: ../index.html");
}


$query = "SELECT * from account where email='" . $email . "';";

if (!($account_result = $mysqli->query($query))) {
    die('Query failed 1: ' . $mysqli->error);
}

// Check if account exists with email then check if password is correct
if ($account_result->num_rows === 0) {
    // header("Location: ../index.html");
    die("Account does not exit");
}

// row is 0: email, 1: password, 3: type
$row = $account_result->fetch_array(MYSQLI_ASSOC);

// Verify Password
if ($row['password'] !== $password) {
    // header("Location: ../index.html");
    die("Password is incorrect");
}

if ($row['type'] == 'student') {
    $query = "SELECT * from student where email='" . $email . "';";
    goto student_dashboard;
} else if ($row['type'] == 'instructor') {
    $query = "SELECT * from instructor where email='" . $email . "';";
    goto instructor_dashboard;
} else if ($row['type'] == 'admin') {
    $admin_email = $_POST['email'];
    goto admin_dashboard;
}
die("Other accounts not implemented");
?>
<?php
student_dashboard:

$student_result = $mysqli->query($query);
if (!$student_result) {
    die('Query failed: ' . $mysqli->error);
}
// Student[student_id, name, email, dept_name]
$student = $student_result->fetch_array(MYSQLI_ASSOC);
setcookie('student_id', $student['student_id'], time() + (86400 * 30), "/");
setcookie('instructor_id', '', time() - 3600, "/");
setcookie('admin_id', '', time() - 3600, "/");

//Calculating GPA

$gradePoints = array(
    'A+' => 4.0,
    'A' => 4.0,
    'A-' => 3.7,
    'B+' => 3.3,
    'B' => 3.0,
    'B-' => 2.7,
    'C+' => 2.3,
    'C' => 2.0,
    'C-' => 1.7,
    'D+' => 1.3,
    'D' => 1.0,
    'D-' => 0.7,
    'F' => 0.0
);

$query = "SELECT t.grade as grade, c.credits as credits FROM take t, course c WHERE t.student_id=" . $student['student_id'] . " AND t.course_id = c.course_id;";

$result = $mysqli->query($query);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}

$total_credits = 0.0;
$total = 0.0;
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    if ($row['grade']) {
        $total_credits += $row['credits'];
        $grade = $gradePoints[$row['grade']] * $row['credits'];
        $total += $grade;
    }
}

if ($total_credits) {
    $gpa = $total / $total_credits;
} else {
    $gpa = "N/A";
}

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

    echo '<h2> Welcome ' . $student['email'] . '</h2>';

    echo "<p> Your Student ID: " . $student['student_id'] . '</p>';
    echo "<p> Your Name: " . $student['name'] . '</p>';

    echo "<p> GPA: " . $gpa . "</p>";
    echo "<p> Total Credits: " . $total_credits . "</p>";

    $query = "SELECT * from undergraduate where student_id='{$student['student_id']}'";
    if (!($query_result = $mysqli->query($query))) {
        die("Query failed . $mysqli->error");
    }
    $hold = "None";
    echo "<p> HOLD: ";
    if ($query_result->num_rows) {
        $row = $query_result->fetch_assoc();
        if ($row['hold'] === 'HOLD') {
            $hold = 'Advising';
        }
    }
    echo $hold;
    echo "</p>";

    ?>
    <form action="account_edit.php" method="get">
        <td><input type="submit" value="Edit Account" /></td>
    </form>
    <h3> Your Course History and Grades: </h3>
    <table>
        <tr>
            <th>Course ID</th>
            <th>Section ID</th>
            <th>Semester</th>
            <th>Year</th>
            <th>Grade</th>
        </tr>
        <?php
        $courses = $mysqli->query("SELECT * from take where student_id=" . $student['student_id'] . ";");
        if (!$courses) {
            die("Courses query failed");
        }

        while ($row = $courses->fetch_array(MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['course_id'] . '</td>';
            echo '<td>' . $row['section_id'] . '</td>';
            echo '<td>' . $row['semester'] . '</td>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>' . $row['grade'] . '</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <form action="student_register.php" method="get">
        <td><input type="submit" value="Register" /></td>
    </form>
</body>

</html>
<?php
return;

instructor_dashboard:

$instructor_result = $mysqli->query($query);
if (!$instructor_result) {
    die('Query failed: ' . $mysqli->error);
}
// Student[student_id, name, email, dept_name]
$instructor = $instructor_result->fetch_array(MYSQLI_ASSOC);

setcookie('instructor_id', $instructor['instructor_id'], time() + (86400 * 30), "/");
setcookie('student_id', '', time() - 3600, "/");
setcookie('admin_id', '', time() - 3600, "/");

$instructor_id = $instructor['instructor_id'];
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

    echo '<h2> Welcome ' . $instructor['email'] . '</h2>';

    echo '<p> Your Title: ' . $instructor['title'] . '</p>';
    echo '<p> Your Department: ' . $instructor['dept_name'] . '</p>';
    echo "<p> Your Instructor ID: " . $instructor['instructor_id'] . '</p>';
    echo "<p> Your Name: " . $instructor['instructor_name'] . '</p>';

    ?>
    <h3> Your Course Taught: </h3>
    <table>
        <tr>
            <th>Course ID</th>
            <th>Section ID</th>
            <th>Semester</th>
            <th>Year</th>
            <th>Students</th>
        </tr>
        <?php
        $courses = $mysqli->query("SELECT * from section where instructor_id={$instructor['instructor_id']} ORDER BY year, semester;");
        if (!$courses) {
            die("Courses query failed");
        }

        while ($row = $courses->fetch_array(MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['course_id'] . '</td>';
            echo '<td>' . $row['section_id'] . '</td>';
            echo '<td>' . $row['semester'] . '</td>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>';
            echo '<form action="instructor_view_students.php" method="post">';
            echo        '<input type="hidden" name="course_id" value="' . $row['course_id'] . '"/>';
            echo        '<input type="hidden" name="section_id" value="' . $row['section_id'] . '"/>';
            echo        '<input type="hidden" name="semester" value="' . $row['semester'] . '"/>';
            echo        '<input type="hidden" name="year" value="' . $row['year'] . '"/>';
            echo        '<input type="submit" value="View Students Report" />
                    </form></td>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <h3>
        PhD Advising
    </h3>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Qualifier</th>
            <th>Proposal Defense Date</th>
            <th>Dissertation Defense Date</th>
            <th>Courses</th>
            <th>Update</th>
        </tr>

        <?php
        $query = "SELECT * FROM advise a, student s, PhD p where a.instructor_id='$instructor_id' and  a.student_id=s.student_id and a.student_id=p.student_id";

        if (!($phd_students = $mysqli->query($query))) {
            die("Query failed: $mysqli->error");
        }

        while ($row = $phd_students->fetch_assoc()) {
            echo '<tr>';
            echo "<td>{$row['student_id']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['qualifier']}</td>";
            echo "<td>{$row['proposal_defense_date']}</td>";
            echo "<td>{$row['dissertation_defense_date']}</td>";
            echo '<td>';
            echo '<form action="advisee_courses.php" method="post">';
            echo        '<button name="courses" value="' . $row['student_id'] . '">View</button>';
            echo '</form></td>';
            echo '</td>';
            echo '<td>';
            echo '<form action="phd_edit.php" method="post">';
            echo        '<button name="update" value="' . $row['student_id'] . '">Update</button>';
            echo '</form></td>';
            echo '</td>';
            echo '</tr>';
        }

        ?>
    </table>

    <h3>
        Undergraduate Advising
    </h3>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Hold Status</th>
            <th>Toggle Hold</th>
        </tr>

        <?php

        $query = "SELECT * FROM advise_undergraduate a, student s, undergraduate u where a.instructor_id='$instructor_id' and  a.student_id=s.student_id and a.student_id=u.student_id";

        if (!($undergrad_result = $mysqli->query($query))) {
            die("Query failed: $mysqli->error");
        }

        while ($row = $undergrad_result->fetch_assoc()) {
            echo '<tr>';
            echo "<td>{$row['student_id']}</td>";
            echo "<td>{$row['name']}</td>";

            echo "<td>";
            echo $row['hold'] ? 'HOLD' : 'NO HOLD';
            echo "</td>";

            echo '<td>';
            echo '<form action="advisor_toggle_hold.php" method="post">';
            echo        '<button name="student_id" value="' . $row['student_id'] . '">Toggle Hold</button>';
            echo '</form></td>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <form action="./advisor_panel.php" method="get">
        <input type="submit" value="Appoint Advisor" />
    </form>
</body>

</html>

<?php
return;

admin_dashboard:

setcookie('admin_id', $admin_email, time() + (86400 * 30), "/");
setcookie('instructor_id', '', time() - 3600, "/");
setcookie('student_id', '', time() - 3600, "/");
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

    <h2>
        Courses:
    </h2>
    <table>
        <tr>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Credits</th>
            <th>Create Section</th>
        </tr>
        <?php
        $courses = $mysqli->query("SELECT * from course;");
        if (!$courses) {
            die("Courses query failed");
        }

        while ($row = $courses->fetch_array(MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['course_id'] . '</td>';
            echo '<td>' . $row['course_name'] . '</td>';
            echo '<td>' . $row['credits'] . '</td>';
            echo '<td><form action="./section_create.php" method="post"> <input type="submit" name="course_id" value="' . $row['course_id'] . '" /></form></td>';
            echo '</tr>';
        }
        ?>
    </table>
    <br />
    <?php
    function createRows(mysqli $mysqli, string $query, $status)
    {
        $sections = $mysqli->query($query);
        if (!$sections) {
            die("Courses query failed Sections: " . $mysqli->error);
        }
        while ($row = $sections->fetch_array(MYSQLI_ASSOC)) {
            $course_id = $row['course_id'];
            $section_id = $row['section_id'];
            $semester = $row['semester'];
            $year = $row['year'];
            $instructor_id = $row['instructor_id'];
            $instructor_name = 'Not Assigned';
            $time_slot_id = $row['time_slot_id'];
            $day = 'Not Assigned';
            $start_time = 'Not Assigned';
            $end_time = 'Not Assigned';
            $classroom_id = $row['classroom_id'];
            $classroom_building = 'Not Assigned';
            $classroom_number = 'Not Assigned';

            if ($instructor_id) {
                $query = "SELECT * from instructor where instructor_id='$instructor_id'";
                if (!($query_result = $mysqli->query($query))) {
                    die("Query failed: ");
                }
                $instructor = $query_result->fetch_assoc();
                $instructor_name = $instructor['instructor_name'];
            } else {
                $instructor_id = "<form action=\"./section_assign.php\" method=\"post\"><button name=\"update\" value=\"{$row['course_id']};{$row['section_id']};{$row['semester']};{$row['year']}\">Update</button></form>";
            }

            if ($time_slot_id) {
                $query = "SELECT * from time_slot where time_slot_id='$time_slot_id'";
                if (!($query_result = $mysqli->query($query))) {
                    die("Query failed: ");
                }
                $time_slot = $query_result->fetch_assoc();
                $day = $time_slot['day'];
                $start_time = $time_slot['start_time'];
                $end_time = $time_slot['end_time'];
            } else {
                $time_slot_id = 'Not Assigned';
            }

            if ($classroom_id) {
                $query = "SELECT * from classroom where classroom_id='$classroom_id'";
                if (!($query_result = $mysqli->query($query))) {
                    die("Query failed: ");
                }
                $classroom = $query_result->fetch_assoc();
                $classroom_building = $classroom['building'];
                $classroom_number = $classroom['room_number'];
            } else {
                $classroom_id = 'Assign';
            }
            $classroom_id = "<form action=\"./assign_classroom.php\" method=\"post\"><button name=\"update\" value=\"{$row['course_id']};{$row['section_id']};{$row['semester']};{$row['year']}\">$classroom_id</button></form>";


            $query = "SELECT * from TA t, student s where t.student_id = s.student_id and course_id='{$row['course_id']}' and section_id='{$row['section_id']}' and semester='{$row['semester']}' and year={$row['year']}";

            if (!($query_result = $mysqli->query($query))) {
                die("Query failed Sections: " . $mysqli->error);
            }

            $ta_button = '';
            if ($query_result->num_rows) {
                $row = $query_result->fetch_assoc();
                $ta_button = "{$row['student_id']} - {$row['name']}";
            } else if ($status === 'Past') {
                $ta_button = '-';
            } else {
                $query = "SELECT * from take WHERE course_id='{$row['course_id']}' and section_id='{$row['section_id']}' and semester='{$row['semester']}' and year={$row['year']}";
                if (!($query_result = $mysqli->query($query))) {
                    die("Query failed Sections: " . $mysqli->error);
                }
                if ($query_result->num_rows > 10) {
                    $ta_button = "<form action=\"./section_ta_assign.php\" method=\"post\"><button name=\"assign\" value=\"{$row['course_id']};{$row['section_id']};{$row['semester']};{$row['year']}\">Assign</button></form>";
                } else {
                    $ta_button = "< 10";
                }
            }

            $query = "SELECT * from undergraduateGrader u, student s where u.student_id = s.student_id and course_id='{$row['course_id']}' and section_id='{$row['section_id']}' and semester='{$row['semester']}' and year={$row['year']} UNION SELECT * from masterGrader u, student s where u.student_id = s.student_id and course_id='{$row['course_id']}' and section_id='{$row['section_id']}' and semester='{$row['semester']}' and year={$row['year']}";

            if (!($query_result = $mysqli->query($query))) {
                die("Query failed Sections: " . $mysqli->error);
            }

            $grader_button = '';
            if ($query_result->num_rows) {
                $row = $query_result->fetch_assoc();
                $grader_button = "{$row['student_id']} - {$row['name']}";
            } else if ($status === 'Past') {
                $grader_button = '-';
            } else {
                $query = "SELECT * from take WHERE course_id='{$row['course_id']}' and section_id='{$row['section_id']}' and semester='{$row['semester']}' and year={$row['year']}";
                if (!($query_result = $mysqli->query($query))) {
                    die("Query failed Sections: " . $mysqli->error);
                }
                if ($query_result->num_rows > 10) {
                    $grader_button = "> 10";
                } else if ($query_result->num_rows >= 5) {
                    $grader_button = "<form action=\"./section_grader_assign.php\" method=\"post\"><button name=\"assign\" value=\"{$row['course_id']};{$row['section_id']};{$row['semester']};{$row['year']}\">Assign</button></form>";
                } else {
                    $grader_button = "< 5";
                }
            }
            echo '<tr>';
            echo "<td>$course_id</td>";
            echo "<td>$section_id</td>";
            echo '<td>' . $instructor_id . '</td>';
            echo '<td>' . $instructor_name . '</td>';
            echo '<td>' . $time_slot_id . '</td>';
            echo '<td>' . $day . '</td>';
            echo '<td>' . $start_time . '</td>';
            echo '<td>' . $end_time . '</td>';
            echo "<td>$classroom_id</td>";
            echo "<td>$classroom_building</td>";
            echo "<td>$classroom_number</td>";
            echo "<td>$ta_button</td>";
            echo "<td>$grader_button</td>";
            echo '</tr>';
        }
    }

    $query = "SELECT * from semester order by year ASC, semester DESC";
    if (!($semester_result = $mysqli->query($query))) {
        die("Query Failed: $mysqli->error");
    }
    while ($semester_row = $semester_result->fetch_array(MYSQLI_ASSOC)) {
        $semester = $semester_row['semester'];
        $year = $semester_row['year'];
        $status = $semester_row['status'];


        echo "<h3> $year - $semester </h3>";
    ?>
        <table>
            <tr>
                <th>Course ID</th>
                <th>Section ID</th>
                <th>Instructor ID </th>
                <th>Instructor Name</th>
                <th>Time Slot ID </th>
                <th>Days </th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Classroom ID</th>
                <th>Building</th>
                <th>Room Number</th>
                <th>TA</th>
                <th>Grader</th>
            </tr>
            <?php

            $query = "SELECT * from section s where s.year=$year and s.semester='$semester'";
            createRows($mysqli, $query, $status);

            ?>
        </table>
    <?php
    }
    ?>
    <form action="./section_create.php" method="get">
        <input type="submit" value="Create Section" />
    </form>

    <h2>
        PhD
    </h2>
    <form action="./advisor_panel.php" method="get">
        <input type="submit" value="Appoint Advisor" />
    </form>
</body>


</html>