<?php
$student_id = $_POST['student_id'];

if (!$student_id) {
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

$query = "SELECT * FROM section s, course c WHERE EXISTS (SELECT * from semester c WHERE c.status='Current' and s.year=c.year and s.semester=c.semester) AND s.course_id=c.course_id";

if (!($section_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
$myArray = array();
while ($row = $section_result->fetch_assoc()) {
    $query = "SELECT * FROM take WHERE course_id='{$row['course_id']}' AND section_id='{$row['section_id']}' AND semester='{$row['semester']}' AND year={$row['year']}";
    if (!($query_result = $mysqli->query($query))) {
        $response['success'] = "false";
        $response['msg'] = 'Query failed: ' . $mysqli->error;
        echo json_encode($response);
        return;
    }
    $row['num_students'] = $query_result->num_rows;
    $myArray[] = $row;
}
$response['data'] = $myArray;
echo json_encode($response);
return;
