<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['update']) {
        goto section_update;
    }
    $course_id = $_POST['course_id'];
    $section_id = $_POST['section_id'];
    $semester = $_POST['semester'];
    $time_slot_id = $_POST['time_slot'];
    $instructor_id = $_POST['instructor'];
    $year = $_POST['year'];

    if (!$course_id || !$section_id || !$semester || !$time_slot_id || !$instructor_id || !$year) {
        $error_msg = "Fields missing or empty";
        goto error;
    }

    $mysqli = new mysqli('localhost', 'root', '');

    if ($mysqli->connect_errno) {
        die('Could not connect: ' . $mysqli->connect_error);
    }

    if (!$mysqli->select_db('DB2')) {
        die('Could not select database');
    }

    // Get time slot details
    $query = "SELECT * from time_slot where time_slot_id='" . $time_slot_id . "';";

    if (!($time_result = $mysqli->query($query)) || $time_result->num_rows == 0) {
        die("Query failed or time slot not found: " . $mysqli->error);
    }

    $time_slot = $time_result->fetch_array(MYSQLI_ASSOC);

    // Get instructor details
    $query = "SELECT * from instructor where instructor_id=" . $instructor_id;

    if (!($instructor_result = $mysqli->query($query)) || $instructor_result->num_rows == 0) {
        die("Query failed or instructor not found: " . $mysqli->error);
    }

    $instructor = $instructor_result->fetch_array(MYSQLI_ASSOC);

    // // Verify section id does not exist
    // $query = "SELECT * from section where course_id='" . $course_id . "'and section_id='" . $section_id . "' and semester='" . $semester . "' and year=" . $year . ";";

    // if (!($section_result = $mysqli->query($query))) {
    //     die("Query failed: " . $mysqli->error);
    // }
    // if ($section_result->num_rows > 0) {
    //     $error_msg = "Section ID already exists in this semester";
    //     goto error;
    // }

    // Check how many sections are running at time slot
    $query = "SELECT * from section where course_id='" . $course_id . "' and semester='" . $semester . "' and year=" . $year . " and time_slot_id='" . $time_slot_id . "'";

    if (!($section_result = $mysqli->query($query))) {
        die("Query failed: " . $mysqli->error);
    }

    if ($section_result->num_rows >= 2) {
        $error_msg = "Attempting to create too many sections at this time slot";
        goto error;
    }

    // Check how many classes the instructor is teaching as well as verify time slots are back to back

    $query = "SELECT * from section where semester='" . $semester . "' and year=" . $year . " and instructor_id='" . $instructor_id . "'";

    if (!($section_result = $mysqli->query($query))) {
        die("Query failed: " . $mysqli->error);
    }

    if ($section_result->num_rows >= 2) {
        $error_msg = "Attempting to assign too many sections to one teacher";
        goto error;
    } else if ($section_result->num_rows === 1) {
        $instructor_section = $section_result->fetch_assoc();

        $query = "SELECT * from time_slot where time_slot_id='" . $instructor_section['time_slot_id'] . "'";

        if (!($time_result = $mysqli->query($query)) || !$time_result->num_rows) {
            die("Query failed or couldn't look up time_slot: " . $mysqli->error);
        }

        $section_time_slot = $time_result->fetch_assoc();

        if ($time_slot['day'] !== $section_time_slot['day']) {
            $error_msg = "Time slots are not consecutive";
            goto error;
        }
        $time1 = $time_slot['start_time'];
        $split = explode(":", $time1);
        $h1 = (int)$split[0];
        $m1 = (int)$split[1];
        $time2 = $section_time_slot['start_time'];
        $split = explode(":", $time2);
        $h2 = (int)$split[0];
        $m2 = (int)$split[1];
        if ($time_slot['day'] === "MoWeFr") {
            if (!($m1 === $m2 && ($h1 === $h2 + 1 || $h1 === $h2 - 1))) {
                $error_msg = "Time slots are not consecutive";
                goto error;
            }
        } else {
            // day === TuTh
            $h1 += $m1 / 60;
            $h2 += $m2 / 60;
            if (!($h1 + 3 / 2 == $h2 || $h2 + 3 / 2 == $h1)) {
                $error_msg = "Time slots are not consecutive";
                goto error;
            }
        }
    }

    $query = "UPDATE section SET instructor_id='$instructor_id', time_slot_id='$time_slot_id' WHERE course_id='$course_id' and section_id='$section_id' and semester='$semester' and year=$year";
    if (!($update_result = $mysqli->query($query))) {
        die("Query failed: " . $mysqli->error);
    }

    header("Location: ./dashboard.php");
    die("Unexpected error");
}
goto section_update;
?>
<?php


section_update:
// Check if user is logged into an admin account
if (!isset($_COOKIE['admin_id']) || $_COOKIE['admin_id'] === '') {
    die("Please login to admin account");
    // goto error_login;
}

$info = $_POST['update'];

$arr = explode(';', $info);

$course_id = $arr[0];
$section_id = $arr[1];
$semester = $arr[2];
$year = $arr[3];

error:

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

$query = "SELECT * from section s, course c where s.course_id='$course_id' and s.section_id='$section_id' and s.semester='$semester' and s.year='$year' and s.course_id=c.course_id";

if (!($section_result = $mysqli->query($query))) {
    die("Query failed or could not find course to update: " . $mysqli->error);
}

$section = $section_result->fetch_assoc();

?>
<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>

<body>
    <h1>University Management System</h1>
    <form method="post">
        <table>
            <tr>
                <td><label for="course_id">Course Name:</label></td>
                <td>
                    <input type="hidden" name="course_id" value="<?php echo $section['course_id'] ?>" />
                    <p><?php echo $section['course_name']; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="section_id">Section ID:</label></td>
                <td>
                    <input type="hidden" name="section_id" value="<?php echo $section['section_id'] ?>" />
                    <p><?php echo $section['section_id']; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="semester">Semester:</label></td>
                <td>
                    <input type="hidden" name="semester" value="<?php echo $section['semester'] ?>" />
                    <p><?php echo $section['semester']; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="year">Year:</label></td>
                <td>
                    <input type="hidden" name="year" value="<?php echo $section['year'] ?>" />
                    <p><?php echo $section['year']; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="time_slot">Time Slot:</label></td>
                <td>
                    <select name="time_slot">
                        <?php
                        $query = "SELECT * from time_slot;";

                        if (!($time_result = $mysqli->query($query))) {
                            die('Query failed: ' . $mysqli->error);
                        }

                        while ($row = $time_result->fetch_array(MYSQLI_ASSOC)) {
                            echo "<option value=\"" . $row['time_slot_id'] . "\">" . $row['day'] . " " . $row['start_time'] . "-" . $row['end_time'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="instructor">Instructor:</label></td>
                <td>
                    <select name="instructor">
                        <?php
                        $query = "SELECT * from instructor;";

                        if (!($instructor_result = $mysqli->query($query))) {
                            die('Query failed: ' . $mysqli->error);
                        }

                        while ($row = $instructor_result->fetch_array(MYSQLI_ASSOC)) {
                            echo "<option value=\"" . $row['instructor_id'] . "\">" . $row['instructor_name'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
            if ($error_msg) {
                echo '<tr><td colspan="3">' . $error_msg . '</td></tr>';
            }
            ?>
            <tr>
                <td><input type="submit" name="Update" /></td>
            </tr>
        </table>
    </form>
</body>

</html>