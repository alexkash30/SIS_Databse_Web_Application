<?php
$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

if (!isset($_COOKIE['instructor_id']) || $_COOKIE['instructor_id'] === '') {
    die("Please login to an instructor account");
    // goto error_login;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['update']) {
        goto phd_edit;
    }

    $student_id = $_POST['student_id'];
    $qualifier = $_POST['qualifier'];
    $proposal_defense_date = $_POST['proposal_defense_date'];
    $dissertation_defense_date = $_POST['dissertation_defense_date'];

    $query = "SELECT * from PhD where student_id=" . $student_id . ";";
    if (!($student_result = $mysqli->query($query))) {
        die('Query failed: ' . $mysqli->error);
    }

    if (!$student_result->num_rows) {
        die("Student doesn't exist");
    }

    $student = $student_result->fetch_assoc();

    // Check qualifier not null and set
    if ($qualifier) {
        $query = "UPDATE PhD SET qualifier='" . $qualifier . "' where student_id='" . $student_id . "';";
        if (!$mysqli->query($query)) {
            die("Query failed: $mysqli->error");
        }
    }
    // Check password not null and set account.password
    if ($proposal_defense_date) {
        $date = date('Y-m-d', strtotime($proposal_defense_date));

        $query = "UPDATE PhD SET proposal_defense_date='" . $date . "' where student_id='" . $student_id . "';";
        if (!$mysqli->query($query)) {
            die('Query failed: ' . $mysqli->error);
        }
    }

    if ($dissertation_defense_date) {
        $date = date('Y-m-d', strtotime($dissertation_defense_date));

        $query = "UPDATE PhD SET dissertation_defense_date='" . $dissertation_defense_date . "' where student_id='" . $student_id . "';";
        if (!$mysqli->query($query)) {
            die('Query failed: ' . $mysqli->error);
        }
    }

    header("Location: ./dashboard.php");
    exit("This should not have happened");
}
die("Please post student_id");

phd_edit:

$student_id = $_POST['update'];

?>


<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System: Update PhD Student Information</title>
</head>

<body>
    <h1>Update PhD Student Information</h1>
    <form action="" method="post">
        <table>
            <tr>
                <?php echo '<input type="hidden" name="student_id" value="' . $student_id . '" />'; ?>
            </tr>
            <tr>
                <td><label for="qualifier">Qualifier:</label></td>
                <td><input type="text" name="qualifier" value="" maxlength="30"></td>
            </tr>
            <tr>
                <td><label for="proposal_defense_date">Proposal Defense Date:</label></td>
                <td><input type="date" name="proposal_defense_date"></td>
            </tr>

            <tr>
                <td><label for="dissertation_defense_date">Dissertation Defense Date:</label></td>
                <td><input type="date" name="dissertation_defense_date"></td>
            </tr>
        </table>
        <input type="submit" value="Update Account">
    </form>
</body>