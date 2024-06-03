<?php
$email = $_POST['email'];
$password = $_POST['password'];

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    $response['success'] = "false";
    $response['msg'] = 'Could not connect: ' . $mysqli->connect_error;
    echo json_encode($response);
    return;
}

if (!$mysqli->select_db('DB2')) {
    $response['success'] = "false";
    $response['msg'] = 'Could not select database';
    echo json_encode($response);
    return;
}

if (!$email || !$password) {
    $response['success'] = "false";
    $response['msg'] = "Missing password or email";
    echo json_encode($response);
    return;
}

$query = "SELECT * from account where email='" . $email . "';";

if (!($account_result = $mysqli->query($query))) {
    $response['success'] = "false";
    $response['msg'] = 'Query failed: ' . $mysqli->error;
    echo json_encode($response);
    return;
}

// Check if account exists with email then check if password is correct
if ($account_result->num_rows === 0) {
    $response['success'] = "false";
    $response['msg'] = "Incorrect login info";
    echo json_encode($response);
    return;
}

// row is 0: email, 1: password, 3: type
$row = $account_result->fetch_assoc();

// Verify Password
if ($row['password'] !== $password) {
    $response['success'] = "false";
    $response['msg'] = "Incorrect login info";
    echo json_encode($response);
    return;
}

if ($row['type'] == 'student') {
    $query = "SELECT * from student where email='" . $email . "';";
    $student_result = $mysqli->query($query);
    if (!$student_result) {
        $response['success'] = "false";
        $response['msg'] = 'Query failed: ' . $mysqli->error;
        echo json_encode($response);
        return;
    }
    // Student[student_id, name, email, dept_name]
    $student = $student_result->fetch_assoc();

    $response['success'] = "true";
    $response['type'] = "student";
    $response['student_id'] = $student['student_id'];
    $response['student_name'] = $student['name'];
    $response['student_email'] = $student['email'];
    $gradePoints = array(
        'A+' => 4.0,
        'A' => 4.0,
        'A-' => 3.7,
        'B+' => 3.3,
        'B' => 3.0,
        'B-' => 2.7,
        'C+' => 2.3,
        'C' => 2.0,
        'C-' => 1.7,
        'D+' => 1.3,
        'D' => 1.0,
        'D-' => 0.7,
        'F' => 0.0
    );

    $query = "SELECT t.grade as grade, c.credits as credits FROM take t, course c WHERE t.student_id='{$student['student_id']}' AND t.course_id = c.course_id;";

    if (!($result = $mysqli->query($query))) {
        $response['success'] = "false";
        $response['msg'] = 'Query failed: ' . $mysqli->error;
        echo json_encode($response);
        return;
    }

    $total_credits = 0.0;
    $total = 0.0;
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        if ($row['grade']) {
            $total_credits += $row['credits'];
            $grade = $gradePoints[$row['grade']] * $row['credits'];
            $total += $grade;
        }
    }

    if ($total_credits) {
        $response['gpa'] = number_format($total / $total_credits, 2, '.', ',');
    } else {
        $response['gpa'] = "N/A";
    }

    $query = "SELECT * FROM undergraduate WHERE student_id='{$student['student_id']}'";
    if (!($result = $mysqli->query($query))) {
        $response['success'] = "false";
        $response['msg'] = 'Query failed: ' . $mysqli->error;
        echo json_encode($response);
        return;
    }
    if ($result->num_rows > 0) {
        $result = $result->fetch_assoc();
        $response['hold'] = $result['hold'];
    } else {
        $response['hold'] = 'null';
    }

    echo json_encode($response);
    return;
} else if ($row['type'] == 'instructor') {
    $query = "SELECT * from instructor where email='" . $email . "';";
    $instructor_result = $mysqli->query($query);
    if (!$instructor_result) {
        $response['success'] = "false";
        $response['msg'] = 'Query failed: ' . $mysqli->error;
        echo json_encode($response);
        return;
    }
    // Instructor[instructor_id, instructor_name, email, dept_name]
    $instructor = $instructor_result->fetch_assoc();
    $response['success'] = "true";
    $response['type'] = "instructor";
    $response['data'] = $instructor;
    echo json_encode($response);
    return;
}
$response['success'] = "false";
$response['msg'] = "Unknown error";
echo json_encode($response);
return;
