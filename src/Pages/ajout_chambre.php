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
    [ 'path' => './chambres', 'name' => 'Chambres'],
    [ 'sub_path' => './chambres', 'name' => 'Ajout Chambre']
];
$params = ['page_title'=>'Ajout Chambre', 'breadcrumb' => $breadcrumb];

$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){
    case 'create':

        $chambre_uniqid = filter_var($_POST['chambre_uniqid'],FILTER_SANITIZE_NUMBER_INT);
        $chambre_cout = trim(filter_var($_POST['chambre_cout'],FILTER_SANITIZE_NUMBER_INT));
        $chambre_currency = filter_var($_POST['chambre_currency'],FILTER_SANITIZE_NUMBER_INT);
        $numero_chambre = trim(filter_var($_POST['numero_chambre'],FILTER_SANITIZE_NUMBER_INT));
        $chambre_nombre_lit = trim(filter_var($_POST['chambre_nombre_lit'],FILTER_SANITIZE_NUMBER_INT));
        $chambre_localisation = filter_var($_POST['chambre_localisation'],FILTER_SANITIZE_NUMBER_INT);
        $chambre_description = filter_var($_POST['chambre_description'],FILTER_SANITIZE_NUMBER_INT);
        //$chambre_intitule = filter_var($_POST['chambre_intitule'],FILTER_SANITIZE_STRING);
        $chambre_categorie = filter_var($_POST['chambre_categorie'],FILTER_SANITIZE_STRING);
        $chambre_etat_disponibilite = filter_var($_POST['chambre_etat_disponibilite'],FILTER_SANITIZE_STRING);
        $chambre_intitule = filter_var($_POST['chambre_intitule'],FILTER_SANITIZE_STRING);


        $chambre = new \App\Model\Chambres();
        $chambre->numero_chambre = $numero_chambre;
        $chambre->cout = $chambre_cout;
        $chambre->nombre_lit = $chambre_nombre_lit;
        $chambre->localisation_chambre = $chambre_localisation;
        $chambre->categorie = $chambre_categorie;
        $chambre->etat_disponibilite = $chambre_etat_disponibilite;
        $chambre->intitule_chambre = $chambre_intitule;
        $chambre->chambre_currency = $chambre_currency;
        $chambre->chambre_uniqid = $chambre_uniqid;



        //print_r($_FILES);
        //die();
        $Images = new \App\Classes\Images();
        $Images->path = 'public/upload';
        $Images->file = $_FILES;
        $Images->file_name = $chambre_uniqid;

        $_succes_message = array();
        $_error_message = array();

        try{

            http_response_code(200);
            $_succes_message = array('response_code'=>200,'string'=>'success', 'message'=>"Chambre Ajouté avec Succés");
            $chambre->Save();

            $Images->InsertImage();

            redirectUrl('chambres',$_succes_message);

        }catch (\Exception $exception){

            //print_r($exception->getMessage());
            //die();
            if (strpos($exception->getMessage(),'Integrity constraint violation')!==false){
                http_response_code(200);
                $_error_message = array('response_code'=>250,'string'=>'error',
                    'message'=>'numéro de la chambre existant. merci saisir un numéro correct.');
            }else{
                http_response_code(500);
                $_error_message = array('response_code'=>500,'string'=>'error',
                    'message'=>$exception->getMessage());
            }

            refreshSpecific('ajout_chambre',$_error_message);
        }

        break;
    default:

        render('form_chambre.html.twig', $params);
}
