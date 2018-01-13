<?php
include_once 'db_functions.php';
error_reporting(E_ERROR);
$db = new DB_Functions();

for ($i = 0; $i < 1000000; $i++) {
    $db->makePublication(1, "title", "description", 1);
    echo $i;
}