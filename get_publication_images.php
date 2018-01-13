<?php
include_once 'db_functions.php';
error_reporting(E_ERROR);
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$publication_id = $obj['publication_id'];

$response = array();

if($publication_id > 0){
    $db = new DB_Functions();
    $answer = $db->getPublicationImages($publication_id);
    while($r = mysqli_fetch_assoc($answer)) {
        $response[] = $r;
    }
    echo json_encode($response);
}
?>