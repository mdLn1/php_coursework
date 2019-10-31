<?php
 $host = "localhost";
 $database = "phpcw";
 $username = "root";
 $password = "";

// Create connection
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully";
?>