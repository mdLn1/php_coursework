<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: welcome.php");
  exit;
}

// Include config file
require "classes/oldConnect.php";

// Define variables and initialize with empty values
$ID = $password = "";
$ID_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Check if ID is empty
  if (empty(trim($_POST["ID"]))) {
    $ID_err = "Please enter ID.";
  } else {
    $ID = trim($_POST["ID"]);
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
    $sql = "SELECT ID, pass FROM users WHERE ID = ?";

    if ($stmt = $mysqli->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bind_param("s", $param_ID);

      // Set parameters
      $param_ID = $ID;

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Store result
        $stmt->store_result();

        // Check if ID exists, if yes then verify password
        if ($stmt->num_rows == 1) {
          // Bind result variables
          $stmt->bind_result($ID, $hashed_password);
          if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
              // Password is correct, so start a new session
              session_start();

              // Store data in session variables
              $_SESSION["loggedin"] = true;
              $_SESSION["ID"] = $ID;

              // Redirect user to welcome page
              header("location: welcome.php");
            } else {
              // Display an error message if password is not valid
              $password_err = "The password you entered was not valid.";
            }
          }
        } else {
          // Display an error message if ID doesn't exist
          $ID_err = "No account found with that ID.";
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
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
  <title>Home</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/custom.css">
</head>

<body>
  <?php include("navbar.php") ?>

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
      <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p>Don't have an account yet? <a href="register.php">Sign up here</a></p>
  </div>

  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
  <script src="js/tether.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <script src="js/ie10-viewport-bug-workaround.js"></script>
  <script src="js/Bootstrap_tutorial.js"></script>
</body>

</html>