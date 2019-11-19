<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $subject = "";
        if(!isset($_POST["group_number"])){
            $group_err = "Group number not set.";
        }
        $group = $_POST["group_number"];
        if(empty($group_err)){
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
        if (isset($_POST["completedGrades"])) {
            $subject = "Final result";
            foreach ($data as $row) {
                $finalized_grade = $row["finalized"];
                $id = $row["ID"];
                $email = "";
                $body = "Dear Student $id, \n Your final grade is $finalized_grade.";
                if ($stmt = $db->query("SELECT email FROM users WHERE ID = $id")) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $email = $result["email"];
                    mail($email, $subject, $body, "From: cp3526m@gre.ac.uk\r\n");
                }
                
            }
        } else if (isset($_POST["reminderGrades"])) {
            $subject = "Peer evaluation reminder";
            foreach ($data as $row) {
                $id = $row["ID"];
                $email = "";
                $body = "Dear Student $id, \n This is a polite reminder to finalize your peers evaluation.";
                if ($stmt = $db->query("SELECT email FROM users WHERE ID = $id")) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $email = $result["email"];
                    
                    mail($email, $subject, $body, "From: cp3526m@gre.ac.uk\r\n");
                }
            }
        }
        }
    }
    
    ?>