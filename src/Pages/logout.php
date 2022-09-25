<?php
$_SESSION["unique_id"] = "";
unset($_SESSION['unique_id']);
session_destroy();

$unique_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : false;
if ($unique_id){
    require_once "./src/Pages/login.php";
    exit;
}
header("LOCATION: ".getBaseUrl());