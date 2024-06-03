<?php

$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}

if (!$mysqli->select_db('DB2')) {
    die('Could not select database');
}

if ((!isset($_COOKIE['admin_id']) || $_COOKIE['admin_id'] === '') && (!isset($_COOKIE['instructor_id']) || $_COOKIE['instructor_id'] === '')) {
    die("Please login to admin or instructor account");
    // goto error_login;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
}
goto advisor_panel;
?>

<?php

advisor_panel:

?>

<html>

<head>
    <meta charset="UTF-8">
    <title>University Management System</title>
</head>

<style>
    table,
    th,
    td {
        border: 1px solid black;
    }
</style>


<body>
    <h1>University Management System</h1>

    <h3>PhD Students:</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Advisor 1 Name</th>
            <th>Advisor 1 ID</th>
            <th>Advisor 1 Start Date</th>
            <th>Advisor 1 End Date</th>
            <th>Advisor 2 Name</th>
            <th>Advisor 2 ID</th>
            <th>Advisor 2 Start Date</th>
            <th>Advisor 2 End Date</th>
        </tr>
        <?php
        $query = "SELECT * from student s, PhD p where s.student_id=p.student_id";

        if (!($student_result = $mysqli->query($query))) {
            die("Query failed $mysqli->error");
        }

        while ($student = $student_result->fetch_assoc()) {
            $student_id = $student['student_id'];
            echo "<tr>";
            echo "<td>$student_id</td>";
            echo "<td>{$student['name']}</td>";

            $query = "SELECT * from advise a, instructor i where student_id='$student_id' and a.instructor_id=i.instructor_id";

            if (!($advisor_result = $mysqli->query($query))) {
                die("Query failed $mysqli->error");
            }
            while ($advisor = $advisor_result->fetch_array()) {
                echo "<td>{$advisor['instructor_name']}</td>";
                echo "<td>{$advisor['instructor_id']}</td>";
                echo "<td>{$advisor['start_date']}</td>";
                if ($advisor['end_date']) {
                    echo "<td>" . $advisor['end_date'] . "</td>";
                } else {
                    echo "<td>" . "-" . "</td>";
                }
            }
            $count = 2 - $advisor_result->num_rows;
            while ($count > 0) {
                echo '<td><form action="./advisor_assign_phd.php" method="post"><button name="assign" value="' . $student_id . '">Assign</button></form></td>';
                echo "<td>-</td>";
                echo "<td>-</td>";
                echo "<td>-</td>";
                $count--;
            }
            echo "</tr>";
        }
        ?>
    </table>

    <h3>Undergraduate Students:</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Advisor Name</th>
            <th>Advisor ID</th>
            <th>Advisor Start Date</th>
            <th>Advisor End Date</th>
        </tr>
        <?php
        $query = "SELECT * from student s, undergraduate u where s.student_id=u.student_id";

        if (!($student_result = $mysqli->query($query))) {
            die("Query failed $mysqli->error");
        }

        while ($student = $student_result->fetch_assoc()) {
            $student_id = $student['student_id'];
            echo "<tr>";
            echo "<td>$student_id</td>";
            echo "<td>{$student['name']}</td>";

            $query = "SELECT * from advise_undergraduate a, instructor i where student_id='$student_id' and a.instructor_id=i.instructor_id";

            if (!($advisor_result = $mysqli->query($query))) {
                die("Query failed $mysqli->error");
            }
            while ($advisor = $advisor_result->fetch_array()) {
                echo "<td>{$advisor['instructor_name']}</td>";
                echo "<td>{$advisor['instructor_id']}</td>";
                echo "<td>{$advisor['start_date']}</td>";
                if ($advisor['end_date']) {
                    echo "<td>" . $advisor['end_date'] . "</td>";
                } else {
                    echo "<td>" . "-" . "</td>";
                }
            }
            $count = 1 - $advisor_result->num_rows;
            while ($count > 0) {
                echo '<td><form action="./advisor_assign_undergraduate.php" method="post"><button name="assign" value="' . $student_id . '">Assign</button></form></td>';
                echo "<td>-</td>";
                echo "<td>-</td>";
                echo "<td>-</td>";
                $count--;
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>


</html>