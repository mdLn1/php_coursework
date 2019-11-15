<?php
session_start();

include "checks/loggedIn.php";
include "checks/tutorLogged.php";
include "functionality/fetchImage.php";
$data = array();
$group_number = $image_err = $image_success = $record_updated = "";
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

    if (empty(trim($_POST["justification"]))) {
        $justification = trim($_POST["justification"]);
    }
    if (empty(trim($_POST["graded_id"]))) {
        $graded_id = trim($_POST["graded_id"]);
    }
    if (empty(trim($_POST["grader_id"]))) {
        $grader_id = trim($_POST["grader_id"]);
    }
    if (empty(trim($_POST["grade"]))) {
        $grade= (int)trim($_POST["grade"]);
    }

    if (!empty($_FILES["fileToUpload"]["size"])) {
        if (!preg_match('/gif|png|x-png|jpeg/', $_FILES['fileToUpload']['type'])) {
            $image_err = '<p>Only browser compatible images allowed</p>';
        } else if ($_FILES['fileToUpload']['size'] > 100000) {
            $image_err = '<p>Sorry file too large</p>';
            // Connect to database
        } else if (!($handle = fopen($_FILES['fileToUpload']['tmp_name'], "r"))) {
            $image_err = '<p>Error opening temp file</p>';
        } else if (!($image = fread($handle, filesize($_FILES['fileToUpload']['tmp_name'])))) {
            $image_err = '<p>Error reading temp file</p>';
        } else {
            fclose($handle);
            // Commit image to the database
            $image_name = $_FILES['fileToUpload']['name'];
            $image_type = $_FILES['fileToUpload']['type'];
            if (!(isset($_POST["image-exists"]))) {
                $query = 'INSERT INTO images (img_name,img_type,img) VALUES ("' . $image_name . '","' . $image_type  . '","' . $image . '")';
            } else {
                $pic_id = $_POST["image-exists"];
                $query = 'UPDATE images SET img_name = "' . $image_name . '", img_type = "' . $image_type  . '", img = "' . $image . '" WHERE pic_id = "' . $pic_id . '"';
            }
            $result = [];
            if (!($result = $dbConnect->query($query))) {
                $image_err = '<p>Error writing image to database</p>';
            } else {
                if (!(isset($_POST["image-exists"]))) {
                    $image_id = $result->lastInsertId();
                    $query = "UPDATE assessments SET image_id = ? WHERE grader_id = ? AND graded_id = ?";
                    if ($stmt = $dbConnect->prepare($query)) {
                        if ($stmt->execute([$image_id, $_SESSION["ID"], $graded_id])) {
                            $image_success = '<p>Record updated.</p>';
                        } else {
                            $image_err = '<p>Error linking image to user</p>';
                        }
                    }
                } else {
                    $image_success .= '<p>Image successfully copied to database</p>';
                }
            }
        }
    }
    $sql = "";
    if (isset($_POST['btnFinish'])) {
        $sql = "UPDATE assessments SET grade = ?, justification = ?, finalized = 1 WHERE grader_id = ? AND graded_id = ?";
    } else if (isset($_POST['btnSave'])) {
        $sql = "UPDATE assessments SET grade = ?, justification = ? WHERE grader_id = ? AND graded_id = ?";
    } else if (isset($_POST['btnDelete'])) {
        $sql = "UPDATE assessments SET grade = '', justification = '', image_id = NULL WHERE grader_id = ? AND graded_id = ?";
    }
    if ($stmt = $dbConnect->prepare($sql)) {

        if (!isset($_POST['btnDelete'])) {
            if($stmt->execute([0, "", $grader_id, $graded_id])){
                $record_updated = "<p>Information has been deleted for $param_graded_id.</p>";
            }
        } else {
            if($stmt->execute([$grader_id, $graded_id])){
                $record_updated = "<p>Information has been successfully updated for $param_graded_id.</p>";
            }
        }
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
    <?php include("pageContent/navbar.php") ?>
    <?php if (!empty($image_err)) : ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error uploading image!</h4>
            <?php echo $image_err ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($record_updated)) : ?>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Record updated!</h4>
            <?php echo $record_updated ?>
            <?php echo $image_success ?>
        </div>
    <?php endif; ?>
    <h1 class="display-3" style="text-align: center; padding-bottom: 2rem">Peer assessments group <?php echo $group_number ?></h1>
    <div class="jumbotron">
        <?php
        if (count($data) == 0) {
            echo '<div class="alert alert-danger" role="alert">
            No peers have registered yet!
          </div>';
        } else {
            foreach ($data as $value) : ?>
                <form class="col-sm-6" id="<?php echo $value["graded_id"] ?>" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                            <div class="img-container">
                                <?php echo getImage($mysqli, $value['image_id']); ?>
                            </div>
                            <button id="<?php echo $value["graded_id"] ?>" role="button" class="btn btn-dark btn-md">Change Picture</button>
                            <input type="hidden" id="image-exists" name="image-exists" value="<?php echo $value['image_id']; ?>" />
                        <?php endif; ?>
                    </div>
                    <div class="form-group" id="show<?php echo $value["graded_id"]; ?>" style="<?php if ($value['image_id'] !== NULL) echo 'display: none;' ?>">
                        <label for="fileToUpload">Select image to upload:</label>
                        <input class="form-control" type="file" name="fileToUpload" id="fileToUpload">
                    </div>
                    <hr class="my-4">
                    <?php if ($value['finalized'] == 1) : ?>
                        <p class="text-danger">You already submitted the final grade. No more changes can be done.</p>
                    <?php else : ?>
                        <div style="position:relative;">
                            <p>Click finish to finalize the grade.</p>
                            <input type="submit" class="btn btn-primary btn-lg" name="btnFinish" value="Finish" />
                            <input type="submit" class="btn btn-success btn-lg" name="btnSave" value="Save" />
                            <input type="submit" class="btn btn-danger btn-lg" style="position: absolute; right:.3rem;" name="btnDelete" value="Delete" />
                        </div>
                    <?php endif; ?>
                </form>
        <?php endforeach;
        } ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $("button").on('click', function(event) {
                event.preventDefault();
                $("#show" + event.target.id).css("display", "block");
            });
        });
    </script>
</body>

</html>