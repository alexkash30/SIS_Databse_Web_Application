<?php

$student_id = $_POST['student_id'];
$selected_course_id = $_POST['course_id'];
$selected_section_id = $_POST['section_id'];
$selected_year = $_POST['year'];
$selected_semester = $_POST['semester'];

if (!$student_id || !$selected_course_id || !$selected_section_id || !$selected_year || !$selected_semester) {
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

$query = "SELECT * FROM take WHERE course_id='$selected_course_id' AND section_id='$selected_section_id' AND semester='$selected_semester' AND year=$selected_year AND student_id='$student_id'";
if (!($enrollment_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

if ($enrollment_result->num_rows > 0) {
    $response['success'] = "false";
    $response['msg'] = 'Already enrolled in course!';
    echo json_encode($response);
    return;
}

//Check available space in the selected section
$query = "SELECT * FROM take WHERE course_id='$selected_course_id' AND section_id='$selected_section_id' AND semester='$selected_semester' AND year=$selected_year";
if (!($enrollment_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}
$num_students = $enrollment_result->num_rows;

//check if there is available space
if ($num_students >= 15) {
    $response['success'] = "true";
    $response['enrolled'] = "false";
    $response['msg'] = 'Class full!';
    echo json_encode($response);
    return;
}

// Check prereq
$query = "SELECT * from take where student_id='$student_id'";
if (!($classes_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$classes_taken = $classes_result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * from prereq where course_id='$selected_course_id'";
if (!($prereq_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}
$prereq_classes = $prereq_result->fetch_all(MYSQLI_ASSOC);

function checkClasses($prereq_classes, $classes_taken)
{
    foreach ($prereq_classes as $prereq) {
        $satisfied = false;
        foreach ($classes_taken as $class) {
            if ($prereq['prereq_id'] == $class['course_id'] && $class['grade']) {
                $satisfied = true;
                break;
            }
        }
        if (!$satisfied) {
            return false;
        }
    }
    return true;
}

$satisfied = checkClasses($prereq_classes, $classes_taken);

if (!$satisfied) {
    $response['success'] = "true";
    $response['enrolled'] = "false";
    $response['msg'] = 'Prereqs not satisfied!';
    echo json_encode($response);
    return;
}

$query = "SELECT * from undergraduate where student_id='$student_id'";
if (!($query_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

if ($query_result->num_rows) {
    $row = $query_result->fetch_assoc();
    if ($row['hold'] === 'HOLD') {
        $response['success'] = "true";
        $response['enrolled'] = "false";
        $response['msg'] = 'Unable to register. Advising Hold!';
        echo json_encode($response);
        return;
    }
}

$query = "INSERT into take (student_id, course_id, section_id, semester, year) values ('$student_id', '$selected_course_id', '$selected_section_id', '$selected_semester', $selected_year)";

if (!($query_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

$response['success'] = "true";
$response['enrolled'] = "true";
$response['msg'] = 'N/A';
echo json_encode($response);
return;
