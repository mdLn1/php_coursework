<?php
 $host = "mysql.cms.gre.ac.uk";
 $database = "mdb_cp3526m";
 $username = "cp3526m";
 $password = "Peer21";

// Create connection
$mysqli = new mysqli($host, $username, $password, $database);

if (mysqli_connect_errno($conn)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	exit();
}
if ($result = mysqli_query($conn, "SELECT * FROM table")) {
	printf("Select returned %d rows.\n", mysqli_num_rows($result));
	echo "Result set order...";
	$result->data_seek(0);
	while ($row = $result->fetch_assoc()) {
		echo $row['field1'].' '.$row['field2'];
	}
	mysqli_free_result($result);
}

echo "Connected successfully";

mysqli_close($conn);
?>