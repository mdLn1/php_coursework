<?php // Define variables and initialize with empty values
session_start();

$ID = $confirm_captcha = $group = $email = $password = $confirm_password = "";
$ID_err = $confirm_captcha_err = $group_err = $email_err = $password_err = $confirm_password_err = "";
$accountCreated = false;
include "checks/databaseConnection.php";
require "functionality/createCaptcha.php";
$groups = [];

function findGroupsAvailable($sqliDb)
{
    $sql = "SELECT COUNT(ID), group_number
        FROM students
        GROUP BY group_number
        ORDER BY COUNT(ID) ASC";
    $result = $sqliDb->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            if ($row['COUNT(ID)'] == 3) {
                $data[] = $row;
            }
        }
        return $data;
    }
    return [];
}
function findStudentsFromGroup($sqliDb, $number, $id)
{
    $sql = "SELECT ID FROM students WHERE group_number = $number";
    $result = $sqliDb->query($sql);
    // output data of each row
    if ($result->num_rows > 1) {
        while ($row = $result->fetch_assoc()) {
            if ($row['ID'] != $id) {
                $data[] = $row;
            }
        }
        return $data;
    }
    return [];
}
$groups = findGroupsAvailable($mysqli);
$availableGroups = [];
if (!empty($groups)) {
    for ($i = 1; $i < 11; $i++) {
        if (in_array($i, array_column($groups, 'group_number'))) {
            continue;
        }
        array_push($availableGroups, $i);
    }
} else {
    $availableGroups = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
}
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    $correctCaptcha = $_SESSION["captcha"];
    if (empty(trim($_POST["ID"]))) {
        $ID_err = "Please enter an ID number.";
    } else {
        // Prepare a select statement
        $sql = "SELECT ID FROM users WHERE ID = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_ID);

            // Set parameters
            $param_ID = trim($_POST["ID"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $ID_err = "This ID is already used by another student.";
                } else {
                    $ID = trim($_POST["ID"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }


    // Validate email
    include "checks/checkEmail.php";

    // Validate email
    if (empty(trim($_POST["group"]))) {
        $group_err = "Please enter an group.";
    } else {
        $group = trim($_POST["group"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty(trim($_POST["confirm_captcha"]))) {
        $confirm_captcha_err = "Captcha string does not match";
        $confirm_captcha = "";
    } else {
        if (trim($_POST["confirm_captcha"]) != $correctCaptcha) {
            $confirm_captcha_err = "Captcha string does not match";
            $confirm_captcha = "";
        } else {
            $confirm_captcha = trim($_POST["confirm_captcha"]);
        }
    }

    // Check input errors before inserting in database
    if (empty($ID_err) && empty($email_err) && empty($group_err) && empty($confirm_captcha_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (ID, email, pass) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_ID, $param_email, $param_pass);

            // Set parameters
            $param_ID = $ID;
            $param_email = $email;
            $param_pass = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $sql = "INSERT INTO students (ID, group_number) VALUES (?, ?)";
                if ($stmt = $mysqli->prepare($sql)) {
                    $stmt->bind_param("ss", $param_ID, $param_group);
                    $param_ID = $ID;
                    $param_group = $group;
                    if ($stmt->execute()) {
                        $accountCreated = true;
                        $others = findStudentsFromGroup($mysqli, $group, $ID);
                        if (count($others) > 0) {
                            foreach ($others as $student) {
                                $colleagueID = $student["ID"];
                                $sql = "INSERT INTO assessments (grader_id, graded_id) VALUES ($colleagueID, $ID)";
                                $mysqli->query($sql);
                                $sql = "INSERT INTO assessments (grader_id, graded_id) VALUES ($ID, $colleagueID)";
                                $mysqli->query($sql);
                            }
                        }
                        $ID = $confirm_captcha = $group = $email = $password = $confirm_password = "";
                        $ID_err = $confirm_captcha_err = $group_err = $email_err = $password_err = $confirm_password_err = "";
                    }
                }
                // Redirect to login pages
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Register</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <style>
        img#captcha-image {
            margin-bottom: 1rem;
        }

        #captcha-button {
            padding: 1rem;
            border-radius: .5rem;
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>

<body>
    <?php include("pageContent/navbar.php") ?>
    <?php if ($accountCreated) : ?>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Account Successfully created!</h4>
            <p>Please head in to the <a class="navbar-brand" href="login.php">Login</a> page in order to login.</p>
        </div>
    <?php endif; ?>
    <div class="form-container">
        <h2>Register</h2>
        <p>Please fill this form to create an account.</p>
        <form method="post" id="signupForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group  <?php echo (!empty($ID_err)) ? 'has-error' : ''; ?>">
                <label for="id1">ID</label>
                <input type="text" class="form-control" name="ID" id="id1" aria-describedby="IDHelp" placeholder="Enter ID" value="<?php echo $ID ?>">
                <span class="error-input"><?php echo $ID_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" aria-describedby="email" placeholder="Enter your email address" value="<?php echo $email; ?>">
                <span class="error-input"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($group_err)) ? 'has-error' : ''; ?>">
                <label for="group">Available Groups</label>
                <select class="form-control" name="group" id="group" aria-describedby="group" value="<?php echo $group ?>">
                    <?php
                    foreach ($availableGroups as $value) : ?>
                        <option value="<?php echo $value ?>"><?php echo $value ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-input"><?php echo $group_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?php echo $password ?>">
                <span class="error-input"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label for="exampleInputPassword2">Password</label>
                <input type="password" name="confirm_password" class="form-control" id="exampleInputPassword2" placeholder="Confirm Password" value="<?php echo $confirm_password ?>">
                <span class="error-input"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_captcha_err)) ? 'has-error' : ''; ?>">
                <img id="captcha-image" src='data:image/jpeg;base64,<?php $captchaImg = makeImgCaptcha();
                                                                    echo $captchaImg; ?>' alt="captcha image" />
                <input type="button" id="captcha-button" value="Regenerate" />
                <?php echo $_SESSION["captcha"]; ?>
                <input type="text" name="confirm_captcha" class="form-control" id="confirm_captcha" placeholder="Confirm Captcha" value="<?php echo $confirm_captcha ?>">
                <span class="error-input"><?php echo $confirm_captcha_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Sign up</button>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#captcha-button").on('click', function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'createCaptcha.php',
                    data: {
                        action: 'regenerate'
                    },
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(output) {
                        $("#captcha-image").attr("src", "data:image/jpeg;base64," + output.imageCode)
                    }
                })
            })
        });
    </script>
</body>

</html>