<?php
include_once 'db_functions.php';
error_reporting(E_ERROR);
if(isset($_GET["publication_id"])){
    $publication_id = $_GET["publication_id"];
}else{
	$publication_id = 2;
}

$response = array();

if($publication_id != null){
    $db = new DB_Functions();
    $answer = $db->getComments($publication_id);
    while ($r = mysqli_fetch_assoc($answer)){
        $response[]=$r;
    }
    echo json_encode($response);
}
?>