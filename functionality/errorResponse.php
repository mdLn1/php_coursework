<?php
function responseError($message, $status = 500, $statusMessage = "Internal Server Error")
{
    header("HTTP/1.1 $status $statusMessage");
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => $message)));
}
?>