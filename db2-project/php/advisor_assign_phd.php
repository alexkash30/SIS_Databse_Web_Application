<?php
$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

if ((!isset($_COOKIE['admin_id']) || $_COOKIE['admin_id'] === '') && (!isset($_COOKIE['instructor_id']) || $_COOKIE['instructor_id'] === '')) {
    die("Please login to admin or instructor account");
    // goto error_login;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['assign']) {
        goto advisor_assign;
    }
    $student_id = $_POST['student_id'];
    $instructor_id = $_POST['instructor_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (!$student_id || !$instructor_id || !$start_date) {
        $error_msg = "Fields missing or empty";
        goto error;
    }

    $start_date = date('Y-m-d', strtotime($start_date));
    if ($end_date) {
        $end_date = date('Y-m-d', strtotime($end_date));
        $query = "INSERT into advise(instructor_id, student_id, start_date, end_date) values ('$instructor_id', '$student_id', '$start_date', '$end_date')";
    } else {
        $query = "INSERT into advise(instructor_id, student_id, start_date) values ('$instructor_id', '$student_id', '$start_date')";
    }

    if (!($query_result = $mysqli->query($query))) {
        die("Query failed: $mysqli->error");
    }

    header("Location: ./advisor_panel.php");
    die("Unexpected error");
}
goto advisor_assign;
?>
<?php

error:

advisor_assign:

$student_id = $_POST['assign'];
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>

<body>
    <h1>University Management System</h1>

    <h3>Assign Advisor</h3>
    <form method="post">
        <table>
            <tr>
                <td><label for="student_id">Student ID:</label></td>
                <td>
                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>" />
                    <p><?php echo $student_id; ?></p>
                </td>
            </tr>
            <tr>
                <td><label for="instructor_id">Available Advisors:</label></td>
                <td>
                    <select name="instructor_id">
                        <?php
                        $query = "SELECT * from instructor s where s.instructor_id not in (SELECT a.instructor_id from advise a where a.student_id='$student_id')";
                        if (!($available_result = $mysqli->query($query))) {
                            die("Query failed or could not find course to update: " . $mysqli->error);
                        }

                        while ($row = $available_result->fetch_array(MYSQLI_ASSOC)) {

                            echo "<option value=\"" . $row['instructor_id'] . "\">" . $row['instructor_id'] . '-' . $row['instructor_name'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="start_date">Start Date:</label></td>
                <td>
                    <input type="date" name="start_date">
                </td>
            </tr>
            <tr>
                <td><label for="end_date">End Date:</label></td>
                <td>
                    <input type="date" name="end_date">
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