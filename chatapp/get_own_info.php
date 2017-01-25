<?php
include_once 'db_functions.php';
include_once 'db_tokens.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$token = $obj['token'];

$dbt = new DB_Tokens();

$user_id = $dbt->getUserIdByToken($token);

if($user_id >= 0){
    $db = new DB_Functions();
    $answer = $db->getOwnInfo($user_id);
    $row = mysql_fetch_array($answer);
    $response['error_code'] = 0;
    $response['username'] = $row['username'];
    $response['name'] = $row['name'];
    $response['surname'] = $row['surname'];
    $response['img_link'] = $row['img_link'];
    $response['email'] = $row['email'];
    $response['about'] = $row['about'];
    echo json_encode($response);
}else{
    $response = array();
    $response['error_code'] = 1;
    echo json_encode($response);
}