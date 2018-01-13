<?php
// include our OAuth2 Server object
require_once __DIR__.'/server.php';
date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
// Handle a request for an OAuth2.0 Access Token and send the response to the client
//$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

// http://testclient:testpass@localhost/oauth2test/token.php