<?php

// Check if user is logged into a student account
if (!isset($_COOKIE['instructor_id']) || $_COOKIE['instructor_id'] === '') {
    die("Please login to admin account");
    // goto error_login;
}

$instructor_id = $_COOKIE['instructor_id'];

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id = $_POST['course_id'];
    $section_id = $_POST['section_id'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
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

    <h3> Students: </h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Grade</th>
        </tr>
        <?php
        $query = "SELECT * from student s, take t where s.student_id = t.student_id and t.course_id='$course_id' and t.section_id='$section_id' and t.semester='$semester' and t.year=$year;";

        if(!($students_result = $mysqli->query($query))){
            die("Courses query failed");
        }

        while ($row = $students_result->fetch_array(MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['student_id'] . '</td>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['grade'] . '</td>';
        }
        ?>
    </table>
</body>

</html>
    <?php
}
?>