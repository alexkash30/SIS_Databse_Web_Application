<?php

$instructor_id = $_POST['instructor_id'];
$student_id = $_POST['student_id'];

if (!$instructor_id || !$student_id) {
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

$query = "SELECT * FROM advise_undergraduate WHERE instructor_id='$instructor_id' AND student_id='$student_id'";

if (!($advise_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

if ($advise_result->num_rows <= 0) {
    $response['success'] = "false";
    $response['msg'] = 'You are not an advisor for this student';
    echo json_encode($response);
    return;
}

$advise = $advise_result->fetch_assoc();
$today = date('Y-m-d');
$start_date = $advise['start_date'];

if ($start_date > $today) {
    $response['success'] = "false";
    $response['msg'] = 'Advising start date has not begun.';
    echo json_encode($response);
    return;
}

if ($advise['end_date'] && $advise['end_date'] < $today) {
    $response['success'] = "false";
    $response['msg'] = 'Advising end date has passed.';
    echo json_encode($response);
    return;
}

$query = "SELECT hold FROM undergraduate WHERE student_id='$student_id'";

if (!($advise_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}
$hold = $advise_result->fetch_assoc();
if ($hold['hold'] === 'HOLD') {
    $query = "UPDATE undergraduate SET hold=NULL where student_id='$student_id';";
} else {
    $query = "UPDATE undergraduate SET hold='HOLD' where student_id='$student_id';";
}

if (!($result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
echo json_encode($response);
return;
