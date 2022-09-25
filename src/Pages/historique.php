<?php

if (!in_array('admin',$_SESSION['roles'])){
    $params = [
        'title' => '403 forbidden - access restricted',
        'app_full_url' => get_app_full_url()];


    render('access_denied.html.twig', $params);
    exit();
}

$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './users', 'name' => 'Historique']
];

$params = ['page_title'=>'Historique Connexion', 'breadcrumb' => $breadcrumb];

/**
 * @return mixed[]
 */
function getLog(){
    global $db;
    $check_user = $db->fetchAll('SELECT * FROM tb_logs order by  id desc');

    return $check_user;
}

$params['data']=getLog();
render('historique.html.twig', $params);