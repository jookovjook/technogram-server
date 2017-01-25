<?php
include_once 'db_functions.php';
include "db_tokens.php";
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$username = $obj['username'];
$password = $obj['password'];
$email = $obj['email'];

$dbt = new DB_Tokens();
$exists = $dbt->ifUserExists('username');

$response = array();
$response['error'] = 4;
$response['message'] = "Unknown error 4";
if($exists){
    $response['error'] = 1;
    $response['message'] = 'User with this username already exists';
}else{
    $exists = $dbt -> ifEmailExists($email);
    if($exists){
        $response['error'] = 2;
        $response['message'] = 'User with this e-mail already exists';
    }else{
        $db = new DB_Functions();
        $result = $db->register($username, $password, $email);
        $error = $result['errod'];
        if($error){
            $response['error'] = 3;
            $response['message'] = 'Unknown error 3';
        }else{
            $response['error'] = 0;
            $response['message'] = 'successful';
            $response['user_id'] = $result['user_id'];
            $response['token'] = $dbt->addToken($result['user_id']);
        }
    }
}
echo json_encode($response);