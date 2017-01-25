<?php
include_once 'db_functions.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$user_id = $obj['user_id'];
$last_id = $obj['last_id'];

$response = array();

if($user_id >= 0){
    $db = new DB_Functions();
    if($last_id < 0) {
        $answer = $db->getUserPublications($user_id);
    }else{
        $answer = $db->getUserPubsId($user_id, $last_id);
    }
    while($r = mysql_fetch_assoc($answer)) {
        $response[] = $r;
    }
    echo json_encode($response);
}

?>