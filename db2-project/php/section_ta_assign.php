<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['assign']) {
        goto section_ta_assign;
    }
    $course_id = $_POST['course_id'];
    $section_id = $_POST['section_id'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $student_id = $_POST['ta'];

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

    $query = "INSERT into TA(student_id, course_id, section_id, semester, year) values ('$student_id', '$course_id', '$section_id', '$semester', $year)";
    if(!($query_result = $mysqli->query($query))) {
        die("Query failed: $mysqli->error");
    }

    header("Location: ./dashboard.php");
    die("Unexpected error");
}
goto section_ta_assign;
?>
<?php

error:

section_ta_assign:
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
                <td><label for="ta">Available TA's:</label></td>
                <td>
                    <select name="ta">
                        <?php

                        $query = "SELECT * from PhD p, student s where s.student_id=p.student_id and p.student_id not in ( SELECT t.student_id from TA t where t.semester='$semester' and t.year=$year )";

                        if (!($available_result = $mysqli->query($query))) {
                            die("Query failed or could not find course to update: " . $mysqli->error);
                        }

                        while ($row = $available_result->fetch_array(MYSQLI_ASSOC)) {

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