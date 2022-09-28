<?php


namespace App\Model;

use App\Classes\Security;

class Users
{

    private $user_id;
    public $username;
    public $name;
    public $first_name ;
    public $email;
    public $phone_number;
    public $uid;
    public $status;
    public $password_hash ;

    private $created_at;
    private $updated_at;


    private $db;

    public function __construct()
    {
        global $db;

        $this->db = $db ;
    }

    public function CreateUser(){

        $this->db->beginTransaction();

        try{

            $this->db->insert('t_users',
                array(
                    'username'      => $this->username,
                    'name'          => $this->name,
                    'first_name'    => $this->first_name,
                    'email'         => $this->email,
                    'phone_number'  => $this->phone_number,
                    'uid'           => $this->getUid(),
                    'status'        => $this->status,
                    'created_at'    => $this->getCreatedAt(),
                    'update_at'     => $this->getUpdatedAt(),
                    'password_hash' =>$this->getPasswordHash()));

            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

    }

    public function UpdateUser(){

    }

    public function getAllUsers(){

    }

    public function Login($username, $password){

        $query = $this->db->fetchAssociative('SELECT * FROM t_users WHERE username = ?', array($username));

        $password_verify = password_verify($password,$query['password_hash']);

        if (!$password_verify){
            return false;
        }
        return $query;
    }

    public function VerifyUsersExistByUsername($username){

        return $this->db->fetchAssociative('SELECT * FROM t_users WHERE username = ?', array($username));

    }

    /**
     * @return mixed
     */
    private function getCreatedAt()
    {
        return $this->created_at = date('Y-m-d H:i:s');
    }

    private function getUpdatedAt()
    {
        return $this->updated_at = date('Y-m-d H:i:s');
    }



    /**
     * @return mixed
     */
    private function getPasswordHash()
    {
        return $password = password_hash($this->password_hash,PASSWORD_DEFAULT);
    }

    private function getUid(){

         $uid = new Security();

         return $uid->generatAuthKey(15);
    }




}