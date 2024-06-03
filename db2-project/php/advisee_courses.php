<?php

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

if (!isset($_COOKIE['instructor_id']) || $_COOKIE['instructor_id'] === '') {
    die("Please login to an instructor account");
    // goto error_login;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$_POST['courses']) {
        die("No student given");
    }

    $student_id = $_POST['courses'];

    $query = "SELECT * FROM take WHERE student_id='$student_id'";
} else {
    die("Should not have happened");
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>Viewing Student Courses System</title>
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

    echo '<h2> Student ' . $student_id . '</h2>';

    ?>
    <h3>History and Grades:</h3>
    <table>
        <tr>
            <th>Course ID</th>
            <th>Section ID</th>
            <th>Semester</th>
            <th>Year</th>
            <th>Grade</th>
        </tr>
        <?php
        $courses = $mysqli->query("SELECT * from take where student_id=" . $student_id . ";");
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
</body>

</html>