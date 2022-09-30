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
    [ 'path' => './users', 'name' => 'Utilisateurs']
];
$params = ['page_title'=>'Tous les utilisateurs', 'breadcrumb' => $breadcrumb];

$action = app_request();
$date_time = date('Y-m-d H:i:s');

switch ($action){
    case 'edit':

        $message = array();
        $db->beginTransaction();
        try{
            $role_name = $_POST['role'];
            $role_id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $password_ = $_POST['password'];

            if (empty($password_)){
                $db_password = $db->fetchColumn('SELECT password FROM tb_users WHERE id = ?',array($user_id));
                $password_hash = $db_password;
            }else{
                $password_hash = password_hash($password_,PASSWORD_DEFAULT);
            }

            $status_compte = isset($_POST['status_compte']) ? $_POST['status_compte'] : '0';

            $array = array($status_compte,$password_hash,$date_time,$user_id);
            $db->executeUpdate('UPDATE tb_users SET status = ?, password = ?, updated_at = ? WHERE id = ?',$array);

            $count = 0;
            foreach ($role_id as $key=>$value){

                $status = '';

                if (array_key_exists($value,$role_name)){
                    $status = 1;
                }else{
                    $status = 0;
                    $count++;
                }
                $array = array($status,$value,$user_id);
                $db->executeUpdate('UPDATE tb_roles SET status = ?  WHERE id = ? AND users_id = ?',$array);


            }

            $db->commit();

            http_response_code(200);
            $message = array('response_code'=>200,'message'=>'mise à jours réussi');


        }catch (\Exception $exception){
            $db->rollBack();
            http_response_code(500);
            $message = array('response_code'=>500,'message'=>$exception->getMessage());
        }
        echo \GuzzleHttp\json_encode($message);
        break;
    case 'getdata':

        $post_id = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);

        $query = $db->fetchAssoc('SELECT * FROM tb_users WHERE id = ?',array($post_id['id']));

        $query_roles = $db->fetchAll('SELECT * FROM tb_roles WHERE users_id = ?',array($query['id']));

        $status = $query['status']==1 ? '1' : '0';
        $checked_string = $query['status']==1 ? "checked" : '';

        $raw_html =" <div class=\"card-body\">
                            <div class=\"form-row\">
                                <div class=\"form-group col-md-6\">
                                    <label for=\"inputEmail4\">Nom Complet</label>
                                    <input type=\"text\" class=\"form-control\" id=\"\" value='{$query['fullname']}'  disabled>
                                </div>
                                <div class=\"form-group col-md-6\">
                                    <label for=\"inputPassword4\">Nom</label>
                                    <input type=\"text\" class=\"form-control\" id=\"inputPassword4\" value='{$query['name']}'  disabled>
                                </div>
                            </div>
                            <div class=\"form-row\">
                                <div class=\"form-group col-md-6\">
                                    <label for=\"inputEmail4\">Prénom</label>
                                    <input type=\"text\" class=\"form-control\" id=\"inputEmail4\" value='{$query['firstname']}'  disabled>
                                </div>
                                <div class=\"form-group col-md-6\">
                                    <label for=\"inputPassword4\">Email</label>
                                    <input type=\"text\" class=\"form-control\" id=\"\" value='{$query['email']}'  disabled>
                                </div>
                            </div>
                            <div class=\"form-row\">
                                <div class=\"form-group col-md-6\">
                                    <label for=\"inputEmail4\">Cuid</label>
                                    <input type=\"text\" class=\"form-control\" id=\"inputEmail4\" value='{$query['username']}'  disabled>
                                </div>
                                <div class=\"form-group col-md-6\">
                                    <label for=\"inputEmail4\">Phone</label>
                                    <input type=\"text\" class=\"form-control\" id=\"\" value='{$query['phone_number']}'  disabled>
                                    <input type=\"hidden\" class=\"form-control\" id=\"\" name='user_id' value='{$query['id']}'>
                                </div>
                            </div>
                            ";

        if (!empty($query['password'])) {
            $raw_html .= "<div class=\"form-group row\">
                                <label for=\"example-text-input\" class=\"col-sm-5 col-form-label\">mot de passe</label>
                                <div class=\"col-sm-7\">
                                    <input type=\"text\" class=\"form-control\" name=\"password\" required>
                                </div>
                            </div>";
        }
        $raw_html .= "<div class=\"form-row\">
                                <div class=\"col-md-2\">
                                    <label for=\"status_compte\">Status Compte</label>
                                </div>
                                <div class=\"col-md-1\">
                                    <input type=\"checkbox\" id=\"status_compte\" name ='status_compte' switch=\"primary\" value='{$status}' {$checked_string}>
                                    <label for=\"status_compte\" data-on-label=\"Active\" data-off-label=\"Inactive\"></label>
                                </div>
                            </div>";

        $i = 0;
        $raw_html .="<div class=\"form-row\">";
        foreach ($query_roles as $role){
            $i++;
            $checked = $role['status']==1 ? 'checked' : '';
            $raw_html .=" <div class=\"col-md-1\">
                                    <label for=\"role_{$i}\">{$role['role']}</label>
                                </div>
                                <div class=\"col-md-1\">
                                    <input type=\"checkbox\" id=\"role_{$i}\" switch=\"primary\" value='{$role['status']}' name='role[{$role['id']}]' {$checked}>
                                    <label for=\"role_{$i}\" data-on-label=\"Yes\" data-off-label=\"No\"></label>
                                    <input type=\"hidden\" value='{$role['id']}' name='id[{$role['id']}]'> 

                                </div>";

            $script_js = "<script>
        $('#role_{$i}').change(function () {
            let role=$('#role_{$i}');
            if (role.is(':checked')){
                $('#role_{$i}').val('1');
            }else if(role.is(':checked')==false){
                $('#role_role_{$i}').val('0');
            }

        });
        </script>";
            $raw_html .=$script_js;
        }
        $raw_html .='</div>';


        $script_js = "<script>$('#status_compte').change(function () {
            let status=$('#status_compte');

            if (status.is(':checked')){
                $('#status_compte').val('1');
            }else if(status.is(':checked')==false){
                $('#status_compte').val('0');
            }

        });
        </script>";
        $raw_html .=$script_js;
        echo \GuzzleHttp\json_encode(array('message'=>$raw_html));
        break;
    case 'create_users':
        $message = array();

        break;
    case 'deactivate':


        break;
    default:

        $user = new \App\Model\Users();

        $params ['list_users'] = $user->getAllUsers();

        render('users.html.twig', $params);
}
