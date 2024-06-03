<?php
// Check if user is logged into an admin account
if (!isset($_COOKIE['admin_id']) || $_COOKIE['admin_id'] === '') {
    die("Please login to admin account");
    // goto error_login;
}

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['update']) {
        goto section_classroom_assign;
    }

    $course_id = $_POST['course_id'];
    $section_id = $_POST['section_id'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $classroom_id = $_POST['classroom_id'];

    if (!$course_id || !$section_id || !$semester || !$year || !$classroom_id) {
        $error_msg = "Missing classroom";
        goto error;
    }

    $query = "SELECT * FROM section where course_id='$course_id' and section_id='$section_id' and semester='$semester' and year=$year";
    if (!($query_result = $mysqli->query($query))) {
        die("Query failed: " . $mysqli->error);
    }
    $section = $query_result->fetch_assoc();
    $time_slot_id = $section['time_slot_id'];
    if (!$time_slot_id) {
        $error_msg = 'Please assign time slot first';
        goto error;
    }
    if ($section['classroom_id']) {
        $query = "UPDATE section set classroom_id=NULL WHERE course_id='$course_id' and section_id='$section_id' and semester='$semester' and year=$year";
        if (!$mysqli->query($query)) {
            die('Query failed: ' . $mysqli->error);
        }
    }

    $query = "SELECT * FROM section where classroom_id = '$classroom_id' and time_slot_id='$time_slot_id' and semester='$semester' and year=$year;";
    if (!($potential_collision = $mysqli->query($query))) {
        die('Query failed: ' . $mysqli->error);
    }
    if ($potential_collision->num_rows) {
        $error_msg = 'This time and location is already in use';
        goto error;
    }

    $query = "UPDATE section set classroom_id= '$classroom_id' WHERE course_id='$course_id' and section_id='$section_id' and semester='$semester' and year=$year";
    if (!$mysqli->query($query)) {
        die('Query failed: ' . $mysqli->error);
    }

    header("Location: ./dashboard.php");
    die("Unexpected error");
}
goto section_classroom_assign;

?>

<?php


section_classroom_assign:

$info = $_POST['update'];

$arr = explode(';', $info);

$course_id = $arr[0];
$section_id = $arr[1];
$semester = $arr[2];
$year = $arr[3];

error:

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
                <td><label for="classroom_id">Available Classrooms's:</label></td>
                <td>
                    <select name="classroom_id">
                        <?php
                        $query = "SELECT * from classroom;";

                        if (!($classroom_result = $mysqli->query($query))) {
                            die('Query failed: ' . $mysqli->error);
                        }

                        while ($row = $classroom_result->fetch_assoc()) {
                            echo "<option value=\"" . $row['classroom_id'] . "\">" . $row['building'] . " " . $row['room_number'] . " Count: " . $row['capacity'] . "</option>";
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