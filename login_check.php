<?php
error_reporting(E_ERROR);
/**
 * Features:
 */

include_once 'db_functions.php';

if (isset($_GET["username"])) {
    $username = $_GET['username'];
}

if (isset($_GET["password"])) {
    $password = $_GET['password'];
}

$response = array();


if ($username == null || $password == null){
    $response["error_code"] = '001';
    $response['message'] = 'Insert username and password';
    echo json_encode($response);
} else {
    $db = new DB_Functions();
    $answer = $db->getUser($username);
    $row = mysqli_fetch_array($answer);
    $count = count($row);
    if ($count == 4 ){
        if($password == $row['password']) {
            $response["error_code"] = '000';
            $response['message'] = 'OK';
            $response['user_id'] = $row['user_id'];
            //echo $row['password'];
            echo json_encode($response);
        } else {
            $response["error_code"] = '003';
            $response['message'] = 'Wrong password';
            echo json_encode($response);
        }
    }else{
        $response["error_code"] = '002';
        $response['message'] = 'User does not exist.';
        echo json_encode($response);
        echo $count;
    }

}