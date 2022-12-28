<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use App\Utils;
use App\Paginator;

class ExtensionTwigFunctions extends AbstractExtension
{
    
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('site_url',  [$this, 'getSiteUrl']),
            new \Twig\TwigFunction('path',  [$this, 'getPath']),
            new \Twig\TwigFunction('get_config',  [$this, 'getConfig']),
            new \Twig\TwigFunction('paginator',  [$this, 'paginator']),
            new \Twig\TwigFunction('http_params',  [$this, 'getHttpParams']),
            new \Twig\TwigFunction('get_certificat',  [$this, 'getCertificat']),
            new \Twig\TwigFunction('unset_array',  [$this, 'UnsetTwigArray']),
            new \Twig\TwigFunction('unset_flash',  [$this, 'unsetFlash']),
        ];
    }
    
    public function getSiteUrl(){
        echo Utils::siteUrl();
    }


    public function UnsetTwigArray($args){

        unset($_SESSION[$args]) ;
    }
    public function unsetFlash(){

        unset($_SESSION['flash_message']);
    }

    public function getPath($url, $params = null){
        $url_params = "";
        if($params){
            foreach ($params as $key => $value) {
                $url_params .=($url_params)?"&":""."$key=$value";
            }
        }
        $url_params =($url_params)?"?$url_params":"";
        return  Utils::siteUrl().$url.$url_params;
    }
    
    public function getConfig($tree){
        global $config;
        $conf = $config;
        if(is_array($tree)){
            foreach ($tree as $key => $item) {
                $conf = $conf[$item];
            }
        }elseif(is_string($tree)){
            $conf = $conf[$tree];
        }
        return $conf;
    }
    
    public function getHttpParams($param=null){    
        if($param){
            return $_REQUEST[$param];
        }else{
            unset($_REQUEST['page']);
            unset($_REQUEST['submitBatchMsisdn']);
            return $_REQUEST;
        }
    }
    
    public function getCertificat($file){
        global $config;
        $input = $config['General']['root']."/".$file;
        $path_parts = pathinfo($input);
        $output = $path_parts['filename'];
        
        try {
            if(!fsockopen("127.0.0.1",4000)){
                $command = "nohup node server.js > var/logs/node-server.log &";
                system($command);
            }
            $url ="http://127.0.0.1:4000/?input=$input&output=$output";
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);
            $body = $response->getBody();
            $content = $body->getContents();
            $content = json_decode($content, true);
            $file = $content['message'][0]['dir']."/".$content['message'][0]['name'];
            $img = $this->getPath($file);
            return $img;
            
        } catch (Exception $e) {
            return;
        }
    }
    
    
    public function paginator($items_total, $default_ipp=10, $mid_range=9){
        $pages = new Paginator;
        $pages->default_ipp = $default_ipp;
        $pages->items_total = $items_total;
        $pages->mid_range   = $mid_range;
        $pages->paginate();	
        
        if($pages->items_total > 0) {
	       echo $pages->display_pages();
	       echo $pages->display_items_per_page();
	       //echo $pages->display_jump_menu();
        }
    }
}

