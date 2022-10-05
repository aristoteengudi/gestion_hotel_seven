<?php


namespace App\Model;


use App\Classes\db;
use App\Classes\UserAgent;

class Logs extends db
{
    private $db;

    public $log_id;
    public $users_id;
    public $username;
    public $status;
    public $description;



    public function save(){

        $user_agent = new UserAgent();

        $this->db->beginTransaction();
        try{
            $this->db->insert('t_logs',
                array(
                    'log_id'        =>$this->log_id,
                    'users_id'      =>$this->users_id,
                    'username'      =>$this->username,
                    'status'        =>$this->status,
                    'useragent'     =>$user_agent->Browser(),
                    'ip'            =>$user_agent->IP(),
                    'description'   =>$this->getDescription(),
                    'created_at'    =>$this->getCreatedDate(),
                    'updated_at'    =>$this->getUpdatedDate()
                )
            );

            $this->db->commit();

            return true;
        }catch (\Exception $exception){
            $this->db->rollBack();

            throw $exception;
        }
    }

    public function getAllLogs(){
        return $query = $this->db->fetchAllAssociative('
                            SELECT a.log_id, a.`users_id`,a.`username`,a.`status`,a.`description`,
                            a.`useragent`, a.`ip`,a.`created_at`,
                            CONCAT(b.`first_name`,\' \',b.`name`) AS names 
                            FROM `t_logs` a
                            INNER JOIN `t_users` b
                            ON a.users_id = b.user_id ');
    }

    public function FindLogById($log_id){}

    public function FindLogByDate($stardate, $endate){}

    private function getDescription(){ return htmlentities($this->description); }


    private function getCreatedDate(){return date('Y-m-d H:i:s');}

    private function getUpdatedDate(){ return date('Y-m-d H:i:s');}

    private function getUserAgent(){


    }

}