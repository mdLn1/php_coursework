<?php
session_start();
include "checks/isLogged.php";
include "database.php";

// Define variables and initialize with empty values
$ID = $password = "";
$ID_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
  $db = new Database();
  // Check if ID is empty
  if (empty(trim($_POST["ID"]))) {
    $ID_err = "Please enter your ID.";
  } else {
    $ID = trim($_POST["ID"]);
      if (strlen($ID) !== 9) {
            $ID_err = "Your ID must have exactly 9 characters.";
        }
        if (preg_match('/[^0-9]/', $ID)) {
            $ID_err = "Your ID must be formed of digits only.";
        }
  }

  // Check if password is empty
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
  }


  // Validate credentials
  if (empty($ID_err) && empty($password_err)) {
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
          $password = "";
        $password_err = "The password you entered was not valid.";
      }
    }
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
          <input type="text" class="form-control" name="ID" id="id1" aria-describedby="IDHelp" required placeholder="Enter ID" value="<?php echo $ID ?>">
          <span class="error-input" id="id-error"><?php echo $ID_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
          <label for="exampleInputPassword1">Password</label>
          <input type="password" name="password" class="form-control" id="exampleInputPassword1" required placeholder="Password" value="<?php echo $password ?>">
          <span class="error-input"><?php echo $password_err; ?></span>
       
        <button type="submit" name="login" class="btn btn-primary" style="margin-top: .5rem; display: block;">Login</button>
      </form>
      <p>Don't have an account yet? <a href="register.php">Sign up here</a></p>
    </div>
  </div>
  <script>
    $(document).ready(function() {
     
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