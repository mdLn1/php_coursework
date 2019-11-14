<?php
session_start();

include "checks/databaseConnection.php";

include "checks/loggedIn.php";
include "checks/studentLogged.php";
include "functionality/fetchImage.php";

$data = array();
$studentid = $overall = $votes = $found = 0;
$overall_err = "";
$finished = false;

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
                if ($row["finalized"] === 1) {
                    $votes += 1;
                }
            }
            $length = count($data);
            if ($votes !== $length && $votes !== 2) {
                if ($length === 0) {
                    $overall_err = "This student has no peers in his/her group";
                } else if ($length < 2) {
                    $overall_err = "To produce an overall a group must contain three students.";
                } else if ($length == 2) {
                    $overall_err = "Peers have not finished their evaluation.";
                }
            } else if ($votes === 2) {
                foreach ($data as $peerEval) {
                    $overall += $peerEval["grade"];
                }
                $overall = $overall / $length;
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
    <style>
        .record-container {
            padding: 1rem;
        }

        img.extra-img {
            margin-bottom: 1rem;
        }

        img.extra-img {
            max-width: 100%;
            height: auto;
        }

        .img-container {
            width: 5rem;
            height: auto;
            min-width: 3rem;
        }

        div.col-sm-6 {
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #d6d7d8;
            z-index: 1;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        label {
            font-weight: bold;
        }

        input {
            padding-left: 1rem;
        }

        .stdid {
            text-decoration: underline;
            color: blue;
            font-size: 1.5rem;
            font-family: 'Times New Roman', Times, serif;
        }

        .stdid:hover {
            cursor: pointer;
        }
    </style>
</head>

<body>
<?php include("pageContent/navbar.php") ?>
    <div class="record-container">
        <h1>Student <?php echo $studentid ?> record</h1>
        <br />
        <?php if (!empty($overall_err)) : ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">No overall available!</h4>
                <p><?php echo $overall_err ?></p>
            </div>
        <?php endif; ?>
        <?php if ($overall > 0) : ?>
            <h2>Student's overall grade is <?php echo $overall ?></h2>
            <?php foreach ($data as $value) : ?>
                <div class="col-sm-6">
                    <h3 class="display-5">Assessment from <?php echo $value['grader_id'] ?></h3>
                    <div class="form-group row">
                        <label for="grade" class="col-sm-2 col-form-label">Grade:</label>
                        <div class="col-sm-10">
                            <input type="text" readonly class="form-control-plaintext" id="grade" value="<?php echo $value["grade"]; ?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="justification" class="col-sm-2 col-form-label">Justification:</label>
                        <div class="col-sm-10">
                            <input type="text" readonly class="form-control-plaintext" id="justification" value="<?php echo $value["justification"]; ?>" />
                        </div>
                    </div>
                    <?php if ($value['image_id'] === NULL) : ?>
                        <div class="form-group row">
                            <label for="image" class="col-sm-2 col-form-label">Image:</label>
                            <div class="col-sm-10">
                                <div class="img-container">
                                    <?php echo getImage($mysqli, 15); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($found > 0) : ?>
            <h3>Student's colleagues</h3>
            <?php foreach ($data as $value) : ?>
                <p><span class="stdid"><?php echo $value["grader_id"] ?></span></p>
            <?php endforeach;
            else : ?>
            <p>No colleagues registered yet!</p>
        <?php endif; ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".stdid").click(function() {
                window.location = 'studentRecord.php?studentid=' + $(this).text().trim();
            });
        });
    </script>
</body>

</html>