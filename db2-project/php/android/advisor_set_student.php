<?php

$instructor_id = $_POST['instructor_id'];
$student_id = $_POST['student_id'];
$advisor_id = $_POST['advisor_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

if (!$instructor_id || !$student_id || !$advisor_id) {
    $response['success'] = "false";
    $response['msg'] = 'Missing fields!';
    echo json_encode($response);
    return;
}
if (!$start_date) {
    $response['success'] = "false";
    $response['msg'] = 'Missing start date!';
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

$query = "DELETE FROM advise_undergraduate WHERE student_id='$student_id'";
if (!($query_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$start_date = date('Y-m-d', strtotime($start_date));
if ($end_date) {
    $end_date = date('Y-m-d', strtotime($end_date));
    $query = "INSERT into advise_undergraduate(instructor_id, student_id, start_date, end_date) values ('$advisor_id', '$student_id', '$start_date', '$end_date')";
} else {
    $query = "INSERT into advise_undergraduate(instructor_id, student_id, start_date) values ('$advisor_id', '$student_id', '$start_date')";
}

if (!($query_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
echo json_encode($response);
return;
