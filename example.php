<?php 
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"])) {
    echo json_encode(array("greeting"=>"hello1"));
} else {
    echo json_encode(array("greeting"=>"hello"));
}
 ?>