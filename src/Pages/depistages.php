<?php
if (!in_array('simple',$_SESSION['roles'])){
    $params = [
        'title' => '403 forbidden - access restricted',
        'app_full_url' => get_app_full_url()];


    render('access_denied.html.twig', $params);
    exit();
}
$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './Depistages', 'name' => 'Depistages']
];
$params = ['page_title'=>'Depistages', 'breadcrumb' => $breadcrumb];

$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){
    case 'insert':
        $message = '';

        $db->beginTransaction();
        try{
            $array = array( 'type_test'=>htmlentities($_POST['type_test']),
                            'created_at'=>$date_time,
                            'updated_at'=>$date_time,);

            $insert_data = $db->insert('tb_depistages', $array);

            $array = array();
            foreach ($_POST['group-a'] as $value){

                $array [] = array(
                                'fk_depistage_id'=>$db->lastInsertId(),
                                'fk_centre_id'=>$value['centre'],
                                'created_at'=>$date_time,
                                'updated_at'=>$date_time,);
            }

            $insert_role = new \App\BulkInsertQuery($db,'tb_details_depistage_centres');

            $insert_role->setColumns(['fk_depistage_id','fk_centre_id','created_at','updated_at'])
                ->setValues($array)->execute();

            $db->commit();

            http_response_code(200);
            $message = array('response_code'=>200,
                'message'=>"Crée");

        }catch (\Exception $exception){
            $db->rollBack();
            http_response_code(500);
            $message = array('response_code'=>500,
                'message'=>$exception->getMessage());

        }

        echo \GuzzleHttp\json_encode($message);
        break;
    case 'edit':
        $db->beginTransaction();
        try{
            $array = array(
                @htmlentities($_POST['nom_centre']),
                @htmlentities($_POST['groupe_sanguin']),
                @htmlentities($_POST['ville']),
                @htmlentities($_POST['district']),
                @htmlentities($_POST['adresse']),
                $date_time,
                @$_POST['id']);

            $db->executeUpdate('UPDATE tb_banque_sang SET nom_centre = ?,groupe_sanguin = ?,ville = ?,
                                district = ?,adresse = ?,updated_at = ? WHERE id = ?',$array);
            $db->commit();

            http_response_code(200);
            $message = array('response_code'=>200,
                'message'=>"mise à jour réussi");

            echo \GuzzleHttp\json_encode($message);
        }catch (\Exception $exception){
            $db->rollBack();
            http_response_code(500);
            $message = array('response_code'=>500,'message'=>'Erreur survenus');
            echo \GuzzleHttp\json_encode($message);
        }
        break;

    case 'batch_form':

        $db->beginTransaction();
        try{

            $post_centre_batch = $_POST['centre_batch'];
            $file_name = explode('.',$_FILES['batch']['name']);
            $directory = '/var/uploads/';
            $uploader = new \App\Uploader($_FILES['batch'],$file_name[0],$directory);
            $uploader_ = $uploader->upload();
            $array_explode = array();

            if (($handle = fopen(__DIR__.'/../..'.$directory.$uploader_, "r")) !== FALSE) {
                while (($data = fgets($handle)) !== FALSE) {
                    $trim = trim($data);

                    $array_explode [] = (explode( ";", $trim ));
                }
                fclose($handle);
            }

            $array_shift = array_shift($array_explode); // move first element to $array_shift

            $count = 0;
            $array_insert_bulk = array();

            //print_r($array_explode);
            //die();
            foreach ($array_explode as $value) {

                list($type_depistage, $cost) = $value;
                /**/
                $array = array( 'type_test'=>$type_depistage,
                    'cost'=>$cost,
                    'created_at'=>$date_time,
                    'updated_at'=>$date_time,);
                $insert_ = $db->insert('tb_depistages',$array);

                $insert_detail = array('fk_depistage_id'=>$db->lastInsertId(),'fk_centre_id'=>$post_centre_batch,'created_at'=>$date_time,'updated_at'=>$date_time);
                $insert_ = $db->insert('tb_details_depistage_centres',$insert_detail);
            }
            $db->commit();

            http_response_code(200);
            $message = array('response_code'=>200,'message'=>'Ajouté');
        }catch (\Exception $exception){
            $db->rollBack();
            http_response_code(200);
            $message = array('response_code'=>500,'message'=>$exception->getMessage());
        }

        echo \GuzzleHttp\json_encode($message);
        break;

    case 'delete':
        $post_id = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);

        $array = array($post_id['id']);

        $deleted = $db->executeUpdate('DELETE FROM  tb_depistages WHERE id = ?',$array);
        $deleted = $db->executeUpdate('DELETE FROM  tb_details_depistage_centres WHERE fk_depistage_id = ?',$array);

        if ($deleted){

            http_response_code(200);
            $message = array('response_code'=>200,
                'message'=>"Suppression réussi");

            echo \GuzzleHttp\json_encode($message);
        }else{
            http_response_code(500);
            $message = array('response_code'=>500,'message'=>'Erreur survenus');
            echo \GuzzleHttp\json_encode($message);
        }
        break;
    case 'view':

        $post_id = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);

        $query = $db->fetchAssoc('SELECT * FROM tb_banque_sang
                                    WHERE id = ?',array($post_id['id']));

        $raw_html = "<div class=\"card-body\">
                            <div class=\"form-group row\">
                                <div class=\"col-sm-10\">
                                <span id='example-text-input\'><b style='font-weight: bold'>nom_centre</b> : {$query['nom_centre']}</span>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <div class=\"col-sm-10\">
                                <span id='example-text-input\'><b style='font-weight: bold'>ville</b> : {$query['ville']}</span>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <div class=\"col-sm-10\">
                                <span id='example-text-input\'><b style='font-weight: bold'>district</b> : {$query['district']}</span>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <div class=\"col-sm-10\">
                                <span id='example-text-input\'><b style='font-weight: bold'>adresse</b> : {$query['adresse']}</span>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <div class=\"col-sm-10\">
                                <span id='example-text-input\'><b style='font-weight: bold'>groupe_sanguin</b> : {$query['groupe_sanguin']}</span>
                                </div>
                            </div>
                        </div>";
        $response  = \GuzzleHttp\json_encode(array('message' => $raw_html));
        echo $response;
        break;
    case 'getdata':

        $post_id = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);

        $query = $db->fetchAssoc('SELECT * FROM tb_medicaments
                                    WHERE id = ?',array($post_id['id']));

        $query_centres_by_id= $db->fetchAll("SELECT a.id , b.nom_centre,b.adresse, b.id AS id_centre
                                FROM tb_details_medicament_centres a
                                LEFT JOIN `tb_centres` b ON a.`fk_centre_id` = b.`id`
                                WHERE a.fk_medicament_id = ? ORDER BY a.`id` desc", array($post_id['id']));

        $query_centres = $db->fetchAll('SELECT * FROM tb_centres');


        $raw_html = "<div class=\"card-body\">
                            <div class=\"form-group row\">
                                <label for=\"example-text-input\" class=\"col-sm-4 col-form-label\">nom_medicament</label>
                                <div class=\"col-sm-8\">
                                    <input type=\"text\" class=\"form-control\" value='{$query['nom_medicament']}' name=\"nom_medicament\" required>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"example-text-input\" class=\"col-sm-4 col-form-label\">description</label>
                                <div class=\"col-sm-8\">
                                    <textarea name=\"description\"  class=\"form-control\" id=\"\" cols=\"30\" rows=\"3\">{$query['description']}</textarea>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"example-text-input\" class=\"col-sm-8 col-form-label\">coût</label>
                                <div class=\"col-sm-4\">
                                    <input type=\"text\" class=\"form-control\" value='{$query['cost']}' name=\"cost\" required>
                                </div>
                            </div>";

        $raw_html .="<div class=\"form-group row\">
                                <label for=\"example-text-input\" class=\"col-sm-4 col-form-label\">Centre existant</label>
                                <div class=\"col-sm-8\">";
        foreach ($query_centres_by_id as $centre){
            $raw_html .="<input type=\"text\" class=\"form-control\" value='{$centre['nom_centre']}' required disabled>";
        }
        $raw_html .="</div>
                            </div>";

        $raw_html .="<div data-repeater-list=\"group-a\">
                                <div data-repeater-item class=\"row\">
                                    <div class=\"form-group col-lg-8\">
                                        <label for=\"centre\">Centres</label>
                                        ";

        $raw_html .="<select name=\"centre\" id=\"centre\" class=\"form-control\"><option value=\"\">...</option>";
        foreach ($query_centres as $centres){
            $raw_html .="<option value=\"{$centres['id']}\">{$centres['nom_centre']} / {$centres['adresse']}</option>";
        }

        $raw_html .="</select></div> <div class=\"col-lg-3 align-self-center\">
                                        <input data-repeater-delete type=\"button\" class=\"btn btn-primary btn-block\" value=\"Delete\"/>
                                    </div>
                                </div>

                            </div>
                            <input data-repeater-create type=\"button\" class=\"btn btn-success mo-mt-2\" value=\"Add\"/>
                        </div>";

        $response  = \GuzzleHttp\json_encode(array('message' => $raw_html));
        echo $response;
        break;

    default:
        function getAll(){
            global $db;

            $query = $db->fetchAll("SELECT * FROM tb_depistages ORDER BY id DESC");
            return $query;
        }

        function getCentres(){
            global $db;

            $query = $db->fetchAll("SELECT * FROM tb_centres ORDER BY id DESC");
            return $query;
        }

        $params['data']=getAll();
        $params['data_two']=getCentres();

        render('depistages.html.twig', $params);
}

