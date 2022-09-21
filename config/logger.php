<?php
/***
 * fichier permettant a generer le logs d'erreur
 */


ini_set("soap.wsdl_cache_enabled",0);
ini_set("soap.wsdl_cache_ttl",0);

function Logger(){
    if (!file_exists(__DIR__.'/../var/logs/')){
        mkdir(__DIR__.'/../var/logs',0777,true);
    }

    ini_set('display_errors', '1');
    ini_set('log_errors',1);
    ini_set('error_log',__DIR__.'/../var/logs/error_log_'.date('Y-m-d').'.log');
    error_reporting(E_ALL);

}

function send_error_sms($message=null,array $context=null,$application=null){

    $wsdl = "http://10.25.3.122/ordc-api/soap/sms.php?wsdl";
    $sms = new \SoapClient($wsdl);
    $sender="ErrorAp OCD";
    $date = date('Y-m-d H:i:s');
    $message_ = @remove_accents(utf8_encode($message));


    $text = "$date $message_   \n\n APP: $application";

    $result = $sms->sendSms(array("receiver" => '0843186955', "sender" => $sender, "text" => $text));

}


function remove_accents($string){

    $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
    $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
    $replaced = str_replace($search, $replace, $string);

    return $replaced;

}
Logger();