<?php

use GuzzleHttp\Client as Clients;
use App\Classes\Security;
use App\Classes\BulkInsertQuery;
use App\Model\Users;


$params = ['title'=>'Login - Dashboard Anemiapp'];


$http_client = new Clients();

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$STATUS_ACCOUNT_ACTIVED     = $config ['STATUS_ACCOUNT_ACTIVED'] ;
$DEFAULT_STATUS             = $config['DEFAULT_ACCOUNT_STATUS'] ;
$MESSAGE_ACCOUNT_DEACTIVED  = $config['MESSAGE_ACCOUNT_DEACTIVED'] ;
$AUTHENTIFICATION_SUCCESS   = $config['AUTHENTIFICATION_SUCCESS'];

$login = new Users();



//echo '<pre style="margin:100px 0 0 100px;">';print_r($status_active);echo '</pre>';

$date_now = date('Y-m-d H:i:s');

if (empty($username) and empty($password)){

    render('login.html.twig', $params);

} else {


    if (!empty($login->VerifyUsersExistByUsername($username))) {

        if ($check_user=$login->Login($username,$password)){

            if ($check_user['status'] != $STATUS_ACCOUNT_ACTIVED){

                $array = array(
                    'users_id'          =>  $check_user['user_id'],
                    'username'          =>  $username,
                    'status'            =>  $DEFAULT_STATUS,
                    'description'       =>  $MESSAGE_ACCOUNT_DEACTIVED,
                    'created_at'        =>  $date_now,
                    'updated_at'        =>  $date_now,
                );


                $logs = new \App\Model\Logs();
                $logs->username = $check_user['username'];
                $logs->users_id = $check_user['user_id'];
                $logs->status   = $DEFAULT_STATUS;
                $logs->description = $MESSAGE_ACCOUNT_DEACTIVED ;
                $logs->save();

                $params['error']=['message' => "<strong>{$MESSAGE_ACCOUNT_DEACTIVED}</strong> "];

                render('login.html.twig', $params);

            }else{

                $_SESSION['unique_id']  = $check_user['uid'];
                $_SESSION['username']   = $check_user['username'];
                $_SESSION['first_name']  = $check_user['first_name'];
                $_SESSION['user_id']    = $check_user['user_id'];
                $_SESSION['name']       = $check_user['name'];
               // $_SESSION['roles']      = roles($check_user['id']);

                $array = array(
                    'users_id'          =>  $check_user['user_id'],
                    'username'          =>  $check_user['username'],
                    'status'            =>  $STATUS_ACCOUNT_ACTIVED,
                    'description'       =>  $AUTHENTIFICATION_SUCCESS,
                    'created_at'        =>  $date_now,
                    'updated_at'        =>  $date_now,
                );

                $logs = new \App\Model\Logs();
                $logs->username = $check_user['username'];
                $logs->users_id = $check_user['user_id'];
                $logs->status   = $STATUS_ACCOUNT_ACTIVED;
                $logs->description = $AUTHENTIFICATION_SUCCESS ;
                $logs->save();

                header("LOCATION: ".getBaseUrl());
            }


        }else{
            $params['error']=['message'=>'<strong>Oh snap!</strong> imposible de se connecter <br><br> 
                                    mot de passe ou username incorrect'];

            render('login.html.twig', $params);
        }

    }else{

        $params['error']=['message'=>'<strong>Oh snap!</strong> imposible de se connecter <br><br> 
                                    vous ne disposez pas encore d\'un compte.'];

        render('login.html.twig', $params);
    }

}



/**
 * get user roles by her id AI
 *
 * @param $user_id
 * @return array
 */
function roles($user_id){

    global $db;

    $roles = array();
    $querys = $db->fetchAll('SELECT * FROM tb_roles WHERE users_id = ? and status = 1 ',array($user_id));

    foreach ($querys as $query){

        $roles [] = $query['role'];
    }

    return $roles;

}