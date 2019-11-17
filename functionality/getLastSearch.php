<?php
if (!empty($_COOKIE['LastSearch']) && $_SERVER["REQUEST_METHOD"] === "GET") {
   echo json_encode(["cookie" => $_COOKIE['LastSearch']]);
} else {
    echo json_encode(array("cookie" => "empty"));
}
?>