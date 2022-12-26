<?php

/**
 * @return bool|string
 * get base url of application
 */
function getBaseUrl() {

//    $url_= $_SERVER['SERVER_NAME'];
    $url_= $_SERVER['PHP_SELF'];
    return substr($url_ ,0,strpos($url_,"index.php"));

}


function redirect() {

    header("LOCATION: ".getBaseUrl());
}

/***
 * @return string
 * get full url of application
 */
function get_app_full_url(){
    $url_= $_SERVER['PHP_SELF'];
    $server_name = $_SERVER['SERVER_NAME'];
    $substring = substr($url_ ,0,strpos($url_,"index.php"));
    $full_url = $server_name.$substring;
    return $full_url;
}

/**
 * @return mixed|string
 * get user ip
 */
function get_user_ip(){
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        @$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        @$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        @$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        @$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        @$ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        @$ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        @$ipaddress = 'UNKNOWN';

    return $ipaddress;
}

/**
 * @return mixed
 * get app request
 */
function app_request(){

    extract($_REQUEST);

    $route = '';

    if (isset($request)){
        $route = $request;
    }elseif (isset($r)){
        $route = $r;
    }

    return $route;

}

function get_user_info($info_name){

    $info = '';

    if ($info_name=='unique_id') {

        $info = @$_SESSION['unique_id'];

    }elseif ($info_name=='fullname'){

        $info = @$_SESSION['fullname'];

    }elseif ($info_name=='firstname'){

        $info = @$_SESSION['firstname'];

    }elseif ($info_name == 'user_id'){

        $info = @$_SESSION['user_id'];

    }elseif ($info_name =='name') {

        $info = @$_SESSION['name'];
    }else{
        $info;
    }

    return $info;

}