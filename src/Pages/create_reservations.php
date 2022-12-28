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
    case 'insert':

        $message = array();

        $reservation = new \App\Model\Reservations();

        $db->beginTransaction();

        try {


            $client_uniqid = time().'_cust';

            $db->insert('t_clients',
                array(
                    'noms'=> $_POST['noms'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'telephone' => $_POST['telephone'],
                    'uniqid' =>$client_uniqid
                ));


            $explode_data_from_chambre_name = explode(',',$_POST['chambre']);
            $chambre = $explode_data_from_chambre_name[0];
            $prix = $explode_data_from_chambre_name[1];

            $reservation->nombre_personne = $_POST['nombre_personnes'];
            $reservation->client_uniqid = $client_uniqid;
            //$reservation->endate = '';
            //$reservation->stardate = '';
            $reservation->chambre_uniqid = $chambre;
            $reservation->prix = $prix;
            $reservation->createReservation();

            http_response_code(200);
            $message = array('response_code'=>200,
                'message'=>"account created.");


            $db->commit();


        }catch (\Exception $exception){

            $db->rollBack();
            if (strpos($exception->getMessage(),'Integrity constraint violation')!==false){
                http_response_code(200);
                $message = array('response_code'=>250,
                    'message'=>'account already exist. cuid ou phone number exist.');
            }else{
                http_response_code(500);
                $message = array('response_code'=>500,
                    'message'=>$exception->getMessage());
            }
        }



        echo \GuzzleHttp\json_encode($message);
        break;
    default:

        $chambre = new \App\Model\Chambres();

        $params ['chambre_disponible'] = $chambre->getChambreDisponible();

        render('form_reservation.html.twig', $params);
}
