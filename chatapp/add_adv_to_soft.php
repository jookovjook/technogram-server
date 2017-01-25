<?php
include_once 'db_functions.php';

//getting input
$json = file_get_contents('php://input');
$array = json_decode($json, true);

$response = array();
$response['error'] = true;
$response['message'] = "unknown error";
$header = $array[0];
$publication_id = $header['publication_id'];
$token = $header['token'];
$license = $header['license'];
$stage = $header['stage'];
$db = new DB_Functions();
$result = $db->addAdvToSoft($publication_id, $license, $stage);
if(!$result['error']){
   $response['error'] = false;
}
echo json_encode($response);