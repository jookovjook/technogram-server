<?php
date_default_timezone_set('Europe/Moscow');
include_once 'db_tokens.php';
error_reporting(E_ERROR);
$date = new DateTime();
echo $date->getTimestamp();

echo "hi 1";
//return;
echo "hi 2";

echo "\r\n";

$db = new DB_Tokens();
$auth =  $db->authentificate('jookovjook', '12345678');

if($auth == false){
    echo "wrong password";
}else{
    echo $auth;
}