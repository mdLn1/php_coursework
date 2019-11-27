<?php // Define variables and initialize with empty values
session_start();

$ID = $confirm_captcha = $group = $email = $password = $confirm_password = "";
$ID_err = $confirm_captcha_err = $group_err = $email_err = $password_err = $confirm_password_err = "";
$registration_error = "";
$accountCreated = false;
require "functionality/createCaptcha.php";
include "database.php";
$groups = [];
$db = new Database();
$groups = $db->findGroupsAvailable();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correctCaptcha = $_SESSION["captcha"];

    if (empty(trim($_POST["ID"]))) {
        $ID_err = "Please enter an ID number.";
    } else {
        $ID = trim($_POST["ID"]);
        if (strlen($ID) !== 9) {
            $ID_err = "Your ID must have exactly 9 characters.";
        }
        if (preg_match('/[^0-9]/', $ID)) {
            $ID_err = "Your ID must be formed of digits only.";
        }
        if ($db->getUserDetails($ID)) {
            $ID_err = "This ID is already used by another student.";
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
        if (!preg_match('/^([\w\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/', $email)) {
            $email_err = "The email address given is invalid";
        }
    }

    if (empty(trim($_POST["group"]))) {
        $group_err = "Please enter an group.";
    } else {
        $group = trim($_POST["group"]);
        if (preg_match('/[^1-9]/', $group)) {
            $group_err = "Group must be a digit between 1 and 10 inclusive.";
        }
        if ((int) $group > 10 || (int) $group < 0) {
            $group_err = "Group must be a digit between 1 and 10 inclusive.";
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

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

    if (
        empty($ID_err) && empty($email_err) && empty($group_err)
        && empty($confirm_captcha_err) && empty($password_err) && empty($confirm_password_err)
    ) {
        if ($db->addStudent($ID, $email, $password, $group)) {
            $accountCreated = true;
            $ID = $confirm_captcha = $group = $email = $password = $confirm_password = "";
            $ID_err = $confirm_captcha_err = $group_err = $email_err = $password_err = $confirm_password_err = "";
        } else {
            $password= $confirm_captcha = "";
            $registration_error = "An error occurred during the account creation process, please try again!";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="custom.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>
</head>

<body>
    <?php include("pageContent/navbar.php") ?>
    <?php if ($accountCreated) : ?>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Account Successfully created!</h4>
            <p>Please head in to the <a class="navbar-brand" href="login.php">Login</a>page in order to login.</p>
        </div>
    <?php endif; ?>
    <?php if (!empty($registration_error)) : ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error while creating account</h4>
            <p><?php echo $registration_error; ?></p>
        </div>
    <?php endif; ?>
    <div style="padding:1rem;">
        <div class="form-container">
            <h2>Register</h2>
            <p>Please fill this form to create an account.</p>
            <form method="post" id="signupForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group <?php echo (!empty($ID_err)) ? 'has-error' : ''; ?>">
                    <label for="ID">ID</label>
                    <input type="text" class="form-control" name="ID" id="id1" aria-describedby="IDHelp" required placeholder="Enter ID" value="<?php echo $ID; ?>">
                    <span class="error-input" id="id-error"><?php echo $ID_err; ?></span>
                </div>

                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" aria-describedby="email" required placeholder="Enter your email address" value="<?php echo $email; ?>">
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
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" required placeholder="Password" value="<?php echo $password ?>">
                    <span class="error-input"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label for="exampleInputPassword2">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" id="exampleInputPassword2" required placeholder="Confirm Password" value="<?php echo $confirm_password ?>">
                    <span class="error-input"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_captcha_err)) ? 'has-error' : ''; ?>">
                    <img id="captcha-image" alt="captcha image" />
                    <input type="button" id="captcha-button" value="Regenerate" />
                    <input type="text" name="confirm_captcha" class="form-control" id="confirm_captcha" required placeholder="Confirm Captcha" value="<?php echo $confirm_captcha ?>">
                    <span class="error-input"><?php echo $confirm_captcha_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Sign up</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function doCaptcha(){
                $.ajax({
                    url: 'functionality/createCaptcha.php',
                    data: {
                        action: 'regenerate'
                    },
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(output) {
                        $("#captcha-image").attr("src", "data:image/jpeg;base64," + output.imageCode)
                    }
                })
            }
            $("#captcha-button").on('click', function(event) {
                event.preventDefault();
                doCaptcha();
            });
            $("#id1").on("keyup paste", function(event) {
                let re = /[^0-9]/;
                if (re.test(event.target.value)) {
                    el = event.target.value.match(/[0-9]+/);
                    if (el !== null) {
                        $(this).val(el[0]);
                        
                    } else {
                        $(this).val("");
                    }
                    $(this).css("border", "3px solid red");
                        $("#id-error").css("display", "block");
                    $("#id-error").css("color", "red");
                    $("#id-error").text("An ID must contain only digits.");
                        setTimeout(function() {
                            $("#id-error").css("display", "none");
                            $("#id1").css("border", "0");
                        }, 5000);
                }
            });
            doCaptcha();
        });
    </script>
    <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.html") ?>

</body>

</html>