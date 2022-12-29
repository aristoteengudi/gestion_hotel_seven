<?php


namespace App\Model;


use App\Classes\db;

class Chambres extends db
{
    public $numero_chambre;
    public $description;
    public $etat_disponibilite;
    public $localisation_chambre;
    public $chambre_uniqid;
    public $intitule_chambre;
    public $categorie;
    public $chambre_currency;
    public $nombre_lit;
    public $cout;




    public function Save(){

        $this->db->beginTransaction();

        try{

            $this->db->insert('t_chambres',
                array(
                    'numero_chambre'        => $this->numero_chambre,
                    'description'           => $this->description,
                    'etat_disponibilite'         => $this->getBedroomState(),
                    'nombre_lit'            => $this->nombre_lit,
                    'cout'                  => $this->cout,
                    'localisation_chambre'  => $this->localisation_chambre,
                    'categorie'         => $this->categorie,
                    'chambre_uniqid'         => $this->chambre_uniqid,
                    'intitule_chambre'         => $this->intitule_chambre,
                    'chambre_currency'         => $this->chambre_currency,
                    'created_at'            => $this->getCreatedAt(),
                    'updated_at'            => $this->getUpdatedAt()));

            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getChambre(){

        $query = $this->db->fetchAllAssociative('SELECT * FROM t_chambres');

        return $query;
    }

    public function getChambreWithOtherDetails(){

        $query = $this->db->fetchAllAssociative('select * 
                                                        from 
                                                        `t_chambres`
                                                        
                                                        join `t_images` 
                                                        on t_images.fk_chambre_uniqid = t_chambres.chambre_uniqid ;');

        return $query;
        
    }


    public function getChambreWithOtherDetailsByStatus($status,$start_date,$end_date){

        $status = trim(filter_var($status,FILTER_SANITIZE_STRING));
        $start_date = str_replace('/','-',trim(filter_var($start_date,FILTER_SANITIZE_STRING)));
        $start_date = date('Y-m-d',strtotime($start_date));


        $end_date = str_replace('/','-',trim(filter_var($end_date,FILTER_SANITIZE_STRING)));
        $end_date = trim(filter_var($end_date,FILTER_SANITIZE_STRING));
        $end_date = date('Y-m-d H:i:s',strtotime($end_date));;


        $query = $this->db->fetchAllAssociative("select * 
                                                        from 
                                                        `t_chambres`
                                                        
                                                        join `t_images` 
                                                        on t_images.fk_chambre_uniqid = t_chambres.chambre_uniqid 
                                                        where t_chambres.created_at BETWEEN '{$start_date}' AND '{$end_date}'
                                                        and t_chambres.etat_disponibilite = '{$status}';");

        return $query;

    }

    public function getChambreById($id){

        $query = $this->db->fetchAllAssociative('SELECT * FROM t_chambres where id= ?',array($id));

        return $query;
    }

    public function getChambreByUniqId($id){

        $query = $this->db->fetchAssociative('SELECT * FROM t_chambres where chambre_uniqid= ?',array($id));

        return $query;
    }

    public function getChambreByNummber($numberOrUniqId){
        $query = $this->db->fetchAssociative('SELECT * FROM t_chambres where numero_chambre = ? or chambre_uniqid = ?',array($numberOrUniqId,$numberOrUniqId));

        return $query;
    }

    public function getChambreDisponible(){
        $query = $this->db->fetchAllAssociative("SELECT * FROM t_chambres where etat_disponibilite = 'disponible' ");

        return $query;
    }


    public function getTotalChambreDisponible(){
        $query = $this->db->fetchAssociative("SELECT count(*) as count_total_chambre_disponible FROM t_chambres where etat_disponibilite = 'disponible'");

        return $query;
    }

    private function getCreatedAt(){
        return date('Y-m-d H:i:s');

    }

    private function getUpdatedAt(){

        return date('Y-m-d H:i:s');
    }

    private function getBedroomState(){

        return 'disponible';
    }

}