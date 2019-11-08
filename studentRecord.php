<?php
session_start();
$data = array();
$studentid = 0;
$found = 0;
require "classes/oldConnect.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
if (!empty($_SERVER['QUERY_STRING'])) {
    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);
    $studentid = $queries['studentid'];
    $sql = "SELECT * FROM assessments WHERE graded_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_graded_id);
        $param_graded_id = $studentid;
        if ($eval = $stmt->execute()) {
            $result = $stmt->get_result();
            $found = $result->num_rows;
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->free_result();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Student Record check</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">

</head>

<body>
    <?php include("navbarTutor.php") ?>
    <h1>Student <?php echo $studentid ?> record</h1>
    <?php if ($found > 0) : ?>
        <?php foreach ($data as $value) : ?>
            <p><?php echo $value['grade'] ?></p>
        <?php endforeach;
        else : ?>
        <p>No assessments</p>
    <?php endif; ?>
</body>

</html>