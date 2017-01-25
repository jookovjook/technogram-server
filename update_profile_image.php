<?php
include_once 'db_functions.php';
include_once 'db_tokens.php';
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$token = $obj['token'];
$image_id = $obj['id'];
$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($obj['token']);

$response['img_link'] = 1;

if($user_id >= 0){
    $img_link = $obj['img_link'];
    $db = new DB_Functions();
    $answer = $db->transferImage($image_id, $img_link);
    if($answer['error']){
        $response['img_link'] = 2;
    }else{
        $db->setProfileImage($user_id, $answer['image_id']);
        $response['img_link'] = 0;
    }
}

echo json_encode($response);