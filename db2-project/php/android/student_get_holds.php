<?php

$instructor_id = $_POST['instructor_id'];

if (!$instructor_id) {
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
$today = date('Y-d-m');
$query = "SELECT s.student_id as student_id, s.name as student_name, u.hold as hold FROM student s, undergraduate u WHERE s.student_id=u.student_id AND s.student_id IN (SELECT student_id FROM advise_undergraduate WHERE instructor_id='$instructor_id' AND start_date>='$today' AND (end_date is NULL or end_date >='$today'))";

if (!($student_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
$myArray = array();
while ($row = $student_result->fetch_assoc()) {
    $myArray[] = $row;
}
$response['data'] = $myArray;
echo json_encode($response);
return;
