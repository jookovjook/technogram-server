<?php
include_once 'db_functions.php';
$db = new DB_Functions();
echo $db->getUserIdByToken(10);
?>