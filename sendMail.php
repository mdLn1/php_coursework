<?php
session_start();
include "checks/loggedIn.php";
include "checks/studentLogged.php";
include "functionality/errorResponse.php";

$data = [];
$response = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "database.php";
    $subject = "";
    if (!isset($_POST["group_number"])) {
        responseError("Group number not set.");
    }
    $group = $_POST["group_number"];
    
    if (preg_match('/[^1-9]/', $group) || $group < 1 || $group > 10) {
        responseError("Group number must be between 1 and 10 inclusive.");
    }
    $db = new Database();
    if ($result = $db->dontWorryQuery("SELECT * FROM students WHERE group_number = $group")) {
        $data = $result;
    }
    $dbConnect = $db->getDbConnection();
    $response["group"] = $group;
    $sentTo = [];
    if (isset($_POST["completedGrades"])) {
        $subject = "Final result";
        
        foreach ($data as $row) {
            $finalized_grade = $row["finalized"];
            $id = $row["ID"];
            $email = "";
            
            $body = "Dear Student $id, \n Your final grade is $finalized_grade.";
            if ($stmt = $dbConnect->query("SELECT email FROM users WHERE ID = $id")) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $email = $result["email"];
                // mail($email, $subject, $body, "From: cp3526m@gre.ac.uk\r\n");
                array_push($sentTo, $email);
            }
        }
        array_push($sentTo, $email);
    } else if (isset($_POST["reminderGrades"])) {
        $subject = "Peer evaluation reminder";
        foreach ($data as $row) {
            $id = $row["ID"];
            $email = "";
            $body = "Dear Student $id, \n This is a polite reminder to finalize your peers evaluation.";
            if ($stmt = $dbConnect->query("SELECT email FROM users WHERE ID = $id")) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $email = $result["email"];
                array_push($sentTo, $email);
                // mail($email, $subject, $body, "From: cp3526m@gre.ac.uk\r\n");
            }
        }
        
    }
    $response["sentTo"] = $sentTo;
    echo json_encode($response);
}
