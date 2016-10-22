<?php
include_once 'db_functions.php';
if(isset($_GET["publication_id"])){
    $publication_id = $_GET["publication_id"];
}

$response = array();

if($publication_id != null){
    $db = new DB_Functions();
    $answer = $db->getComments($publication_id);
    while ($r = mysql_fetch_assoc($answer)){
        $response[]=$r;
    }
    echo json_encode($response);
}
?>