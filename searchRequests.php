<?php
require "classes/oldConnect.php";
$queries = array();
$data = [];
$totalPages = $resultsFound = 0;
$pageNumber = 1;
$records_page = 5;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_SERVER['QUERY_STRING'])) {

    parse_str($_SERVER['QUERY_STRING'], $queries);
    if (isset($queries['studentId'])) {
        $studentid = $queries['studentId'];
        $offset = ($pageNumber - 1) * $records_page;
        $totalPages = 'SELECT COUNT(*) FROM students WHERE ID REGEXP "^' . $studentid . '" ORDER BY ID';
        $result = $mysqli->query($totalPages);
        $resultsFound = $result->fetch_array()[0];
        if ($resultsFound > 0) {
            $total_pages = ceil($resultsFound / $records_page);
            $sql = 'SELECT * FROM students WHERE ID REGEXP "^' . $studentid . '" ORDER BY ID LIMIT ' . $offset . ',' . $records_page . '';
            $res_data = $mysqli->query($sql);
            while ($row = $res_data->fetch_array()) {
                $data[] = $row;
            }
        }
    }
    echo json_encode(array("resultsFound" => $resultsFound));
} else if (empty($_SERVER['QUERY_STRING'])) {
    $sql = "SELECT * FROM students ORDER BY group_number ASC";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    $resultsFound = $result->num_rows;
    echo json_encode(array("resultsFound" => $resultsFound));
}
