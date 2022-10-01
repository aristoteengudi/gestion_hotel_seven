<?php


/*
if (!in_array('admin',$_SESSION['roles'])){
    $params = [
        'title' => '403 forbidden - access restricted',
        'app_full_url' => get_app_full_url()];


    render('access_denied.html.twig', $params);
    exit();
}

*/

$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './users', 'name' => 'Réservations']
];
$params = ['page_title'=>'Réservations', 'breadcrumb' => $breadcrumb];

$action = app_request();
$date_time = date('Y-m-d H:i:s');

switch ($action){
    case '.':
        $message = array();

        break;
    default:


        render('reservations.html.twig', $params);
}
