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
    case 'create':

        $customer_room = filter_var($_POST['customer_room'],FILTER_SANITIZE_STRING);
        $customer_name = filter_var($_POST['customer_name'],FILTER_SANITIZE_STRING);
        $customer_firstname = filter_var($_POST['customer_firstname'],FILTER_SANITIZE_STRING);
        $customer_mobile_phone = filter_var($_POST['customer_mobile_phone'],FILTER_SANITIZE_STRING);
        $customer_genre = filter_var($_POST['customer_genre'],FILTER_SANITIZE_STRING);
        $customer_occupant = filter_var($_POST['customer_occupant'],FILTER_SANITIZE_STRING);
        $customer_type_piece_identite = filter_var($_POST['customer_type_piece_identite'],FILTER_SANITIZE_STRING);
        $customer_numero_piece = filter_var($_POST['customer_numero_piece'],FILTER_SANITIZE_STRING);
        $customer_autre_details = filter_var($_POST['customer_autre_details'],FILTER_SANITIZE_STRING);
        $customer_start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
        $customer_end_date = filter_var($_POST['end_date'],FILTER_SANITIZE_STRING);



        $customer_start_date = str_replace('/','-',trim(filter_var($customer_start_date,FILTER_SANITIZE_STRING)));
        $customer_start_date = date('Y-m-d ',strtotime($customer_start_date)).date('H:i:s');


        $customer_end_date = str_replace('/','-',trim(filter_var($customer_end_date,FILTER_SANITIZE_STRING)));
        $customer_end_date = date('Y-m-d ',strtotime($customer_end_date)).date('H:i:s');

        $customer_uniqid = time().'_customer';




        $get_chambre_details = new \App\Model\Chambres();
        $get_chambre_details = $get_chambre_details->getChambreByNummber($customer_room);
        $chambre_cout = $get_chambre_details['cout'];
        $chambre_uniqid = $get_chambre_details['chambre_uniqid'];


        $reservation = new \App\Model\Reservations();
        $Images = new \App\Classes\Images();
        $Images->path = 'public/upload';
        $Images->file = $_FILES['cust_photo_piece_identite'];
        $Images->file_name = $customer_uniqid;

        $_succes_message = array();
        $_error_message = array();

        $db->beginTransaction();

        try {

            $db->insert('t_clients',
                array(
                    'noms'=> $customer_name,
                    'prenom'=> $customer_firstname,
                    'telephone' => $customer_mobile_phone,
                    'genre' => $customer_genre,
                    'type_piece_identite' => $customer_type_piece_identite,
                    'numero_piece' => $customer_numero_piece,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'client_uniqid' =>$customer_uniqid
                ));


            $reservation->nombre_personne = $customer_occupant;
            $reservation->client_uniqid = $customer_uniqid;
            $reservation->endate = $customer_end_date;
            $reservation->stardate = $customer_start_date;
            $reservation->chambre_uniqid = $chambre_uniqid;
            $reservation->cout = $chambre_cout;
            $reservation->createReservation();

            $Images->InsertImage();

            $db->commit();


            http_response_code(200);

            $_succes_message = array('response_code'=>200,'string'=>'success', 'message'=>"Chambre Réservé avec Success");

            redirectUrl('reservations',$_succes_message);

        }catch (\Exception $exception){

            $db->rollBack();
            if (strpos($exception->getMessage(),'Integrity constraint violation')!==false){
                http_response_code(200);
                $_error_message = array('response_code'=>250,'string'=>'error',
                    'message'=>'numéro de la chambre existant. merci saisir un numéro correct.');
            }else{
                http_response_code(500);
                $_error_message = array('response_code'=>500,'string'=>'error',
                    'message'=>$exception->getMessage());
            }

            refreshSpecific('create_reservations',$_error_message);

        }

        break;
    default:

        $chambre = new \App\Model\Chambres();

        $params ['chambre_disponible'] = $chambre->getChambreDisponible();

        render('form_reservation.html.twig', $params);
}
