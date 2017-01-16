<?php
include_once 'db_functions.php';
include_once 'db_tokens.php';
//getting input
$json = file_get_contents('php://input');
$array = json_decode($json, true);
//
$response = array();
$response['error'] = true;
$response['message'] = "unknown error";
//echo json_encode($array[0]);
$header = $array[0];
$publication_id = $header["publication_id"];
echo $publication_id;
$token = $header["token"];
$db = new DB_Functions();
$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($header['token']);
if($user_id >= 0){
    if($db->ifUserOwnsPublication($user_id, $publication_id)) {
        $count = 0;
        for ($i = 1; $i < count($array); $i++) {
            $pub_image = $array[$i];
            $count ++;
            if (!empty($pub_image)) {
                $pub_image_id = $pub_image["_id"];
                $pub_image_filename = $pub_image["filename"];
                $answer = $db->getTempImageLink($pub_image_id);
                $answer_ = mysql_fetch_array($answer);
                if ($answer_["image_link"] == $pub_image_filename) {
                    $db = new DB_Functions();
                    $sub_answer = $db->transferImage($pub_image_id, $pub_image_filename);
                    if(!$sub_answer["error"]){
                        $sub_answer_2 = $db->addImageToPublication($publication_id, $sub_answer['image_id']);
                        if(!$sub_answer_2["error"]){
                            $response["error"] = false;
                            $response["message"] = "successful";
                        }else{
                            $response['message'] = $sub_answer_2['message'];
                        }
                    }else{
                        $response['message'] = $sub_answer['message'];
                    }
                }else{
                    $response['message'] = "Wrong temporary image link set";
                }
            }else{
                $response['message'] = "No images";
            }
        }
    }else{
        $response['message'] = "Title or description not set";
    }
}else{
    $response['message'] = "No user";
}
//echo json_encode($header);
//echo $user_id;
$response['count'] = $count;
echo json_encode($response);
?>