<?php
include_once 'db_tokens.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$token = $obj['token'];

$db = new DB_Tokens();
$response = array();
$response['user_id'] = $db->getUserIdByToken($token);
echo json_encode($response);

?>