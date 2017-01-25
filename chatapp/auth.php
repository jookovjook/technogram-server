<?php
// oauth2 files: authorize.php, resource.php, server.php, token.php
// oauth2 dbs: oauth_*
include_once 'db_tokens.php';

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
}else{
    if($db->ifNotPassword($username, $password)){
        $response['error'] = 2;
        $response['message'] = 'Wrong password';
        $response['token'] = '';
        $response['user_id'] = -1;
    }else{
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['token'] = $db->authentificate($username, $password);
        $response['user_id'] = $db->getUserIdByToken($response['token']);
    }
}
echo json_encode($response);

