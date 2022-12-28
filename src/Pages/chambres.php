<?php


$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './chambres', 'name' => 'Nos Chambres']
];
$params = ['page_title'=>'Nos Chambres', 'breadcrumb' => $breadcrumb];

$action = app_request();

switch ($action){
    case 'insert':

        break;
    case 'edit':
    case 'batch_form':
        break;
    case 'delete':
        break;
    case 'view':
        break;
    case 'getdata':
        break;
    default:
        $chambre = new \App\Model\Chambres();

        $params['list_chambre'] = $chambre->getChambreWithOtherDetails();

        render('chambres.html.twig', $params);
}

