<?php
include_once 'db_functions.php';
include_once "db_tokens.php";
error_reporting(E_ERROR);
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$token = $obj['token'];

$db = new DB_Functions();
$dbt = new DB_Tokens();

$user_id = $dbt->getUserIdByToken($obj['token']);
$response = array();
$response['error'] = 1;
$response['like'] = -1;

//error
//0 - no error
//1 - wrong token
//2 - wrong publication id

//like
//-1 - nothing
//0 - disliked
//1 - liked
//2 - double liked

if($user_id > -1){
    $pub_id = $obj['pub_id'];
    if($db->ifPubExists($pub_id)){
        $response['error'] = 0;
        if($db->ifX2LikeExists($pub_id, $user_id)){
            $db->deleteLike($pub_id, $user_id);
            $db->deleteX2Like($pub_id, $user_id);
            $response['like'] = 0;
        }else{
            if($db->ifLikeExists($pub_id, $user_id)){
                $db->addPublicationX2Like($pub_id, $user_id);
                $response['like'] = 2;
            }else{
                $db->addPublicationLike($pub_id, $user_id);
                $response['like'] = 1;
            }
        }
    }else{
        $response['error'] = 2;
    }
}else{
    $response['error'] = 1;
}

echo json_encode($response);