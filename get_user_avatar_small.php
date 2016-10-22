<?php
include_once 'db_functions.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$user_id = $obj['user_id'];

$response = array();

if($user_id >= 0){
    $db = new DB_Functions();
    $answer = $db->getUserSmallAvatar($user_id);
    $r = mysql_fetch_array($answer);
    $respone['img_link'] = $r['img_link'];
    echo json_encode($r);
}
