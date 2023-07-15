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
    [ 'path' => './reservations', 'name' => 'Recettes'],
];


$params = ['page_title'=>'Recettes', 'breadcrumb' => $breadcrumb];




$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){

    case 'filter':

        $chambre = new \App\Model\Chambres();

        $start_date     = $_GET['start_date'];
        $end_date       = $_GET['end_date'].' 23:59:59';
        $room_status    = $_GET['room_status'];

        //$params ['filter_data'] = $chambre->getChambreWithOtherDetailsByStatus($room_status,$start_date,$end_date);


        $params ['start_date']  = $_GET['start_date'];
        $params ['end_date']    = $_GET['end_date'];
        $params ['room_status'] = $_GET['room_status'];


        render('reservations.html.twig', $params);

        break;
    case 'get_chambre_details':

        $chambre_id = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);
        $chambre_id = $chambre_id ['chambre_uniqid'];

        $get_chambre = new \App\Model\Chambres();

        $data = array('data'=>$get_chambre->getChambreByUniqId($chambre_id));
        $get_chambre = \GuzzleHttp\json_encode($data);

        echo $get_chambre;

        break;
    default:

        $chambres = new \App\Model\Reservations();

        $params ['recettes'] = $chambres->getReservations();

        render('recettes.html.twig', $params);
}