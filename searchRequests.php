<?php
session_start();
// include "checks/databaseConnection.php";
include "checks/loggedIn.php";
include "checks/studentLogged.php";
include "functionality/errorResponse.php";
include "database.php";

define('URLFORM', 'https://stuweb.cms.gre.ac.uk/~cp3526m/coursework/welcomeTutor.php');
$response = array();
$queries = array();
$data = [];
$total_pages = $resultsFound = $offset = 0;
$pageNumber = 1;
$records_page = 5;
// if(!isset($_SERVER['HTTP_REFERER'])){
//     responseError("Invalid origin request", 403, "Forbidden");
// } else if ($_SERVER['HTTP_REFERER'] != URLFORM){
//     responseError("Invalid origin request", 403, "Forbidden");
// }


if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_SERVER['QUERY_STRING'])) {
    $db = new Database();
    $sqlCount = $sqlSearch = "";
    parse_str($_SERVER['QUERY_STRING'], $queries);
    $pageNumber = 1;
    if (!isset($_GET["pageNo"])) {
        responseError("Page number must be a number greater than 0");
    }
    $pageNumber = $_GET["pageNo"];
    if (preg_match('/[^0-9]/', $pageNumber)) {
        responseError("Page number must be a number greater than 0");
    }
    $pageNumber = (int)$pageNumber;
    $offset = ($pageNumber - 1) * $records_page;
    if (isset($queries['studentId'])) {
        $studentid = $queries['studentId'];
        if (preg_match('/[^0-9]/', $studentid)) {
            responseError("Student id must be formed of digits only.", 400, "Bad Request");
        }
        setCookie("LastSearch", $studentid);
        $sqlCount = 'SELECT COUNT(*) FROM students WHERE ID REGEXP "' . $studentid . '" ORDER BY ID';
        $sqlSearch = 'SELECT * FROM students WHERE ID REGEXP "' . $studentid . '" ORDER BY ID LIMIT ' . $offset . ',' . $records_page . '';
    } else if (isset($queries['grade'])) {
        $grade = $queries['grade'];
        if (preg_match('/[^1-9]/', $grade)) {
            responseError("Grade must be a digit between 1 and 9 inclusive.", 400, "Bad Request");
        }
        if (!((int) $grade > 0 && (int) $grade < 10)) {
            responseError("Grade must be a digit between 1 and 9 inclusive.", 400, "Bad Request");
        }
        $filter = "";
        $sorting = "";
        if (isset($queries['filter']) && isset($queries['sort'])) {
            $filter = trim($queries['filter']);
            $sorting = trim($queries['sort']);
            if ($filter !== "over") {
                $filter = "<=";
            } else {
                $filter = ">=";
            }
            if ($sorting !== "ascending") {
                $sorting = "DESC";
            } else {
                $sorting = "ASC";
            }
        } else {
            responseError("Filter and sorting must be set when searching for a grade.", 400, "Bad Request");
        }
        setCookie("LastSearch", $grade);
        $sqlCount = "SELECT COUNT(*) FROM students WHERE finalized_grade IS NOT NULL and finalized_grade $filter $grade";
        $sqlSearch = "SELECT * FROM students WHERE finalized_grade IS NOT NULL and finalized_grade $filter $grade ORDER BY finalized_grade $sorting LIMIT $offset, $records_page";
    } else {
        $sqlCount = 'SELECT COUNT(*) FROM students';
        $sqlSearch = 'SELECT * FROM students ORDER BY group_number ASC LIMIT ' . $offset . ',' . $records_page . '';
    }
    $resultsFound = $db->countStudentRecordsOnCriteria($sqlCount);
    if ($resultsFound > 0) {
        $total_pages = ceil($resultsFound / $records_page);
        if ($pageNumber > $total_pages) {
            $pageNumber = $total_pages;
            $offset = ($pageNumber - 1) * $records_page;
        }
        if ($result = $db->dontWorryQuery($sqlSearch)) {
            $data = $result;
        }
    }
    $response["queryResults"] = $data;
    $response["resultsFound"] = $resultsFound;
    $response["pages"] = $total_pages;
    $response["currentPage"] = $pageNumber;
    echo json_encode($response);
} else {
    responseError("Cannot process your request.");
}
?>