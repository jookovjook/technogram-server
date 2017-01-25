<?php

include_once 'db_functions.php';
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$last_id = $obj['last_id'];

$response = array();
$db = new DB_Functions();

#echo $last_id;
#echo "hi";

if($last_id > 0) {
    $answer = $db->getAllPubsId($last_id);
}else{
    $answer = $db->getAllPublications();
}
while ($r = mysql_fetch_assoc($answer)) {
    $response[] = $r;
}
echo json_encode($response);
?>