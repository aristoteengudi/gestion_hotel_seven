<?php


$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './chambres', 'name' => 'Nos Chambres']
];
$params = ['page_title'=>'Nos Chambres', 'breadcrumb' => $breadcrumb];

$action = app_request();

switch ($action){
    case 'filter':

        $chambre = new \App\Model\Chambres();

        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'].' 23:59:59';
        $room_status = $_GET['room_status'];

        $params ['filter_data'] = $chambre->getChambreWithOtherDetailsByStatus($room_status,$start_date,$end_date);

        $params ['start_date'] = $_GET['start_date'];
        $params ['end_date'] = $_GET['end_date'];
        $params ['room_status'] = $_GET['room_status'];

        render('chambres.html.twig', $params);


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

