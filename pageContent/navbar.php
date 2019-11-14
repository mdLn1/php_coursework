<?php
$loggedIn = false;
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $loggedIn = true;
}
?>

<nav class="navbar navbar-toggleable-md navbar-light bg-info">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div style="text-align: right">
        <a class="navbar-brand text-light" style="display: inline-block;" href="welcome.php">Home</a>
        <?php if ($loggedIn) {
            echo '<a class="navbar-brand text-light" style="display: inline-block;" href="logout.php">Logout</a>';
        } else {
            echo '<a class="navbar-brand text-light" style="display: inline-block;" href="login.php">Login</a>';
        } ?>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item  text-light">
                <a class="nav-link text-light" href="welcome.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <?php if (!$loggedIn) {
                echo '<li class="nav-item">
                <a class="nav-link text-light" href="login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="register.php">Sign Up</a>
            </li>';
            } else {
                echo
                    '<li class="nav-item">
                <a class="nav-link text-light" href="logout.php">Logout</a>
            </li>';
            } ?>

        </ul>
    </div>
</nav>
<br />