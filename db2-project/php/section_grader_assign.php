<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['assign']) {
        goto section_grader_assign;
    }
    $course_id = $_POST['course_id'];
    $section_id = $_POST['section_id'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $student_id = $_POST['student_id'];

    if (!$course_id || !$section_id || !$semester || !$year || !$student_id) {
        die("No info");
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

    $query = "SELECT * from master where student_id='$student_id'";
    if (!($query_result = $mysqli->query($query))) {
        die("Query failed: $mysqli->error");
    }

    if ($query_result->num_rows > 0) {
        $query = "INSERT into masterGrader(student_id, course_id, section_id, semester, year) values ('$student_id', '$course_id', '$section_id', '$semester', $year)";
    } else {
        $query = "INSERT into undergraduateGrader(student_id, course_id, section_id, semester, year) values ('$student_id', '$course_id', '$section_id', '$semester', $year)";
    }
    if (!($query_result = $mysqli->query($query))) {
        die("Query failed: $mysqli->error");
    }



    header("Location: ./dashboard.php");
    die("Unexpected error");
}
goto section_grader_assign;
?>
<?php

error:

section_grader_assign:
// Check if user is logged into an admin account
if (!isset($_COOKIE['admin_id']) || $_COOKIE['admin_id'] === '') {
    die("Please login to admin account");
    // goto error_login;
}

$info = $_POST['assign'];

$arr = explode(';', $info);

$course_id = $arr[0];
$section_id = $arr[1];
$semester = $arr[2];
$year = $arr[3];

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

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
                <td><label for="course_id">Course ID:</label></td>
                <td>
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
                    <p><?php echo $course_id; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="section_id">Section ID:</label></td>
                <td>
                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
                    <p><?php echo $section_id; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="semester">Semester:</label></td>
                <td>
                    <input type="hidden" name="semester" value="<?php echo $semester; ?>" />
                    <p><?php echo $semester; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="year">Year:</label></td>
                <td>
                    <input type="hidden" name="year" value="<?php echo $year; ?>" />
                    <p><?php echo $year; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="student_id">Available Graders:</label></td>
                <td>
                    <select name="student_id">
                        <?php
                        // $query = "SELECT * from take t, student s where (t.grade='A+' or t.grade='A' or t.grade='A-') and t.student_id=s.student_id and (t.student_id in (select student_id from masters) or t.student_id in (select student_id from undergraduate))";

                        $query = "SELECT * from take t, student s where t.student_id=s.student_id and (t.grade='A+' or t.grade='A' or t.grade='A-') and t.student_id in (SELECT student_id from undergraduate) and t.student_id not in (SELECT student_id from undergraduateGrader where semester='$semester' and year=$year)";
                        if (!($available_result = $mysqli->query($query))) {
                            die("Query failed or could not find course to update: " . $mysqli->error);
                        }

                        while ($row = $available_result->fetch_array(MYSQLI_ASSOC)) {

                            echo "<option value=\"" . $row['student_id'] . "\">" . $row['student_id'] . '-' . $row['name'] . "</option>";
                        }


                        $query = "SELECT * from take t, student s where t.student_id=s.student_id and (t.grade='A+' or t.grade='A' or t.grade='A-') and t.student_id in (SELECT student_id from master) and t.student_id not in (SELECT student_id from masterGrader where semester='$semester' and year=$year)";
                        if (!($available_result = $mysqli->query($query))) {
                            die("Query failed or could not find course to update: " . $mysqli->error);
                        }

                        while ($row = $available_result->fetch_assoc()) {

                            echo "<option value=\"" . $row['student_id'] . "\">" . $row['student_id'] . '-' . $row['name'] . "</option>";
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