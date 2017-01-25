<?php
include_once 'db_functions.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$user_id = $obj['user_id'];

$response = array();

if($user_id >= 0){
    $db = new DB_Functions();
    $answer = $db->getUserAddInfo($user_id);
    $row = mysql_fetch_array($answer);
    $response['error_code'] = '000';
    $response['username'] = $row['username'];
    $response['name'] = $row['name'];
    $response['surname'] = $row['surname'];
    $response['img_link'] = $row['img_link'];
    $response['about'] = $row['about'];
    echo json_encode($response);
}

?>