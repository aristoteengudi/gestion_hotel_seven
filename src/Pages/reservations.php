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
    [ 'path' => './reservations', 'name' => 'Réservations'],
];


$params = ['page_title'=>'Réservations', 'breadcrumb' => $breadcrumb];




$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){
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

        $params ['reservations'] = $chambres->getReservations();

        render('reservations.html.twig', $params);
}
