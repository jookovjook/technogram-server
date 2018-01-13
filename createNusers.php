<?php

include_once 'db_functions.php';
$db = new DB_Functions();

$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

$N = 100;

for ($j = 0; $j < $N; $j++) {

    $username = '';
    $email = "";
    $password = "";
    $name = "";
    $surname = "";
    $about = "";
    $img_link = "";

    for ($i = 0; $i < 12; $i++) $username .= $characters[mt_rand(0, strlen($characters))];
    for ($i = 0; $i < 12; $i++) $email .= $characters[mt_rand(0, strlen($characters))];
    for ($i = 0; $i < 12; $i++) $password .= $characters[mt_rand(0, strlen($characters))];
    for ($i = 0; $i < 6; $i++) $name .= $characters[mt_rand(0, strlen($characters))];
    for ($i = 0; $i < 12; $i++) $surname .= $characters[mt_rand(0, strlen($characters))];
    for ($i = 0; $i < 36; $i++) $about .= $characters[mt_rand(0, strlen($characters))];
    for ($i = 0; $i < 64; $i++) $img_link .= $characters[mt_rand(0, strlen($characters))];


    //$result = $db->registerTest($username, $password, $email, $name, $surname, $about, $img_link);

    echo $result['error'];

}