<?php 

mysqli_report(MYSQLI_REPORT_OFF);

$db_name = "job_postings";
$host = "localhost";
$user = "root";
$password = "";

$database = new mysqli($host , $user , $password , $db_name);
if($database->connect_errno) {
    die("error" . $database->connect_errno);
}
return $database

?>