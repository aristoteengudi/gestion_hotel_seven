<?php

session_start();
$unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : false;

global $db;
$query = $db->fetchAssociative('SELECT * FROM t_users WHERE uid = ?',array($unique_id));
if(empty($query)) {
    require_once "./src/Pages/login.php";
    exit;
}

