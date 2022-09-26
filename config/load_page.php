<?php
require_once 'session_loader.php';

$twig->addGlobal('session', $_SESSION); //set session variable to global on twig templating

$page = 'index';
if(isset($_GET['page']) && $_GET['page']){
    $page = $_GET['page'];
}

if (file_exists("./src/Pages/{$page}.php")){

    require_once "./src/Pages/{$page}.php";
    exit();
}
require_once "./src/Pages/404.php";

