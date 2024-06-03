<?php

$instructor_id = $_POST['instructor_id'];

if (!$instructor_id) {
    $response['success'] = "false";
    $response['msg'] = 'Missing Instructor ID!';
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

$query = "SELECT * FROM semester ORDER BY status, year, semester";

if (!($semester_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
$myArray = array();
while ($row = $semester_result->fetch_assoc()) {
    $myArray[] = $row;
}
$response['data'] = $myArray;
echo json_encode($response);
return;
