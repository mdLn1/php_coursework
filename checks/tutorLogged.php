<?php if ($_SESSION["role"] == "tutor") {
    header("location: welcomeTutor.php");
    exit;
}
?>