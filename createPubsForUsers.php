<?php

include_once 'db_functions.php';
$db = new DB_Functions();

$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

// 187904

for($i = 176355; $i < 187905; $i++){
    $threshold = rand (0 , 100);
    //$threshold = 100;
    if($threshold > 50){
        $count = rand (2 , 10);
        //$count = 1;
        for($j = 0; $j < $count; $j++){
            $title = '';
            $description = '';
            $image_link = '';
            for ($l = 0; $l < 12; $l++) $title .= $characters[mt_rand(0, strlen($characters))];
            for ($l = 0; $l < 48; $l++) $description .= $characters[mt_rand(0, strlen($characters))];
            for ($l = 0; $l < 48; $l++) $image_link .= $characters[mt_rand(0, strlen($characters))];
            $answer = $db -> makePublicationTest($i, $title, $description, $image_link);
            $pub_id = $answer['pub_id'];
            $img_count = rand (0 , 7);
            for($k = 0; $k < $img_count; $k ++){
                $image_link = '';
                for ($l = 0; $l < 48; $l++) $image_link .= $characters[mt_rand(0, strlen($characters))];
                $db->addImageToPublicationTest($pub_id, $image_link);
            }
        }
    }
}

echo $i;