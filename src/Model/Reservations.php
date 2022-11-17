<?php

namespace App\Model;


use App\Classes\db;
use App\Classes\Security;


class Reservations extends db
{
    private $reservation_id;
    public $stardate;
    public $endate;
    public $times;
    public $reservation_uid;
    public $chambre_uniqid;
    public $prix;
    public $status_reservation;
    public $client_uniqid;
    public $nombre_personne;



    public function createReservation(){

        $this->db->beginTransaction();

        try{

            $this->db->insert('t_reservations',
                array(
                    'stardate'=> $this->stardate,
                    'endate'=> $this->endate,
                    //'times' => '',
                    'reservation_uid'=> $this->getReservationUid(),
                    'chambre_uniqid'=> $this->chambre_uniqid,
                    'prix'=> $this->prix,
                    'status_reservation'=> 'actif',
                    'client_uniqid'=> $this->client_uniqid,
                    'nombre_personne'=> $this->nombre_personne,
                    'created_at'=>$this->getCreatedDate(),
                    'updated_at'=>$this->getUpdatedDate()
                )); // save reservations



            $this->db->update('t_chambres',
                array('disponibilite'=>'indisponible'),
                array('numero_chambre'=>$this->chambre_uniqid)); // update chambre state

            $this->db->commit();

        }catch (\Exception $exception){
            $this->db->rollBack();
            throw $exception;

        }
    }


    private function getStarDate(){}
    private function getEndDate(){}

    public function getTotalReservation(){

        $query = $this->db->fetchAssociative('SELECT count(*) as count_total_reservation FROM t_reservations');

        return $query;

    }

    public function getTotalClient(){

        $query = $this->db->fetchAssociative('SELECT count(*) as count_total_clients FROM t_clients');

        return $query;
    }
    private function getReservationUid(){ return Security::randomizer_integer(10,979);}
    private function getCreatedDate(){ return date('Y-mc-d H:i:s');}

    private function getUpdatedDate(){ return date('Y-m-d H:i:s');}
}