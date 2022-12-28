<?php
use Twig\Loader\FilesystemLoader;

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, ['debug'=>true]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

function render($tpl, $params){
    global $twig;
    echo $twig->render('pages/'.$tpl, $params);
}

function redirectUrl($path,$flash_message = ''){

    $path = getBaseUrl().$path;

    $_SESSION['flash_message'] = !empty($flash_message) ? $flash_message : false;

    header('location:' .$path);

    //unset($_SESSION['flash_message']);

}


function refreshCurrent(){
    header("Refresh:0");
}

function refreshSpecific($path,$flash_message){

    $path = getBaseUrl().$path;

    $_SESSION['flash_message'] = !empty($flash_message) ? $flash_message : false;

    header("Refresh:0; url={$path}");
    //unset($_SESSION['flash_message']);
}