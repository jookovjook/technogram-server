<?php
include_once 'db_functions.php';
include_once 'db_tokens.php';
error_reporting(E_ERROR);
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$response = array();
$db = new DB_Functions();
$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($obj['token']);

if($user_id >= 0){
    $answer = $db->postComment($obj["publication_id"], $user_id, $obj["comment"]);
    $row = mysqli_fetch_array($answer);
    $response['error'] = 0;
    $response['message'] = "success";
    $response['comment_id'] = $row['comment_id'];
    $response['username'] = $row['username'];
    $response['publication_id'] = $row['publication_id'];
    $response['user_id'] = $row['user_id'];
    $response['comment'] = $row['comment'];
    $response['img_link'] = $row['img_link'];
    echo json_encode($response);
    return;
}

$response['error'] = 1;
$response['message'] = 'wrong token';
$response['comment_id'] = -1;

echo json_encode($response);

?>