<?php
include "database.php";

$db = new Database();

$groups = $db->getUserDetails('000111222');

if ($db->getUserDetails('000111222')) {
    $ID_err = "This ID is already used by another student.";
    echo "hallo";}
    else echo "hallo2";
?>