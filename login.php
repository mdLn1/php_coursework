<?php
session_start();
include "functionality/createCaptcha.php";
include "checks/isLogged.php";
include "database.php";

// Define variables and initialize with empty values
$ID = $password = $confirm_captcha = "";
$ID_err = $password_err = $confirm_captcha_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
  $db = new Database();
  $correctCaptcha = $_SESSION["captcha"];
  // Check if ID is empty
  if (empty(trim($_POST["ID"]))) {
    $ID_err = "Please enter your ID.";
  } else {
    $ID = trim($_POST["ID"]);
  }

  // Check if password is empty
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
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

  // Validate credentials
  if (empty($ID_err) && empty($password_err) && empty($confirm_captcha_err)) {
    // Prepare a select statement
    if ($result = $db->getUserDetails($ID)) {

      if (password_verify($password, $result["pass"])) {
        // Password is correct, so start a new session
        session_start();
        // Store data in session variables
        $_SESSION["loggedin"] = true;
        $id = $_COOKIE['UserId'] .  $ID;
        setcookie('UserId', $id);
        $_SESSION["ID"] = $ID;
        $_SESSION["role"] = $result["role"];

        // Redirect user to welcome page
        if ($result["role"] == "student") {
          header("location: welcome.php");
        } else {
          header("location: welcomeTutor.php");
        }
      } else {
        // Display an error message if password is not valid
        $password_err = "The password you entered was not valid.";
      }
    }
  } else {
    echo "Oops! Something went wrong. Please try again later.";
  }
}
?>
<!DOCTYPE html>
<html>


<head>
  <title>Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link href="custom.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="bootstrap.min.js"></script>
</head>

<body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <?php include("pageContent/navbar.php") ?>
  <div style="padding:1rem;">
    <div class="form-container">
      <h2>Login</h2>
      <p>Please fill this form to login.</p>
      <form method="post" id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group  <?php echo (!empty($ID_err)) ? 'has-error' : ''; ?>">
          <label for="id1">ID</label>
          <input type="text" class="form-control" name="ID" id="id1" aria-describedby="IDHelp" placeholder="Enter ID" value="<?php echo $ID ?>">
          <span class="error-input"><?php echo $ID_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
          <label for="exampleInputPassword1">Password</label>
          <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?php echo $password ?>">
          <span class="error-input"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($confirm_captcha_err)) ? 'has-error' : ''; ?>">
          <img id="captcha-image" src='data:image/jpeg;base64,<?php $captchaImg = makeImgCaptcha();
                                                              echo $captchaImg; ?>' alt="captcha image" />
          <input type="button" id="captcha-button" value="Regenerate" />
          <?php echo $_SESSION["captcha"]; ?>
          <input type="text" name="confirm_captcha" class="form-control" id="confirm_captcha" placeholder="Confirm Captcha" value="<?php echo $confirm_captcha ?>">
          <span class="error-input"><?php echo $confirm_captcha_err; ?></span>
        </div>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
      </form>
      <p>Don't have an account yet? <a href="register.php">Sign up here</a></p>
    </div>
  </div>
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
            // console.log(output.imageCode);
            $("#captcha-image").attr("src", "data:image/jpeg;base64," + output.imageCode)
          }
        })
      })
    });
  </script>
  <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.html") ?>
</body>

</html>