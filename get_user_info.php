<?php

include_once 'db_functions.php';
error_reporting(E_ERROR);
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$user_id = $obj['user_id'];
if(is_null($user_id))$user_id = -1;
$response = array();

$response['error_code'] = 1;

if($user_id >= 0){
    $db = new DB_Functions();
    $answer = $db->getUsernameAndAvaId($user_id);
    $row = mysqli_fetch_array($answer);
    $response['username'] = $row['username'];
    $answer = $db->getImageById($row['avatar_small']);
    $row = mysqli_fetch_array($answer);
    $response['img_link'] = $row['img_link'];
    $answer = $db->getNameSurnameAbout($user_id);
    $row = mysqli_fetch_array($answer);
    $response['name'] = $row['name'];
    $response['surname'] = $row['surname'];
    $response['about'] = $row['about'];
    $answer = $db->getStats($user_id);
    $response['views'] = $answer['views'];
    $response['likes'] = $answer['likes'];
    $response['x2likes'] = $answer['x2likes'];
    $response['subs'] = $answer['subs'];
    $response['error_code'] = 0;
}

echo json_encode($response);