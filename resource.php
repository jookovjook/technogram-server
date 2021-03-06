<?php
// include our OAuth2 Server object

error_reporting(E_ERROR);

require_once __DIR__.'/server.php';
date_default_timezone_set('Europe/Moscow');

// Handle a request to a resource and authenticate the access token
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    //echo "error";
    die;
}

echo json_encode(array('success' => true, 'message' => 'You accessed my APIs!'));