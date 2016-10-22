<?php
include_once 'db_functions.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$publication_id = $obj['publication_id'];

$response = array();

if($publication_id > 0){
    $db = new DB_Functions();
    $answer = $db->getPublication($publication_id);
    while($r = mysql_fetch_assoc($answer)) {
        $response[] = $r;
    }
    echo json_encode($response);
}
?>