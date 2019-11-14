<?php if ($_SESSION["role"] == "student") {
    header("location: welcome.php");
    exit;
}
?>