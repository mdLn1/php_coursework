<?php 
if(isset($_SESSION["loggedin"]) && isset($_SESSION["role"])){
    if ($_SESSION["role"] == "student") {
        header("location: welcome.php");
        exit;
    } else if ($_SESSION["role"] == "tutor"){
        header("location: welcomeTutor.php");
        exit;
    }
}
?>