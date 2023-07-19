<?php


namespace App\Model;

use App\Classes\db;


class Customers extends db
{


    public function getAllCustomers(){

        $query = $this->db->fetchAllAssociative("select * from `t_clients`");

        return $query;
    }

    public function getCustomerByDate(){

    }

}