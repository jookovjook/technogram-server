<?php
include_once 'db_functions.php';
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$pub_id = $obj['pub_id'];
$db = new DB_Functions();
$adv = $db->getSoftAdv($pub_id);
$response = $array();
$response[] = $adv;
echo json_encode($response);