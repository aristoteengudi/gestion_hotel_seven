<?php
/*
session_start();
$unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : false;

global $db;
$query = $db->fetchAssoc('SELECT * FROM tb_users WHERE unique_id = ?',array($unique_id));
if(empty($query)) {
    require_once "./src/Pages/login.php";
    exit;
}

*/