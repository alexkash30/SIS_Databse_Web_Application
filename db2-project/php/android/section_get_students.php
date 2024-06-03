<?php

$instructor_id = $_POST['instructor_id'];
$course_id = $_POST['course_id'];
$section_id = $_POST['section_id'];
$semester = $_POST['semester'];
$year = $_POST['year'];

if (!$instructor_id || !$course_id || !$section_id || !$semester || !$year) {
    $response['success'] = "false";
    $response['msg'] = 'Missing fields!';
    echo json_encode($response);
    return;
}

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    $response['success'] = "false";
    $response['msg'] = 'Could not connect: ' . $mysqli->connect_error;
    echo json_encode($response);
    return;
}

if (!$mysqli->select_db('DB2')) {
    $response['success'] = "false";
    $response['msg'] = 'Could not select database: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$query = "SELECT s.student_id as student_id, s.name as name, t.grade as grade FROM take t, student s WHERE t.student_id=s.student_id and t.course_id='$course_id' and t.section_id='$section_id' and t.semester='$semester' and t.year=$year";

if (!($students_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
$myArray = array();
while ($row = $students_result->fetch_assoc()) {
    $myArray[] = $row;
}
$response['data'] = $myArray;
echo json_encode($response);
return;
