<?php
session_start();

include "checks/loggedIn.php";
include "checks/tutorLogged.php";
$data = array();
$group_number = $grade_err = $graded_id_err = $justification_err = $image_err = $image_success = $record_updated = "";
include "database.php";

$db = new Database();

if ($result = $db->dontWorryQuery("SELECT group_number FROM students WHERE ID = " . $_SESSION["ID"], $moreThanOne = false)) {
    $group_number = $result["group_number"];
}

if ($result = $db->dontWorryQuery("SELECT * FROM assessments WHERE grader_id = " . $_SESSION["ID"])) {
    $data = $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbConnect = $db->getDbConnection();
    $justification = $graded_id = $grader_id = "";
    $grade = 0;
    $grader_id = $_SESSION["ID"];
    $finalize = isset($_POST['btnFinish']);
    $delete = isset($_POST['btnDelete']);
    $save = isset($_POST['btnSave']);
    $image_exists = isset($_POST["image-exists"]);
    if (!empty(trim($_POST["justification"]))) {
        $justification = trim($_POST["justification"]);
        if (strlen($justification) > 1000) {
            $justification_err = "Justification too long";
        }
    }
    if (!empty(trim($_POST["graded_id"]))) {
        $graded_id = trim($_POST["graded_id"]);
        if (preg_match('/[^0-9]/', $graded_id)) {
            $graded_id_err = "Incorrect peer id.";
        }
        if (strlen($graded_id) !== 9) {
            $graded_id_err = "Incorrect peer id.";
        }
    }
    if (!empty(trim($_POST["grade"]))) {
        $grade = trim($_POST["grade"]);
        if (preg_match('/[^1-9]/', $grade)) {
            $grade_err = "Grade value must be between 0 and 10 inclusive";
        }
        if ($grade < 0 || $grade > 10) {
            $grade_err = "Grade value must be between 0 and 10 inclusive";
        }
    }
    if (empty($grade_err) && empty($graded_id_err) && empty($justification_err)) {
        if (!empty($_FILES["fileToUpload"]["size"])) {
            if (!preg_match('/gif|png|x-png|jpeg/', $_FILES['fileToUpload']['type'])) {
                $image_err = '<p>Only browser compatible images allowed</p>';
            } else if ($_FILES['fileToUpload']['size'] > 100000) {
                $image_err = '<p>Sorry file too large</p>';
            } else if (!($handle = fopen($_FILES['fileToUpload']['tmp_name'], "r"))) {
                $image_err = '<p>Error opening temp file</p>';
            } else if (!($image = fread($handle, filesize($_FILES['fileToUpload']['tmp_name'])))) {
                $image_err = '<p>Error reading temp file</p>';
            } else {
                fclose($handle);
                $image_name = $_FILES['fileToUpload']['name'];
                $image_type = $_FILES['fileToUpload']['type'];
                if (!$image_exists) {
                    $query = 'INSERT INTO images (img_name,img_type,img) VALUES (?, ?, ?)';
                    if ($stmt = $dbConnect->prepare($query)) {
                        if ($stmt->execute([$image_name, $image_type, $image])) {
                            $image_id = $dbConnect->lastInsertId();
                            $query = "UPDATE assessments SET image_id = ? WHERE grader_id = ? AND graded_id = ?";
                            if ($stmt = $dbConnect->prepare($query)) {
                                if ($stmt->execute([$image_id, $grader_id, $graded_id])) {
                                    $image_success = '<p>Record updated.</p>';
                                } else {
                                    $image_err = '<p>Error linking image to user</p>';
                                }
                            }
                        } else {
                            $image_err = '<p>Error linking image to user</p>';
                        }
                    }
                } else {
                    $pic_id = $_POST["image-exists"];
                    $query = 'UPDATE images SET img_name = ?, img_type = ?, img = ? WHERE pic_id = "' . $pic_id . '"';
                    if ($stmt = $dbConnect->prepare($query)) {
                        if ($stmt->execute([$image_name, $image_type, $image])) {
                            $image_success = '<p>Record updated.</p>';
                        } else {
                            $image_err = '<p>Error updating image for assessment</p>';
                        }
                    }
                }
            }
        }
        $sql = "";
        if ($finalize) {
            $sql = "UPDATE assessments SET grade = ?, justification = ?, finalized = 1 WHERE grader_id = ? AND graded_id = ?";
        } else if ($save) {
            $sql = "UPDATE assessments SET grade = ?, justification = ? WHERE grader_id = ? AND graded_id = ?";
        } else if ($delete) {
            $sql = "UPDATE assessments SET grade = ?, justification = ?, image_id = NULL WHERE grader_id = ? AND graded_id = ?";
            $justification = "";
            $grade = 0;
        }
        if ($stmt = $dbConnect->prepare($sql)) {
            if ($finalize) {
                if ($grade === 0 || $grade > 10) {
                    $grade_err = "Grade must be between 1 and 10 inclusive.";
                    return;
                }
            }
            if ($stmt->execute([$grade, $justification, $grader_id, $graded_id])) {
                if ($delete) {
                    $record_updated = "Information deleted for $graded_id.";
                } else {
                    if ($finalize) {
                        if ($db->finalizeGrade($graded_id)) {
                            $record_updated = "<p>Information has been successfully updated for $graded_id.</p>";
                        } else {
                            $record_updated = "<p>Update failed for student $graded_id.</p>";
                        }
                    } else {
                        $record_updated = "<p>Information has been successfully updated for $graded_id.</p>";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Peer review</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="custom.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <style>
        div.col-sm-8 {
            padding-left: 1.5rem;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php include("pageContent/navbar.php") ?>
    <div style="margin: 1rem auto;"></div>
    <?php if (!empty($image_err)) : ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error uploading image!</h4>
            <?php echo $image_err ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($graded_id_err) || !empty($justification_err) || !empty($grade_err)) : ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Input errors on last form submission</h4>
            <p><?php echo $graded_id_err ?> </p>
            <p><?php echo $justification_err ?> </p>
            <p><?php echo $grade_err ?> </p>
        </div>
    <?php endif; ?>
    <?php if (!empty($record_updated)) : ?>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Record updated!</h4>
            <?php echo $record_updated ?>
            <?php echo $image_success ?>
        </div>
    <?php endif; ?>
    <h1 class="display-5" style="text-align: center; padding-bottom: 1rem">Peer assessments group <?php echo $group_number ?></h1>
    <div class="jumbotron" style="display: flex; flex-direction: column; align-items: center;">
        <?php
        if (count($data) == 0) {
            echo '<div class="alert alert-danger" role="alert">
            No peers have registered yet!
          </div>';
        } else {
            foreach ($data as $value) : ?>
                <?php if ($value['finalized'] === 1) : ?>
                    <div class="col-12 col-sm-12 col-md-10 col-lg-6 assessment">
                        <h3 class="display-5">Assessment for <?php echo $value['graded_id'] ?></h3>
                        <div class="form-group row">
                            <label for="grade" class="col col-form-label">Grade:</label>
                            <div class="col-6 col-sm-8 col-md-8 col-lg-8">
                                <input type="text" readonly class="form-control-plaintext" id="grade" value="<?php echo $value["grade"]; ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="justification" class="col col-form-label">Justification:</label>
                            <div class="col-6 col-sm-8 col-md-8 col-lg-8">
                                <input type="text" readonly class="form-control-plaintext" id="justification" value="<?php echo $value["justification"]; ?>" />
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
                        <hr class="my-4">
                        <p class="text-danger">You already submitted the final grade. No more changes can be done.</p>
                    </div>
                <?php else : ?>
                    <form class="col-12 col-sm-12 col-md-10 col-lg-6 assessment" id="<?php echo $value["graded_id"] ?>" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <input type="hidden" name="graded_id" id="<?php echo $value["graded_id"] ?>" value="<?php echo $value['graded_id'] ?>">
                        <h3 class="display-5">Assessment for <?php echo $value['graded_id'] ?></h3>
                        <div class="form-group">
                            <label for="grade">Grade</label>
                            <select class="form-control" id="grade" name="grade">
                                <?php if ($value["grade"] == 0) echo "<option value='0' selected>Please select</option>";
                                            else echo "<option value='0'>Please select</option>";
                                            for ($x = 1; $x < 11; $x++) {
                                                if ($value["grade"] == $x) {
                                                    echo "<option value='$x' selected>$x</option>";
                                                } else {
                                                    echo "<option value='$x'>$x</option>";
                                                }
                                            } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="justification">Justification</label>
                            <textarea class="form-control" id="justification" name="justification" rows="3"><?php echo $value['justification'] ?></textarea>
                        </div>
                        <div class="form-group">
                            <?php if ($value['image_id'] === NULL) : ?>
                                <h2>No image added yet!</h2>
                            <?php else : ?>
                                <label>Image</label>
                                <div class="img-container">
                                    <?php echo $db->getImage($value['image_id']); ?>
                                </div>
                                <?php if ($value['finalized'] !== 1) : ?>
                                    <button id="<?php echo $value["graded_id"] ?>" role="button" class="btn btn-dark btn-md">Change Picture</button>
                                <?php endif; ?>
                                <input type="hidden" id="image-exists" name="image-exists" value="<?php echo $value['image_id']; ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="form-group" id="show<?php echo $value["graded_id"]; ?>" style="<?php if ($value['image_id'] !== NULL) echo 'display: none;' ?>">
                            <label for="fileToUpload">Select image to upload:</label>
                            <input class="form-control" type="file" name="fileToUpload" id="fileToUpload">
                        </div>

                        <div style="position:relative;">
                            <p>Click finish to finalize the grade.</p>
                            <input type="submit" class="btn btn-primary btn-lg" name="btnFinish" value="Finish" />
                            <input type="submit" class="btn btn-success btn-lg" name="btnSave" value="Save" />
                            <input type="submit" class="btn btn-danger btn-lg" style="position: absolute; right:.3rem;" name="btnDelete" value="Delete" />
                        </div>
                    </form>
                <?php endif; ?>
        <?php endforeach;
        } ?>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            $("button").on('click', function(event) {
                event.preventDefault();
                $("#show" + event.target.id).css("display", "block");
            });
        });
    </script>
    <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.html") ?>

</body>

</html>