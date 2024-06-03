<?php

$instructor_id = $_POST['instructor_id'];
$semester = $_POST['semester'];
$year = $_POST['year'];

if (!$instructor_id || !$semester || !$year) {
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

$query = "SELECT * FROM course c, section s WHERE c.course_id=s.course_id AND s.instructor_id='$instructor_id' AND s.semester='$semester' AND s.year=$year";

if (!($classes_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
$myArray = array();
while ($row = $classes_result->fetch_assoc()) {
    $query = "SELECT * FROM take WHERE course_id='{$row['course_id']}' AND section_id='{$row['section_id']}' AND semester='{$row['semester']}' AND year='{$row['year']}'";
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
