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
$db = new DB_Functions();
for ($i = 1; $i < count($array); $i++) {
    $soft_link = $array[$i];
    $result = $db -> addLinkToSoft($publication_id, $soft_link['link'],$soft_link['type']);
    if($result['error']){
        $response['error'] = true;
        $i = count($array);
        echo "aaaa";
    }else{
        $response['error'] = false;
    }
}
echo json_encode($response);