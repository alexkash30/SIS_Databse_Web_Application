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
    $student_id = $_POST['student_id'];

    if (!$student_id) {
        die("Please Post student_id");
    }

    $query = "SELECT * FROM undergraduate where student_id='$student_id'";

    if (!($student_result = $mysqli->query($query)) || $student_result->num_rows == 0) {
        die("Query Failed or student does not exist: $mysqli->error");
    }

    $student = $student_result->fetch_assoc();

    if ($student['hold'] === "HOLD") {
        $query = "UPDATE undergraduate SET hold=NULL where student_id='$student_id';";
    } else {
        $query = "UPDATE undergraduate SET hold='HOLD' where student_id='$student_id';";
    }

    if (!($result = $mysqli->query($query))) {
        die("Query Failed: $mysqli->error");
    }
    header("Location: ./dashboard.php");
} else {
    die("Please Post student_id");
}
