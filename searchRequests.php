<?php
include "checks/databaseConnection.php";
$response = array();

$queries = array();
$data = [];
$total_pages = $resultsFound = 0;
$pageNumber = 1;
$records_page = 5;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_SERVER['QUERY_STRING'])) {
    $studentsCount = 'SELECT COUNT(*) FROM students';
    parse_str($_SERVER['QUERY_STRING'], $queries);
    $pageNumber = $_GET["pageNo"];
    $offset = ($pageNumber - 1) * $records_page;
    if (isset($queries['studentId'])) {
        $studentid = $queries['studentId'];
        $sql = 'SELECT COUNT(*) FROM students WHERE ID REGEXP "^' . $studentid . '" ORDER BY ID';
        $result = $mysqli->query($sql);
        $resultsFound = $result->fetch_array()[0];
        if ($resultsFound > 0) {
            $total_pages = ceil($resultsFound / $records_page);
            $sql = 'SELECT * FROM students WHERE ID REGEXP "^' . $studentid . '" ORDER BY ID LIMIT ' . $offset . ',' . $records_page . '';
            $res_data = $mysqli->query($sql);
            while ($row = $res_data->fetch_array()) {
                $data[] = $row;
            }
        }
    } else if (isset($queries['grade'])) {
        $grade = $queries['grade'];
        $filter = $queries['filter'];
        $sorting = $queries['sorting'];
        #sending error responses
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
    } else {
        $sql = 'SELECT COUNT(*) FROM students';
        $result = $mysqli->query($sql);
        $resultsFound = $result->fetch_array()[0];
        if ($resultsFound > 0) {
            $total_pages = ceil($resultsFound / $records_page);
            $sql = 'SELECT * FROM students ORDER BY group_number ASC LIMIT ' . $offset . ',' . $records_page . '';
            $res_data = $mysqli->query($sql);
            while ($row = $res_data->fetch_array()) {
                $data[] = $row;
            }
        }
    }
    $response["queryResults"] = $data;
    $response["resultsFound"] = $resultsFound;
    $response["pages"] = $total_pages;
    $response["currentPage"] = $pageNumber;
    echo json_encode($response);
}
?>