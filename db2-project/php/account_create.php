<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $sid = $_POST['sid'];
    $name = $_POST['name'];
    $type = $_POST['type'];

    if (!$email || !$password || !$confirmPassword || !$sid || !$name || !$type) {
        $error_msg = 'Missing fields!';
        goto error;
    }

    $mysqli = new mysqli('localhost', 'root', '');

    if ($mysqli->connect_errno) {
        die('Could not connect: ' . $mysqli->connect_error);
    }

    if (!$mysqli->select_db('DB2')) {
        die('Could not select database: ' . $mysqli->error);
    }

    // Check if sid in database
    $query = "SELECT * from student where student_id=" . $sid . ";";

    if (!($student_result = $mysqli->query($query))) {
        die("Could not query student: " . $mysqli->error);
    }

    if ($student_result->num_rows > 0) {
        $error_msg = 'Student id taken!';
        goto error;
    }

    // Check if passwords match

    if ($password !== $confirmPassword) {
        $error_msg = 'Passwords mismatched!';
        goto error;
    }

    // Check if email already exists in accounts table
    $query = "SELECT * from account where email='" . $email . "';";

    if (!($account_result = $mysqli->query($query))) {
        die('Query failed: ' . $mysqli->error);
    }

    if ($account_result->num_rows > 0) {
        $error_msg = 'Email already used!';
        goto error;
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
            die('Query failed: ' . $mysqli->error);
        }
    }
    $mysqli->begin_transaction();
    if (!($mysqli->multi_query($account_query . $student_query . $query))) {
        $mysqli->rollback();
        die('Query failed: ' . $mysqli->error);
    }
    while ($mysqli->more_results()) {
        $mysqli->next_result();
    }
    $mysqli->commit();

    header("Location: ../index.html");
    die();
?>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>University Management System</title>
    </head>

    <body>
        Account Created
    </body>

    </html>
<?php }

error:
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>

<body>
    <h1>University Management System: Create Account</h1>
    <table>
        <form action="account_create.php" method="post">
            <table>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" name="email" maxlength="50" value="<?php if ($email) {
                                                                                    echo $email;
                                                                                } ?>"></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" name="password" maxlength="20" value="<?php if ($password) {
                                                                                            echo $password;
                                                                                        } ?>"></td>
                </tr>
                <tr>
                    <td><label for="password">Confirm Password:</label></td>
                    <td><input type="password" name="confirmPassword" maxlength="20" value="<?php if ($confirmPassword) {
                                                                                                echo $confirmPassword;
                                                                                            } ?>"></td>
                </tr>
                <tr>
                    <td><label for="student_id">Student ID:</label></td>
                    <td><input type="number" name="sid" maxlength="10" value="<?php if ($sid) {
                                                                                    echo $sid;
                                                                                } ?>"></td>
                </tr>
                <tr>
                    <td><label for="name">Name:</label></td>
                    <td><input type="text" name="name" maxlength="20" value="<?php if ($name) {
                                                                                    echo $name;
                                                                                } ?>"></td>
                </tr>
                <tr>
                    <td><label for="type">Type:</label></td>
                    <td>
                        <select name="type">
                            <option <?php if ($type && $type === "Undergraduate") {
                                        echo 'selected';
                                    } ?>>Undergraduate</option>
                            <option <?php if ($type && $type === "Masters") {
                                        echo 'selected';
                                    } ?>>Masters</option>
                            <option <?php if ($type && $type === "PhD") {
                                        echo 'selected';
                                    } ?>>PhD</option>
                        </select>
                    </td>
                </tr>
                <?php
                if ($error_msg) {
                    echo '<tr>' . $error_msg . '</tr>';
                }
                ?>
                <td><input type="submit" value="Register" /></td>
            </table>
        </form>

    </table>
</body>

</html>
<?php
return;
?>