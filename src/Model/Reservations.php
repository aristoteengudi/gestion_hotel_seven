<?php

namespace App\Model;


use App\Chart\ChartOptions;
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
    public $cout;
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
                    'cout'=> $this->cout,
                    'status_reservation'=> 'actif',
                    'client_uniqid'=> $this->client_uniqid,
                    'nombre_personne'=> $this->nombre_personne,
                    'created_at'=>$this->getCreatedDate(),
                    'updated_at'=>$this->getUpdatedDate()
                )); // save reservations



            $this->db->update('t_chambres',
                array('etat_disponibilite'=>'indisponible'),
                array('chambre_uniqid'=>$this->chambre_uniqid)); // update chambre state

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

    public function getCurrentMonthReservationAmount(){

        $sql  = "SELECT  SUM(r.cout) AS montant_total_reservation_mensuel,ch.chambre_currency
                    FROM 
                    `t_reservations` r JOIN t_chambres ch
                    ON r.chambre_uniqid = ch.chambre_uniqid
                    WHERE MONTH(r.created_at) = MONTH(NOW())
                    GROUP BY chambre_currency";

        $query = $this->db->fetchAllAssociative($sql);

        return $query;

    }

    public function getTotalClient(){

        $query = $this->db->fetchAssociative('SELECT count(*) as count_total_clients FROM t_clients');

        return $query;
    }
    private function getReservationUid(){ return Security::randomizer_integer(10,979);}
    private function getCreatedDate(){ return date('Y-m-d H:i:s');}


    public function getReservations(){

        $query = $this->db->fetchAllAssociative("SELECT  r.reservation_id,c.noms,c.prenom,c.telephone,c.genre,c.client_uniqid,r.stardate,r.endate,
                                                        r.times,r.chambre_uniqid,r.status_reservation,r.nombre_personne,ch.chambre_currency,
                                                        r.created_at,r.updated_at, ch.numero_chambre,ch.localisation_chambre,
                                                        r.cout
                                                        
                                                        FROM
                                                        
                                                        `t_clients` c JOIN `t_reservations` r 
                                                        
                                                        ON r.client_uniqid = c.client_uniqid
                                                        JOIN t_chambres ch
                                                        ON r.chambre_uniqid = ch.chambre_uniqid");

        return $query;
    }

    private function getUpdatedDate(){ return date('Y-m-d H:i:s');}


    public function getChartHistogramReservMonth(){

        $get = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);
        $start_date = str_replace('/','-',$get['start_date']);
        $start_date = date('Y-m-d',strtotime($start_date));

        $end_date = str_replace('/','-',$get['end_date']);
        $end_date = date('Y-m-d',strtotime($end_date));
        $array = array($start_date,"$end_date 23:59:59");

        $query = $this->db->fetchAllAssociative("select count(*) as count_, `ocs_result_code`,`ocs_result_desc`,
                                case `ocs_result_code`
                                    when '0' then 'Réussi'
                                    when 'S-ACT-00112' then 'Balance insuffisant'
                                    when 'S-DAT-00021' then 'Echec mise à jour requête'
                                    else 'Echec'
                                end as state
                                
                                from `tb_logs_facturation` where `created_at` between ? and ? 
                                group by state",$array);

        $new_array = array();
        $series_name = array();
        $details = '';

        if ($get['start_date']==$get['end_date']){
            $details = "Rapport du {$get['start_date']}";

        }else{
            $details = "Rapport du {$get['start_date']} au {$get['end_date']}";
        }

        $chart = [];
        $chart['container'] = "facturation_histogram";
        $cOptions = new ChartOptions();
        $cOptions->setChart("column",false,null,null);
        $cOptions->setTitle($details);
        $cOptions->setShadow(['text'=>false]);
        $cOptions->setTooltip(['pointFormat' =>'{series.name}: <b>{point.y}</b>','headerFormat'=>'<b>{series.name}</b><br><br>']);
        $cOptions->setXAxis('','',false);
        $cOptions->setYAxis('',['text'=>'Valeur']);
        //$cOptions->setAccessibility(['point'=>['valueSuffix'=> '%']]);
        $cOptions->setPlotOptions( [
            'series'=>[
                'dataLabels'=>[
                    'enabled'=>true,
                    'color'=>'#000',
                    'style'=>['fontWeight'=>'bolder'],
                    'inside' =>true,
                ],
                'pointPadding'=>'0.1',
                'groupPadding'=>'0',
            ]
        ]);
        foreach ($query as $value){
            $cOptions->appendSeries((string)$value['state'],[(int)$value['count_']],false);

            //$new_array [] = array('name'=>(string)$value['region_name'],'data'=>[(int)$value['total']]);
        }

        $options = $cOptions->getOptions();

        $chart = array_merge($chart, $options);


        $response  = \GuzzleHttp\json_encode($chart);
    }
}