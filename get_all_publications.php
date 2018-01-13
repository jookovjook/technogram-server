<?php

$start = microtime(true);
error_reporting(E_ERROR);

include_once 'db_functions.php';
include_once 'db_tokens.php';
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$last_id = $obj['last_id'];

$response = array();
$db = new DB_Functions();

$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($obj['token']);

if($last_id > 0) {
    $answer = $db->getAllPubsId($last_id);
}else{
    $answer = $db->getAllPublications();
}
while ($r = mysqli_fetch_assoc($answer)) {
    $r['like'] = 0;
    if($user_id > -1){
        if($db->ifX2LikeExists($r['publication_id'], $user_id)){
            $r['like'] = 2;
        }else{
            if($db->ifLikeExists($r['publication_id'], $user_id)){
                $r['like'] = 1;
            }
        }
    }
    $response[] = $r;

}
echo json_encode($response);

//echo $time_elapsed_secs = microtime(true) - $start;