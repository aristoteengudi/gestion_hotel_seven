<?php


namespace App\Model;


use App\Classes\db;

class Chambres extends db
{
    public $numero_chambre;
    public $description;
    public $disponibilite;
    public $nombre_lit;
    public $prix;
    public $etage;



    public function Save(){

        $this->db->beginTransaction();

        try{

            $this->db->insert('t_chambres',
                array(
                    'numero_chambre'        => $this->numero_chambre,
                    'description'           => $this->description,
                    'disponibilite'         => $this->getBedroomState(),
                    'nombre_lit'            => $this->nombre_lit,
                    'prix'                  => $this->prix,
                    'localisation_chambre'  => $this->etage,
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

    public function getChambreById($id){

        $query = $this->db->fetchAllAssociative('SELECT * FROM t_chambres where id= ?',array($id));

        return $query;
    }

    public function getChambreByNummber($number){
        $query = $this->db->fetchAllAssociative('SELECT * FROM t_chambres where numero_chambre = ?',array($number));

        return $query;
    }

    public function getChambreDisponible(){
        $query = $this->db->fetchAllAssociative("SELECT * FROM t_chambres where disponibilite = 'disponible' ");

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