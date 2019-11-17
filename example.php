<?php
include "database.php";

$db = new Database();
$filter = "<=";
 $grade = 9;
 $dbConect = $db->getDbConnection();
  $sorting = "ASC";
  $sql = "WHERE finalized_grade IS NOT NULL and finalized_grade $filter $grade ORDER BY finalized_grade $sorting";
  
  echo $db->countStudentRecordsOnCriteria($sql);
  ?>

