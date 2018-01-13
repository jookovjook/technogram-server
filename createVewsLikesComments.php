<?php

include_once 'db_functions.php';
$db = new DB_Functions();

$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

// users: 23 - 187904
// pubs: 658877 - 1216579

for ($i = 0; $i < 10; $i++){

    $user_id = rand(23, 187904);

//    $users_views = rand(100, 10000);
//
//    $numbers = range(99832, 658876);
//    shuffle($numbers);
//    $views = array_slice($numbers, 0, $users_views);
//
//    for($k = 0; $k < count($views); $k++){
//        $pub_id = $views[$k];
//        $db->addPublicationView($pub_id, $user_id);
//            $like_threshold = rand(0, 100);
//            if($like_threshold > 50){
//                $db->addPublicationLike($pub_id, $user_id);
//                if($like_threshold > 70){
//                    $db->addPublicationX2Like($pub_id, $user_id);
//                }
//            }
//            $comment_threshold = rand(0, 100);
//            if($comment_threshold > 80){
//                $comments_count = rand(0, 10);
//                for($i = 0; $i < $comments_count; $i++){
//                    $comment = '';
//                    for($j=0; $j < 80; $j++) $comment .= $characters[mt_rand(0, strlen($characters))];
//                    $db->postComment($pub_id, $user_id, $comment);
//                }
//            }
//    }

//    for($pub_id = 99832; $pub_id < 658877; $pub_id++){
//            $db->addPublicationView($pub_id, $user_id);
//            $like_threshold = rand(0, 100);
//            if($like_threshold > 50){
//                $db->addPublicationLike($pub_id, $user_id);
//                if($like_threshold > 70){
//                    $db->addPublicationX2Like($pub_id, $user_id);
//                }
//            }
//            $comment_threshold = rand(0, 100);
//            if($comment_threshold > 80){
//                $comments_count = rand(0, 10);
//                for($i = 0; $i < $comments_count; $i++){
//                    $comment = '';
//                    for($j=0; $j < 80; $j++) $comment .= $characters[mt_rand(0, strlen($characters))];
//                    $db->postComment($pub_id, $user_id, $comment);
//                }
//            }
//    }

    $pubs_ = rand(1, 100);
    if($pubs_ < 50){
        $pubs = rand(1, 3);
    }else{
        if($pubs_ < 80){
            $pubs = rand(4, 10);
        }else{
            if($pubs_ < 90){
                $pubs = rand(11, 50);
            }else{
                if($pubs_ < 95){
                    $pubs = rand(51, 200);
                }else{
                    $pubs = rand(201, 500);
                }
            }
        }
    }

    for($count = 0; $count < $pubs; $count++){
        $pub_id = rand(658877, 1216579);
        $db->addPublicationView($pub_id, $user_id);
        $like_threshold = rand(0, 100);
        if($like_threshold > 80){
            $db->addPublicationLike($pub_id, $user_id);
            if($like_threshold > 90){
                $db->addPublicationX2Like($pub_id, $user_id);
            }
        }
        $comment_threshold = rand(0, 100);
        if($comment_threshold > 95){
            $comments_count = rand(1, 3);
            for($i = 0; $i < $comments_count; $i++){
                $comment = '';
                for($j=0; $j < 80; $j++) $comment .= $characters[mt_rand(0, strlen($characters))];
                $db->postComment($pub_id, $user_id, $comment);
            }
        }
    }

}