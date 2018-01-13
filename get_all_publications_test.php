<?php
$start = microtime(true);
error_reporting(E_ERROR);
include_once 'db_functions.php';
include_once 'db_tokens.php';
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$last_id = $obj['last_id'];

$response = array();
$db = new DB_Functions();

$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($obj['token']);

if($last_id > 0) {
    $answer = $db->getAllPubsAfterIdOptimized($last_id);
}else{
    $answer = $db->getAllPubsOptimized();
}
while ($r = mysqli_fetch_assoc($answer)) {
    $r['like'] = 0;
    $pub_id = intval($r['publication_id']);
    $answer2 = $db->countComments($pub_id);
    if(mysqli_num_rows($answer2) == 1) $r['comments'] = (mysqli_fetch_row($answer2))[1];
        else $r['comments'] = 0;

    $r['img_link'] = (mysqli_fetch_row($db->getImageById(intval($r['image']))))[0];

    $answer2 = $db->countViews($pub_id);
    if(mysqli_num_rows($answer2) == 1) $r['views'] = (mysqli_fetch_row($answer2))[0];
        else $r['views'] = 0;

    $answer2 = $db->countLikes($pub_id);
    if(mysqli_num_rows($answer2) == 1) $r['likes'] = (mysqli_fetch_row($answer2))[0];
    else $r['likes'] = 0;

    $answer2 = $db->countX2Likes($pub_id);
    if(mysqli_num_rows($answer2) == 1) $r['x2likes'] = (mysqli_fetch_row($answer2))[0];
    else $r['x2likes'] = 0;

    $answer2 = $db -> getUserForPud(intval($r['user_id']));
    $row = mysqli_fetch_row($answer2);
    $r['username'] = $row[0];
    $r['small_avatar'] = (mysqli_fetch_row($db->getImageById(intval($row[1]))))[0];

    //$r['x2likes'] = mysql_fetch_row($db->countX2Likes($pub_id))[0];
    if($user_id > -1){
        if($db->ifX2LikeExists($r['publication_id'], $user_id)){
            $r['like'] = 2;
        }else{
            if($db->ifLikeExists($r['publication_id'], $user_id)){
                $r['like'] = 1;
            }
        }
    }
    $response[] = $r;

}
echo json_encode($response);

echo $time_elapsed_secs = microtime(true) - $start;