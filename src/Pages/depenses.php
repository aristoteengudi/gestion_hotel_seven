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
    [ 'path' => './reservations', 'name' => 'Depenses'],
];


$params = ['page_title'=>'Depenses', 'breadcrumb' => $breadcrumb];


$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){

    case 'filter':
        $chambre = new \App\Model\Chambres();

        $month          = isset($_GET['month']) ? $_GET['month'] : '';
        $room_status    = isset($_GET['room_status'] ) ? $_GET['room_status'] : '   ';


        $params ['month']  = $month;
        $params ['type_recettes'] = $room_status;

        break;

    case 'demande_argent':

        $message = array();


        $dep_montant = isset($_POST['dep_montant']) ? $_POST['dep_montant'] : '';
        $dep_description = isset($_POST['dep_description']) ? $_POST['dep_description'] : '';
        try{

            $_insert_expenses = new \App\Model\Transactions();

            $_insert_expenses->insertExpenses($dep_description,$dep_montant);


            http_response_code(200);
            $message = array('response_code'=>200,'message'=>'demande fait');


        }catch (\Exception $exception){

            http_response_code(500);
            $message = array('response_code'=>500,'message'=>$exception->getMessage());

        }


        //echo '<pre style="margin:100px 0 0 100px;">';print_r($_GET);echo '</pre>';

        echo \GuzzleHttp\json_encode($message);

        break;
    default:

        $expense = new \App\Model\Transactions();

        $params ['expenses'] = $expense->getExpenses();


        //echo '<pre style="margin:100px 0 0 100px;">';print_r($_SESSION);echo '</pre>';

        render('depenses.html.twig', $params);
}