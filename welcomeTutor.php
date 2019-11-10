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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <style>
        .content-wrapper {
            margin: 0 auto;
            max-width: 30em;
            width: 80%;
        }

        #container-table {
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

        .dropbtn {
            color: white;
            border: none;
            cursor: pointer;
        }

        /* The container <input> - needed to position the dropdown content */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown Content (Hidden by Default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 140px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        /* Links inside the dropdown */
        .dropdown-content span {
            color: black;
            cursor: pointer;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content span:hover {
            background-color: #111111;
            color: white;
        }

        /* Show the dropdown menu on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        i {
            border: solid white;
            border-width: 0 3px 3px 0;
            display: inline-block;
            padding: 3px;
            margin-top: -13px;
            margin-left: .5rem;
        }

        .down {
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }
    </style>
</head>

<body>
    <?php include("navbarTutor.php") ?>
    <div style="position: relative; margin-bottom: 1rem; padding-bottom: 1rem;">
        <form class="form-inline my-2 my-lg-0" style="position: absolute; right: 0; top: 0;">
            <div class="dropdown">
                <button class="dropbtn btn btn-secondary">Search By: <span id="search-criteria">ID</span> <i class="arrow down"></i></button>
                <div class="dropdown-content">
                    <span class="search-option">ID</span>
                    <span class="search-option">Group</span>
                    <span class="search-option">Grade</span>
                </div>
            </div>
            <input class="form-control mr-sm-2" type="text" style="padding-left: 1rem; margin-left: 1rem;" placeholder="Search">
            <input type="submit" class="btn btn-success" value="Search" />
            </input>
        </form>
    </div>

    <div id="container-table">
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
    </div>

    <script>
        $(document).ready(function() {
            $(".stdid").click(function() {
                window.location = 'studentRecord.php?studentid=' + $(this).text().trim();
            });
            $(".search-option").on("click", function(event) {
                event.preventDefault();
                $("#search-criteria").html(event.target.innerHTML);
            })
        });
    </script>
</body>

</html>