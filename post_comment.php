<?php
include_once 'db_functions.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$db = new DB_Functions();
$answer = $db->postComment($obj["publication_id"], $obj["user_id"], $obj["comment"]);

$response["a"]=$answer;
$response["error_code"] = '000';
$response['message'] = 'OK';
echo json_encode($obj);

?>