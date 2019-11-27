<?php
$loggedIn = false;
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $loggedIn = true;
}
?>

<nav class="navbar navbar-toggleable-md navbar-light bg-info">
    <?php if ($_SESSION["role"] === "tutor") { ?>
    <div class="dropdown" id="group-drop">
                <button class="dropbtn btn">Groups <i class="arrow down"></i></button>
                <div class="dropdown-content" style="z-index: 2;">
                    <span class="group-option">1</span>
                    <span class="group-option">2</span>
                    <span class="group-option">3</span>
                    <span class="group-option">4</span>
                    <span class="group-option">5</span>
                    <span class="group-option">6</span>
                    <span class="group-option">7</span>
                    <span class="group-option">8</span>
                    <span class="group-option">9</span>
                    <span class="group-option">10</span>
                </div>
            </div>
    <?php } ?>
    <div></div>
    <div style="text-align: right">
        <?php if ($loggedIn) {
    
            echo '<a class="navbar-brand text-light" style="display: inline-block;" href="welcome.php">Home</a><a class="navbar-brand text-light" style="display: inline-block;" href="logout.php">Logout</a>';
        } else {
            echo '<a class="navbar-brand text-light" style="display: inline-block;" href="login.php">Login</a><a class="navbar-brand text-light" style="display: inline-block;" href="register.php">Register</a>';
        } ?>
    </div>
   
</nav>
<br />

