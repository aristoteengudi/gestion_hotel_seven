<?php
use Twig\Loader\FilesystemLoader;

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, ['debug'=>true]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

function render($tpl, $params){
    global $twig;
    echo $twig->render('pages/'.$tpl, $params);
}