<?php
session_start();
$data = array();
require "classes/oldConnect.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
if ($_SESSION["role"] == "student") {
    header("location: welcome.php");
    exit;
}

if ($_SESSION["role"] == "tutor") {
    $sql = "SELECT * FROM students ORDER BY group_number ASC";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        echo "0 results";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tutor students checks</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <style>
        .content-wrapper {
            margin: 0 auto;
            max-width: 30em;
            width: 80%;
        }

        body {
            text-align: center;
        }

        tr {
            font-size: 1.5rem;
            background-color: #c8cbcf;
            color: black;
        }

        tbody tr:hover {
            background-color: white;
            color: #007bff;
        }

        td:hover {

            cursor: pointer;
            text-decoration: underline;
        }

        td:hover .cont::before {
            content: '\00bb';
        }
    </style>
</head>

<body>
    <?php include("navbarTutor.php") ?>
    <h1 style="margin-bottom: 1rem;">Students</h1>
    <div class="content-wrapper">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Student's ID</th>
                    <th scope="col">Group Number</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1;
                foreach ($data as $value) : ?>
                    <tr>
                        <th scope="row"><?php echo $count ?></th>
                        <td><span class="cont"></span><span class="stdid"><?php echo $value["ID"] ?></span></td>
                        <td><span class="cont"></span><?php echo $value["group_number"] ?></td>
                    </tr>
                    <?php $count++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/Bootstrap_tutorial.js"></script>
    <script>
        $(document).ready(function() {
            $(".stdid").click(function() {
                window.location = 'studentRecord.php?studentid=' + $(this).text().trim();
            });
        });
    </script>
</body>

</html>