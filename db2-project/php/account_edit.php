<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $type = $_POST['type'];

    $mysqli = new mysqli('localhost', 'root', '');

    if ($mysqli->connect_errno) {
        die('Could not connect: ' . $mysqli->connect_error);
    }

    if (!$mysqli->select_db('DB2')) {
        die('Could not select database');
    }

    if (!$_COOKIE['student_id']) {
        header("Location: ../index.html");
    }

    $sid = $_COOKIE['student_id'];

    $query = "SELECT * from student where student_id=" . $sid . ";";
    if (!($student_result = $mysqli->query($query))) {
        die('Query failed: ' . $mysqli->error);
    }

    if (!$student_result->num_rows) {
        die("Student doesn't exist");
    }

    $student = $student_result->fetch_array(MYSQLI_ASSOC);

    // Check name not null and set
    if ($name) {
        $query = "SELECT * FROM student WHERE name= '" . $name . "';";
        if (!($potential_collision = $mysqli->query($query))) {
            die('Query failed: ' . $mysqli->error);
        }
        $query3 = "UPDATE student SET name='" . $name . "' where name='" . $student['name'] . "';";
        $mysqli->begin_transaction();
        if (!$mysqli->query($query3)) {
            $mysqli->rollback();
            die('Query failed: ' . $mysqli->error);
        } else {
            $mysqli->commit();
        }
    }
    // Check password not null and set account.password
    if ($password) {
        $query = "SELECT * FROM account WHERE password= '" . $password . "';";
        if (!($potential_collision = $mysqli->query($query))) {
            die('Query failed: ' . $mysqli->error);
        }
        $query4 = "UPDATE account SET password='" . $password . "' where email='" . $student['email'] . "';";
        $mysqli->begin_transaction();
        if (!$mysqli->query($query4)) {
            $mysqli->rollback();
            die('Query failed: ' . $mysqli->error);
        }
        $mysqli->commit();
    }
    // Check type not null and remove from current table and insert into other
    if ($type) {
        $delete1 = "DELETE FROM PhD WHERE student_id= '" . $student['student_id'] . "';";
        $delete2 = "DELETE FROM master WHERE student_id= '" . $student['student_id'] . "';";
        $delete3 = "DELETE FROM undergraduate WHERE student_id= '" . $student['student_id'] . "';";

        $mysqli->begin_transaction();
        if (!$mysqli->multi_query($delete1 . $delete2 . $delete3)) {
            $mysqli->rollback();
            die('Query failed: ' . $mysqli->error);
        }
        $mysqli->commit();

        while ($mysqli->more_results()) {
            $mysqli->next_result();
        }

        if ($type === "Undergraduate") {
            $query = "INSERT INTO undergraduate (student_id, total_credits, class_standing) values ('" . $sid . "', 0, NULL);";
        } else if ($type === "Masters") {
            //Insert into Masters
            $query = "INSERT INTO master (student_id, total_credits) values ('" . $sid . "', 0);";
        } else if ($type === "PhD") {
            //Insert into PhD
            $query = "INSERT INTO PhD (student_id, qualifier, proposal_defense_date, dissertation_defense_date) values ('" . $sid . "', NULL, NULL, NULL);";
        }
        if (!($mysqli->query($query))) {
            die('Query failed: ' . $mysqli->error);
        }
    }

    // Check email not null and not in database
    // Update email in student table and account
    if ($email) {
        $query = "SELECT * from account where email='" . $email . "';";

        if (!($potential_collision = $mysqli->query($query))) {
            die('Query failed: ' . $mysqli->error);
        }

        if ($potential_collision->num_rows) {
            die('Email already exits');
            // goto error_page;
        }

        $query1 = "UPDATE account SET email='" . $email . "' where email='" . $student['email'] . "';";

        $query2 = "UPDATE student SET email='" . $email . "' where student_id=" . $student['student_id'] . ";";

        $mysqli->begin_transaction();
        if (!$mysqli->multi_query($query1 . $query2)) {
            $mysqli->rollback();
            die('Query failed: ' . $mysqli->error);
        } else {
            while ($mysqli->more_results()) {
                $mysqli->next_result();
            }
            $mysqli->commit();
        }
    }
    header("Location: ./dashboard.php");
    exit("This should not have happened");
}


?>

<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System: Update Account</title>
</head>

<body>
    <h1>Update Account Information</h1>
    <form action="account_edit.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>"><br>

        <label for="password">New Password (leave blank to keep current):</label>
        <input type="password" name="password"><br>

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($userInfo['name']); ?>"><br>

        <label for="type">Type:</label>
        <select name="type">
            <option value="Undergraduate" <?php if ($userInfo['type'] == 'Undergraduate') echo 'selected'; ?>>Undergraduate</option>
            <option value="Masters" <?php if ($userInfo['type'] == 'Masters') echo 'selected'; ?>>Masters</option>
            <option value="PhD" <?php if ($userInfo['type'] == 'PhD') echo 'selected'; ?>>PhD</option>
        </select>
        <br />

        <input type="submit" value="Update Account">
    </form>
</body>

</html>