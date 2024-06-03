<?php
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$sid = $_POST['student_id'];
$name = $_POST['name'];
$type = $_POST['type'];

if (!$email || !$password || !$confirmPassword || !$sid || !$name || !$type) {
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

// Check if sid in database
$query = "SELECT * from student where student_id=" . $sid . ";";

if (!($student_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query Failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

if ($student_result->num_rows > 0) {
    $response['success'] = "false";
    $response['msg'] = 'Student id taken!';
    echo json_encode($response);
    return;
}

// Check if passwords match

if ($password !== $confirmPassword) {
    $response['success'] = "false";
    $response['msg'] = 'Passwords mismatched!';
    echo json_encode($response);
    return;
}

// Check if email already exists in accounts table
$query = "SELECT * from account where email='" . $email . "';";

if (!($account_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query Failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

if ($account_result->num_rows > 0) {
    $response['success'] = "false";
    $response['msg'] = 'Email already used!';
    echo json_encode($response);
    return;
}

// Create account
$account_query = "INSERT INTO account (email, password, type) values ('" . $email . "', '" . $password . "', 'student');";
// Insert into students
$student_query = "INSERT INTO student (student_id, name, email, dept_name) values ('" . $sid . "', '" . $name . "', '" . $email . "', NULL);";
$query = '';
// Insert into Undergrad
if ($type === "Undergraduate") {
    $query = "INSERT INTO undergraduate (student_id, total_credits, class_standing) values ('" . $sid . "', 0, NULL);";
} else if ($type === "Masters") {
    //Insert into Masters
    $query = "INSERT INTO master (student_id, total_credits) values ('" . $sid . "', 0);";
} else if ($type === "PhD") {
    //Insert into PhD
    $query = "INSERT INTO PhD (student_id, qualifier, proposal_defense_date, dissertation_defense_date) values ('" . $sid . "', NULL, NULL, NULL);";
    if (!($mysqli->query($query))) {
        $response['success'] = "false";
        $response['msg'] = 'Query Failed: ' . $mysqli->error;
        echo json_encode($response);
        return;
    }
}
$mysqli->begin_transaction();
if (!($mysqli->multi_query($account_query . $student_query . $query))) {
    $mysqli->rollback();
    $response['success'] = "false";
    $response['msg'] = 'Query Failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}
while ($mysqli->more_results()) {
    $mysqli->next_result();
}
$mysqli->commit();

$response['success'] = "true";
$response['student_id'] = $sid;
echo json_encode($response);
return;
