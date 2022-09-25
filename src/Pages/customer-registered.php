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
    [ 'path' => './customer-registered', 'name' => 'Customer Registered']
];
$params = ['page_title'=>'Customer Registered', 'breadcrumb' => $breadcrumb];


function get_all_customer(){
    global $db;
    $customer = $db->fetchAll('SELECT * FROM tb_registers order by  id desc');

    return $customer;
}
$params['data']=get_all_customer();

render('customer-registered.html.twig', $params);