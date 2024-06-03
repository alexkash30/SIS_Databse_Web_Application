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

$query = "SELECT s.student_id, s.name as student_name, o.advisor as advisor from student s LEFT JOIN (SELECT a.student_id as student_id, i.instructor_name as advisor FROM advise_undergraduate a, instructor i WHERE a.instructor_id=i.instructor_id) o ON s.student_id=o.student_id WHERE s.student_id in (SELECT student_id FROM undergraduate)";

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
