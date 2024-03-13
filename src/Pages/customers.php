<?php


$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './chambres', 'name' => 'Nos Clients']
];
$params = ['page_title'=>'Nos Clients', 'breadcrumb' => $breadcrumb];

$action = app_request();

switch ($action){
    case 'filter':

        $customers = new \App\Model\Customers();

        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'].' 23:59:59';
        $room_status = $_GET['room_status'];

        $params ['filter_data'] = $customers->getCustomerByDate($room_status,$start_date,$end_date);

        $params ['start_date'] = $_GET['start_date'];
        $params ['end_date'] = $_GET['end_date'];
        $params ['room_status'] = $_GET['room_status'];

        render('chambres.html.twig', $params);


        break;
    case 'view':
        break;
    case 'getdata':
        break;
    default:
        $customers = new \App\Model\Customers();

        $params['customers'] = $customers->getAllCustomers();

        render('customers.html.twig', $params);
}

