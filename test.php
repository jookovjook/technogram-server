<?php
$postdata = json_decode(file_get_contents("php://input"),TRUE);

$firstName= $postdata["firstName"];
$lastName = $postdata["lastName"];
// Store values in an array
$returnValue = array("firstName"=>$firstName, "lastName"=>$lastName);

// Send back request in JSON format
//echo (file_get_contents("php://input"));
echo json_encode($returnValue);