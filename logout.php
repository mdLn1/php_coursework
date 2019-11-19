<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();
setcookie('UserId', '', time() - 3600);
setcookie('LastSearch', '', time() - 3600);

// Destroy the session.
session_destroy();
?>
<!DOCTYPE html>
<html>

<head>
  <title>Logout</title>
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
  <div class="alert alert-success" role="alert">
    <h4 class="alert-heading">You have successfully logged out!</h4>
    <p>Please head in to the <a class="navbar-brand" href="login.php">Login</a>page in order to login again.</p>
  </div>
  <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.php") ?>
</body>

</html>