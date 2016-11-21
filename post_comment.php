<?php
include_once 'db_functions.php';

$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$response = array();
$db = new DB_Functions();
$answer = $db->postComment($obj["publication_id"], $obj["user_id"], $obj["comment"]);

while ($r = mysql_fetch_assoc($answer)){
    $response[]=$r;
}
echo json_encode($response);

?>