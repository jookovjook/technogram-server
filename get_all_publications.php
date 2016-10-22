<?php

include_once 'db_functions.php';

$response = array();

$db = new DB_Functions();
$answer = $db->getAllPublications();
while($r = mysql_fetch_assoc($answer)) {
    $response[] = $r;
}
echo json_encode($response);

?>