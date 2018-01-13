<?php
include_once 'db_tokens.php';
error_reporting(E_ERROR);

$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$username = $obj['username'];
$password = $obj['password'];

$db = new DB_Tokens();
$response = array();

if($db->ifUserNotExists($username)){
    $response['error'] = 1;
    $response['message'] = 'User doesnt exist';
    $response['token'] = '';
    $response['user_id'] = -1;
    $response['avatar'] = "";
}else{
    if($db->ifNotPassword($username, $password)){
        $response['error'] = 2;
        $response['message'] = 'Wrong password';
        $response['token'] = '';
        $response['user_id'] = -1;
        $response['avatar'] = "";
    }else{
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['token'] = $db->authentificate($username, $password);
        $response['user_id'] = $db->getUserIdByToken($response['token']);
        $response['avatar'] = $db->getUserAvatar($response['user_id']);
        $response['email'] = $db->getUserEmail($response['user_id']);
        $info = $db->getUserInfo($response['user_id']);
        $response['name'] = $info['name'];
        $response['surname'] = $info['surname'];
        $response['about'] = $info['about'];
    }
}

echo json_encode($response);