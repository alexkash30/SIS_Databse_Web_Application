<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $credits = $_POST['credits'];

    if (!$course_id || !$course_name || !$credits) {
        $error_msg = "Fields missing or empty";
        goto create_course;
    }
    $error_msg = '';

    $myConnection = new mysqli('localhost', 'root', '');

    if ($myConnection->connect_errno) {
        die('Could not connect: ' . $myConnection->connect_error);
    }

    if (!$myConnection->select_db('DB2')) {
        die('Could not select database: ' . $myConnection->error);
    }

    // Check if course_id is taken
    $query = 'SELECT course_id in course where course_id=' . $course_id . ';';

    if (!($courses_result = $myConnection->query($query))) {
        die("Could not query course: " . $myConnection->error);
    }

    if ($courses_result->num_rows > 0) {
        $error_msg = 'Course ID Taken!';
        goto create_course;
    }

    $query = "INSERT INTO course (course_id, course_name, credits) values ('" . $course_id . "', '" . $course_name . "', " . $credits . ");";

    if (!($course_result = $myConnection->query($query))) {
        die("Could not query course: " . $myConnection->error);
    }

    header("Location: ./dashboard.php");
    die();
?>
<?php
    return;
}

create_course:
// Check if user is logged into an admin account
if (!isset($_COOKIE['admin_id']) || $_COOKIE['admin_id'] === '') {
    goto error_login;
}



?>
<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>

<body>
    <h1>University Management System</h1>
    <form action="course_create.php" method="post">
        <table>
            <tr>
                <td><label for="course_id">Course ID:</label></td>
                <td><input type="text" name="course_id" maxlength="20" value="<?php if ($course_id) {
                                                                                    echo $course_id;
                                                                                } ?>" /></td>
            </tr>
            <tr>
                <td><label for="course_name">Course Name:</label></td>
                <td><input type="text" name="course_name" maxlength="50" value="<?php if ($course_name) {
                                                                                    echo $course_name;
                                                                                } ?>" /></td>
            </tr>
            <tr>
                <td><label for="credits">Credits:</label></td>
                <td><input type="number" name="credits" maxlength="2" value="<?php if ($credits) {
                                                                                    echo $credits;
                                                                                } ?>" /></td>
            </tr>
            <?php
            if ($error_msg !== '') {
                echo '<tr><td colspan="3">' . $error_msg . '</td></tr>';
            }
            ?>
            <tr>
                <td><input type="submit" name="Create" /></td>
            </tr>
        </table>
    </form>
</body>

</html>

<?php
return;
error_login:
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>

<body>
    <h1>University Management System</h1>
    <p>Please login</p>
</body>

</html>