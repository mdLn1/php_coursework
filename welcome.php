<?php
session_start();
$data = array();
require "classes/oldConnect.php";
$group_number = "";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
if($_SESSION["role"] == "tutor"){
    header("location: welcomeTutor.php");
    exit;
}
// if (!isset($_SESSION["email"])) {
//     $sql = "SELECT email, role FROM users WHERE ID = ?";
//     if ($stmt = $mysqli->prepare($sql)) {
//         $stmt->bind_param("s", $param_ID);

//         $param_ID = $_SESSION["ID"];
//         if ($stmt->execute()) {
//             $result = $stmt->get_result();

//             if ($result->num_rows == 1) {
//                 $first_row = $result->fetch_assoc();
//                 $_SESSION["email"] = $first_row['email'];
//                 $_SESSION["role"] = $first_row['role'];

//                 $stmt->free_result();
//             }
//             $stmt->close();
//         }
//     }
// }
if ($_SESSION["role"] == "student") {
    $sql = "SELECT group_number FROM students WHERE ID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_ID);
        $param_ID = $_SESSION["ID"];
        if ($stmt->execute()) {
            // Store result
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($group_number);
                $stmt->fetch();
            }
            $stmt->close();
        }
    }
}
if (!empty($group_number)) {
    $sql = "SELECT * FROM assessments WHERE grader_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_grader_id);
        $param_grader_id = $_SESSION["ID"];
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            /* Get the number of rows */
            $num_of_rows = $result->num_rows;
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->free_result();
        }
        $stmt->close();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "";
    if (isset($_POST['btnFinish'])) {
        $sql = "UPDATE assessments SET grade = ?, justification = ?, finalized = 1 WHERE grader_id = ? AND graded_id = ?";
    } else if (isset($_POST['btnSave'])) {
        $sql = "UPDATE assessments SET grade = ?, justification = ? WHERE grader_id = ? AND graded_id = ?";
    }
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("dsss", $param_grade, $param_justification, $param_grader_id, $param_graded_id);

        $param_grade = $_POST["grade"];
        $param_justification = $_POST["justification"];
        $param_grader_id = $_SESSION["ID"];
        $param_graded_id = $_POST["gradedId"];
        if ($stmt->execute()) {
            echo "Updated";
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
    <title>Peer review</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <style>
        form.col-sm-6 {
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #d6d7d8;
            z-index: 1;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>
</head>

<body>
    <?php include("navbar.php") ?>

    <h1 class="display-3" style="text-align: center; padding-bottom: 2rem">Peer assessments group <?php echo $group_number ?></h1>
    <div class="jumbotron">
        <?php
        foreach ($data as $value) : ?>
            <form class="col-sm-6" id="<?php echo $value["graded_id"] ?>" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="gradedId" id="<?php echo $value["graded_id"] ?>" value="<?php echo $value['graded_id'] ?>">
                <h3 class="display-5">Assessment for <?php echo $value['graded_id'] ?></h3>
                <div class="form-group">
                    <label for="grade">Grade</label>
                    <select class="form-control" id="grade" name="grade">
                        <option <?php if ($value["grade"] == 1) echo 'selected'; ?>>1</option>
                        <option <?php if ($value["grade"] == 2) echo 'selected'; ?>>2</option>
                        <option <?php if ($value["grade"] == 3) echo 'selected'; ?>>3</option>
                        <option <?php if ($value["grade"] == 4) echo 'selected'; ?>>4</option>
                        <option <?php if ($value["grade"] == 5) echo 'selected'; ?>>5</option>
                        <option <?php if ($value["grade"] == 6) echo 'selected'; ?>>6</option>
                        <option <?php if ($value["grade"] == 7) echo 'selected'; ?>>7</option>
                        <option <?php if ($value["grade"] == 8) echo 'selected'; ?>>8</option>
                        <option <?php if ($value["grade"] == 9) echo 'selected'; ?>>9</option>
                        <option <?php if ($value["grade"] == 10) echo 'selected'; ?>>10</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="justification">Justification</label>
                    <textarea class="form-control" id="justification" name="justification" rows="3"><?php echo $value['justification'] ?></textarea>
                </div>
                <hr class="my-4">
                <?php if ($value['finalized'] == 1) : ?>
                    <p class="text-danger">You already submitted the final grade.</p>
                <?php else : ?>
                <p>Click finish to finalize the grade.</p>
                <input type="submit" class="btn btn-primary btn-lg" name="btnFinish" value="Finish" />
                <input type="submit" class="btn btn-success btn-lg" name="btnSave" value="Save" />
                <?php endif; ?>
               
            </form>
        <?php endforeach; ?>
    </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/Bootstrap_tutorial.js"></script>
</body>

</html>