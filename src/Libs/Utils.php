<?php

namespace App;

class Utils
{
    
    static function siteUrl(){
        return  "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}".substr($_SERVER['PHP_SELF'] ,0,strpos($_SERVER['PHP_SELF'],'index.php'));
    }
}
