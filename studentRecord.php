<?php
session_start();
include "checks/loggedIn.php";
include "checks/studentLogged.php";
include "functionality/errorResponse.php";
include "database.php";
$data = array();
$studentid = $overall = $votes = $found = 0;
$overall_err = $req_err = "";
$finished = false;
$db = new Database();
if (!empty($_SERVER['QUERY_STRING'])) {
    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);
    if (!isset($queries['studentid'])) {
        $req_err = "Student id must be formed of digits only.";
    }
    $studentid = $queries['studentid'];
    if (preg_match('/[^0-9]/', $studentid)) {
        $req_err = "Student id must be formed of digits only.";
    }
    if (empty($req_err)) {
        $sql = "SELECT * FROM assessments WHERE graded_id = $studentid";
        if ($data = $db->dontWorryQuery($sql)) {
            $found = count($data);
            if ($found === 0) {
                $overall_err = "This student has no peers in his/her group";
            } else if ($found < 2) {
                $overall_err = "To produce an overall a group must contain three students.";
            } else if ($found == 2) {
                foreach ($data as $peerEval) {
                    if ($peerEval["finalized"] === 1) {
                        $overall += $peerEval["grade"];
                        $votes++;
                    }
                }
                if ($votes === 2) {
                    $overall /= 2;
                } else {
                    $overall_err = "Peers have not finished their evaluation.";
                }
            }
        } else {
            $overall_err = "No peers.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Student Record check</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="custom.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <style>
        label {
            font-weight: bold;
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
        <?php if (!empty($req_err)) : ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Request error!</h4>
                <p><?php echo $req_err ?></p>
            </div>
        <?php endif; ?>
        <?php if (empty($overall_err) && empty($req_err)) : ?>
            <div class="alert alert-success col-12 col-sm-12 col-md-10 col-lg-6" role="alert">
                <h4 class="alert-heading">Student's overall grade is <?php echo $overall ?></h4>
            </div>
        <?php endif; ?>
        <?php if ($found > 0) : ?>
            <?php foreach ($data as $value) : ?>
                <div class="col-12 col-sm-12 col-md-10 col-lg-6 assessment">
                    <h3 class="display-5">Assessment from <?php echo $value['grader_id'] ?></h3>
                    <div class="form-group row">
                        <label for="grade" class="col col-form-label">Grade:</label>
                        <div class="col-6 col-sm-8 col-md-8 col-lg-8">
                            <input type="text" readonly class="form-control-plaintext" id="grade" value="<?php echo $value["grade"]; ?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="justification" class="col col-form-label">Justification:</label>
                        <div class="col-6 col-sm-8 col-md-8 col-lg-8">
                            <input type="text" readonly class="form-control-plaintext" id="justification" value="<?php echo htmlentities($value["justification"]); ?>" />
                        </div>
                    </div>
                    <?php if ($value['image_id'] !== NULL) : ?>
                        <div class="form-group row">
                            <label for="image" class="col col-form-label">Image:</label>
                            <div class="col-6 col-sm-8 col-md-8 col-lg-8">
                                <div class="img-container">
                                    <?php echo $db->getImage($value['image_id']); ?>
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
    <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.html") ?>
    <script>
        $(document).ready(function() {
        $(".stdid").on("click",  function() {
                window.location = 'studentRecord.php?studentid=' + $(this).text().trim();
            });
        });
    </script>
</body>

</html>