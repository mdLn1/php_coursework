<?php
session_start();
include "checks/loggedIn.php";
include "checks/studentLogged.php";
include "functionality/errorResponse.php";
$data = [];
$group = 0;
$group_err = "";

if (!empty($_SERVER['QUERY_STRING'])) {
    include "database.php";
    parse_str($_SERVER['QUERY_STRING'], $queries);
    if (isset($queries["group"])) {
        $group = $queries["group"];
        if (preg_match('/[^1-9]/', $group) || $group < 1 || $group > 10) {
            $group_err = "Group number must be between 1 and 10 inclusive.";
        }
    } else {
        $group_err = "Group number must be between 1 and 10 inclusive.";
    }
    $db = new Database();
    if ($result = $db->dontWorryQuery("SELECT * FROM students WHERE group_number = $group")) {
        $data = $result;
    }
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $subject = "";
        if ($_POST["completedGrades"]) {
            $subject = "Final result";
            foreach ($data as $row) {
                $finalized_grade = $row["finalized"];
                $id = $row["ID"];
                $email = "";
                $body = "Dear Student $id, \n Your final grade is $finalized_grade.";
                if ($stmt = $db->query("SELECT email FROM users WHERE ID = $id")) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $email = $result["email"];
                }
                mail($email, $subject, $body, "From: cp3526m@gre.ac.uk\r\n");
            }
        } else if ($_POST["reminderGrades"]) {
            $subject = "Peer evaluation reminder";
            foreach ($data as $row) {
                $id = $row["ID"];
                $email = "";
                $body = "Dear Student $id, \n This is a polite reminder to finalize your peers evaluation.";
                if ($stmt = $db->query("SELECT email FROM users WHERE ID = $id")) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $email = $result["email"];
                }
                mail($email, $subject, $body, "From: cp3526m@gre.ac.uk\r\n");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Group view</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="custom.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <style>
        .content-wrapper {
            margin: 0 auto;
            max-width: 50rem;
            width: 80%;
        }
    </style>
</head>

<body>
    <?php include("pageContent/navbar.php") ?>
    <div style="padding: 1rem;">
        <div id="container-table">
            <h2 style="margin-bottom: 1rem;">Group <?php echo $group; ?> Members</h2>
            <br />
            <?php if (count($data) > 0) {
                $finalize = 0;
                $times = 0; ?>
                <div class="content-wrapper">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Student's ID</th>
                                <th scope="col">Final Grade</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php foreach ($data as $row) { ?>
                                <tr>
                                    <th><?php echo ++$times; ?></th>
                                    <td><a class="stdid" href="studentRecord.php?studentid=<?php echo $row["ID"]; ?>"><?php echo $row["ID"]; ?></a></td>
                                    <td><?php if (isset($row["finalized_grade"])) {
                                                    $finalize++;
                                                    echo $row["finalized_grade"];
                                                } else {
                                                    echo "Not available";
                                                } ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>


                    <?php if ($finalize === 3) { ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">This group has finished the peer evaluation.</h4>
                                <button type="submit" name="completedGrades" class="btn btn-success">Send group evaluation</button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">This group has not finished the peer evaluation.</h4>
                                <button type="submit" name="completedGrades" class="btn btn-success">Send group reminder</button>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            <?php } else { ?>

                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">This group has no students registered</h4>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if (!(isset($_COOKIE["CookiesAccepted"]) && $_COOKIE["CookiesAccepted"] === "yes")) include("pageContent/cookieAlert.html") ?>
</body>

</html>