<?php


$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './chambres', 'name' => 'Nos Chambres']
];
$params = ['page_title'=>'Nos Chambres', 'breadcrumb' => $breadcrumb];

$action = app_request();

switch ($action){
    case 'insert':

        $numero_chambre = filter_var($_POST['numero_chambre'],FILTER_SANITIZE_NUMBER_INT);
        $prix = filter_var($_POST['prix'],FILTER_SANITIZE_NUMBER_INT);
        $nombre_lit = filter_var($_POST['nombre_lit'],FILTER_SANITIZE_NUMBER_INT);
        $localisation_chambre = filter_var($_POST['localisation_chambre'],FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);


        $chambre = new \App\Model\Chambres();
        $chambre->numero_chambre = $numero_chambre;
        $chambre->prix = $prix;
        $chambre->nombre_lit = $nombre_lit;
        $chambre->etage = $localisation_chambre;

        try{

            http_response_code(200);
            $message = array('response_code'=>200, 'message'=>"Chambre Ajouté avec Succés");
            $chambre->Save();

        }catch (\Exception $exception){

            if (strpos($exception->getMessage(),'Integrity constraint violation')!==false){
                http_response_code(200);
                $message = array('response_code'=>250,
                    'message'=>'numéro de la chambre existant. merci saisir un numéro correct.');
            }else{
                http_response_code(500);
                $message = array('response_code'=>500,
                    'message'=>$exception->getMessage());
            }
        }

        echo \GuzzleHttp\json_encode($message);

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

        $params['list_chambre'] = $chambre->getChambre();

        render('chambres.html.twig', $params);
}

