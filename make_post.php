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
$header = $array[0];
$title = $header["title"];
$description = $header["description"];
$branch = $header["branch"];
$token = $header["token"];
$db = new DB_Functions();
$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($header['token']);
$pub_image = $array[1];
if($user_id >= 0){
    if(!empty($title) && !empty($description) ) {
        if (!empty($pub_image)) {
            $pub_image_id = $pub_image["_id"];
            $pub_image_filename = $pub_image["filename"];
            $answer = $db->getTempImageLink($pub_image_id);
            $answer_ = mysql_fetch_array($answer);
            if ($answer_["image_link"] == $pub_image_filename) {
                $db = new DB_Functions();
                $sub_answer = $db->transferImage($pub_image_id, $pub_image_filename);
                //$sub_answer_ = mysql_fetch_array($sub_answer);
                if(!$sub_answer["error"]){
                    $sub_answer_2 = $db->makePublication($user_id, $title, $description, $sub_answer["image_id"], $branch);
                    //echo $sub_answer_2['message'];
                    //$sub_answer_2_ = mysql_fetch_array($sub_answer_2);
                    if(!$sub_answer_2["error"]){
                        $response["error"] = false;
                        $response["message"] = "successful";
                        $response['publication_id'] = $sub_answer_2['publication_id'];
                        $db->addPublicationView($sub_answer_2['publication_id'], $user_id);
                        $db->addPublicationStar($sub_answer_2['publication_id'], $user_id);
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
    }else{
        $response['message'] = "Title or description not set";
    }
}else{
    $response['message'] = "No user";
}
//echo json_encode($header);
//echo $user_id;
echo json_encode($response);
?>