<?php
include_once 'db_functions.php';
include_once 'db_tokens.php';
error_reporting(E_ERROR);
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

$token = $obj['token'];
$dbt = new DB_Tokens();
$user_id = $dbt->getUserIdByToken($obj['token']);
$response = array();

//INPUT:

//Input os jsonObject:
//If user wants to update name, surname, about, username, email -> he needs token
//User can just leave empty fields and theirs' values, if he doesn't want to update them
//Example:
// {"token":"abcdefghigklmnopqrstuvwxyz", "name" : "MyName", "up_name" : true, "usrename" : ""}
// name will be updated to "MyName" (if token is correct)
// field username will be just ignored (because its empty)

//There are 2 ways to update password:
//1) if he has token -> password will be updated the same way, as remaining values
//2) if he doesn't has token, user need to send email and LEAVE TOKEN EMPTY!
//Example:
//{"email" : "a@b.c", "curr_pass" : "current password", "password" : "new password"}

//OUTPUT:
//Output is jsonObject with error codes for each of value


//Error codes for all:
// 1, if user doesn't update the value
// 0, if value updated successful

$response['name'] = 1;
$response['surname'] = 1;
$response['about'] = 1;

$response['token'] = 1;
//1, if token is wrong
//0, if OK

$response['username'] = 1;
//2, if length of username < 3
//3, if username already exists

$response['password'] = 1;
//2, if current password is wrong
//3, if password length < 8
//4, if password is unsafe
//5 wrong email by updating password without token

$response['email'] = 1;
//2, if email is fake
//3, if email already exists


if($user_id >= 0){
    $response['token'] = 0;
    $db = new DB_Functions();

    //"up_" prefix: true if value is needed to update
    // some values don't require "up_" - subvalue

    $name = $obj['name'];
    $up_name = $obj['up_name'];

    $surname = $obj['surname'];
    $up_surname = $obj['up_surname'];

    $about = $obj['about'];
    $up_about = $obj['up_about'];

    $username = $obj['username'];

    $password = $obj['password'];
    $curr_pass = $obj['curr_pass'];

    $email = $obj['email'];

    if($up_name) {
        $db->updateName($user_id, $name);
        $response['name'] = 0;
    }

    if($up_surname) {
        $db->updateSurname($user_id, $surname);
        $response['surname'] = 0;
    }

    if($up_about) {
        $db->updateAbout($user_id, $about);
        $response['about'] = 0;
    }

    if(strlen($username) > 0) {
        if (strlen($username) > 3) {
            if ($db->updateUsername($user_id, $username)) {
                $response['username'] = 0;
            } else {
                $response['username'] = 3;
            }
        } else {
            $response['username'] = 2;
        }
    }

    if(strlen($password) > 0) {
        if (strlen($password) > 7) {
            if ($db->updatePassword($user_id, $curr_pass, $password)) {
                $response['password'] = 0;
            } else {
                $response['password'] = 2;
            }
        } else {
            $response['password'] = 3;
        }
    }

    if(strlen($email) > 0) {

        if (strlen($email) > 4) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($db->updateEmail($user_id, $email)) {
                    $response['email'] = 0;
                } else {
                    $response['email'] = 3;
                }
            } else {
                $response['email'] = 2;
            }
        } else {
            $response['email'] = 2;
        }
    }

}else{
    //If user doesn't has token, but wants to update password
    //update only by email
    if($token == ""){
        $response['token'] = 1;
        $password = $obj['password'];
        $curr_pass = $obj['curr_pass'];
        $email = $obj['email'];
        $db = new DB_Functions();
        $dbt = new DB_Tokens();
        $user_id = $dbt->getUserIdByEmail($email);
        if(strlen($email) > 4) {
            if ($user_id >= 0) {
                if(strlen($password) > 7) {
                    if ($db->updatePassword($user_id, $curr_pass, $password)) {
                        $response['password'] = 0;
                    } else {
                        $response['password'] = 2;
                    }
                }else{
                    $response['password'] = 3;
                }
            }else{
                $response['password'] = 5;
            }
        }else{
            $response['password'] = 5;
        }
    }
}

echo json_encode($response);