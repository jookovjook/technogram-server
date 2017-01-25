<?php
include_once 'db_functions.php';

// Path to move uploaded files
$target_path = "uploads/";
$images_directory = "image_resources/";

// array for final json respone
$response = array();
$response['_id'] = -1;
$response['code'] = 204;
$response['message'] = "Server: unknown error 204.";
$response['filename'] = "";

// getting server ip address
$server_ip = gethostbyname(gethostname());

// final file url that is being uploaded
$file_upload_url = 'http://' . $server_ip . '/' . 'chatApp' . '/' . $target_path;

//get token from query
$token = isset($_POST['token']) ? $_POST['token'] : '';

//take user_id by token from DB
$db = new DB_Functions();
$user_id = $db->getUserIdByToken($token);

//try to proceed
if($user_id >= 0) {
    if (isset($_FILES['image']['name'])) {
        //generating random filename
        $file_exists = true;
        do {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $rand_name = '';
            for ($i = 0; $i < 64; $i++) $rand_name .= $characters[mt_rand(0, strlen($characters))];
            $temp = explode(".", $_FILES["image"]["name"]);
            //$target_path = $target_path . basename($_FILES['image']['name']);
            $filename = $rand_name . '.' . end($temp);
            $file_exists = file_exists($target_path . $filename) && file_exists($images_directory . $filename);
        } while ($file_exists);
        $target_path = $target_path . $filename;
        // reading other post parameters
        $response['filename'] = $filename;
        try {
            // Throws exception incase file is not being moved
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                // make error flag true
                $response['error'] = true;
                $response['code'] = 205;
                $response['message'] = 'Server: filed moving file from the temporary folder.';
            } else {
                $db = new DB_Functions();
                $_id = $db->insertTempImage($filename);
                if ($_id != "-1") {
                    // File successfully uploaded
                    $response['_id'] = $_id;
                    $response['error'] = false;
                    $response['code'] = 200;
                    $response['message'] = 'File uploaded successfully.';
                } else {
                    //Error pasting file into DB
                    $response['error'] = true;
                    $response['code'] = 203;
                    $response['message'] = "Server: error pasting file into DB.";
                }
            }
        } catch (Exception $e) {
            // Exception occurred. Make error flag true
            $response['error'] = true;
            $response['code'] = 202;
            $response['message'] = 'Server: ' . $e->getMessage();
        }
    } else {
        // File parameter is missing
        $response['error'] = true;
        $response['code'] = 201;
        $response['message'] = 'Server: not received any file.';
    }
}else{
    $response['error'] = true;
    $response['code'] = 206;
    $response['message'] = 'Server: wrong token.';
}
// Echo final json response to client
echo json_encode($response);
?>