<?php
$servername = "mysql.cms.gre.ac.uk";
$username = "cp3526m";
$password = "Peer21";
$db = "mdb_cp3526m";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>