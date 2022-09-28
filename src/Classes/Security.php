<?php
namespace App\Classes;

class Security

    /**
     * Security class provide methode related to security.
     *
     * @author Aristote ENGUDI
     *
     */
{

    /***
     * this methode generate an authentification key based by current date
     * @param $length
     * @return bool|string
     * @throws \Exception
     */
    public function generatAuthKey($length){

        $date_now=date('Ymdhis');

        return $this->base64UrlEncode($date_now,$length);
    }

    /***
     *
     * this methode encode a string to base64
     * @param $input
     * @param $length
     * @return bool|string
     * @throws \Exception
     */
    private function base64UrlEncode($input,$length){

        if (!is_int($length)){
            throw new \Exception('First parameter must be ($length)an integer');
        }

        if ($length < 15) {
            throw new \Exception('First parameter must be ($length) must be greather than length : '.$length);

        }

        $unique_uid=base64_encode(md5(uniqid().time().date("YmdHis").rand(999999, 999999)).$input);
        return substr(strtr(base64_encode(time().$unique_uid),'+/','-_'),0,$length);

    }

    /**
     * @param string $length
     * @param string $prefix
     * @return string
     * @throws \Exception
     * this methode return random string
     */
    public static function randomizer_sting($length='10',$prefix='zuzexzorx') {
        if (!is_int($length)){
            throw new \Exception('First parameter must be ($length)an integer');
        }

        if ($length < 3) {
            throw new \Exception('First parameter must be ($length) must be greather than length : '.$length);

        }
        $time=date('Ymd_His');
        $characters = array(
            0 => array("11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "23",
                "24", "25", "26", "27", "28", "29", "30",0,1,2,3,4,5,6,7,8,9),
            1 => array('a','b','c','e','f','g','h','i','j','k','m','n','o','p','q','s','t',
                'u','v','w','x','y','z','s','a','b','v','h','d','z','s','b','aa','bb',
                'cc','az','er','bv','nb','qs','rf','tr','qz','aris','qs','e','ds'));

        @$array  = array();

        for($i=0;$i<$length;$i++)
        {
            @$rand = rand(0, 1);
            switch($rand)
            {
                case 0: @$q = rand(0, 8);
                    break;
                case 1: @$q = rand(0, 24);
                    break;
            }
            @$array[$i] = $characters[$rand][$q];
        }
        @$uppercase = implode("", $array).$time.$prefix;

        return $uppercase;
    }

    /**
     * @param string $length
     * @param string $prefix
     * @return string
     * @throws \Exception
     * this metthode return random integer
     */
    public static function randomizer_integer($length='10',$prefix='777') {
        if (!is_int($length)){
            throw new \Exception('First parameter must be ($length)an integer');
        }

        if (!is_int($length)){
            throw new \Exception('First parameter must be ($prefix)an integer');
        }

        if ($length < 3) {
            throw new \Exception('First parameter must be ($length) must be greather than length : '.$length);

        }

        $time=date('Ymd_His');
        $characters = array(
            0 => array("11", "12", "13", "14", "15", "16", "17", "18", "19", "20",
                "21","25", "26", "27", "28", "29", "30", '1','2','3','4','5','6',
                '7','8','9','0','33'),
            1 => array('222','101','102','103','104','105','777','109','110','202',
                        '202','111','203','204','205','206','207','909','999','004',
                        '005','005','0101'));

        @$array  = array();

        for($i=0;$i<$length;$i++)
        {
            @$rand = rand(0, 1);
            switch($rand)
            {
                case 0: @$q = rand(0, 8);
                    break;
                case 1: @$q = rand(0, 24);
                    break;
            }
            @$array[$i] = $characters[$rand][$q];
        }
        @$uppercase = implode("", $array).$time.$prefix;

        return $uppercase;
    }
}