<?php


namespace App\Model;


use App\Classes\db;
use App\Classes\Security;

class Transactions extends db
{

    private $transaction_id;
    public $transaction_type;
    public $transaction_montant;
    public $transaction_description;
    public $fk_reservation_uid;
    public $fk_expens_uniqid ;
    private $uniqid_id;


    public function insertTransactions(){
        $this->db->beginTransaction();

        try{

            $this->db->insert('t_transactions',
                array(
                    'uniqid'=> $this->getTransactionUid(),
                    'trans_type'=> $this->transaction_type,
                    'trans_montant'=> $this->transaction_montant,
                    'trans_description'=> $this->transaction_description,
                    'fk_reservation_uid'=> $this->fk_reservation_uid,
                    'fk_expens_uniqid'=> $this->fk_expens_uniqid,
                    'trans_created_at'=>$this->getCreatedDate(),
                    'trans_updated_at'=>$this->getUpdatedDate()
                )); // save transactions


            $this->db->commit();

        }catch (\Exception $exception){
            $this->db->rollBack();
            throw $exception;

        }
    }

    public function insertExpenses($_description,$_montant){

        $this->db->beginTransaction();

        $_expense_uid = $this->getExpensUid();

        try{

            $this->db->insert('t_expenses',
                array(
                    'montant'=> $_montant,
                    'description'=> $_description,
                    'user_info'=> "{$_SESSION['username']} {$_SESSION['name']} ",
                    'user_uid'=> $_SESSION['unique_id'],
                    'uniqid_id'=> $_expense_uid,
                    'created_at'=>$this->getCreatedDate(),
                    'updated_at'=>$this->getUpdatedDate()
                )); // save transactions

            $this->db->insert('t_transactions',
                array(
                    'trans_type'=> 'expens',
                    'trans_montant'=> $_montant,
                    'trans_description'=> $_description,
                    'fk_expens_uniqid'=> $_expense_uid,
                    'uniqid'=> $this->getTransactionUid(),
                    'trans_created_at'=>$this->getCreatedDate(),
                    'trans_updated_at'=>$this->getUpdatedDate()
                )); // save transactions


            $this->db->commit();

        }catch (\Exception $exception){
            $this->db->rollBack();
            throw $exception;

        }

    }

    /**
     * @return array|\Doctrine\DBAL\list
     * @throws \Doctrine\DBAL\Exception
     *
     *  Income with expense
     */
    public function getIncomeExpenseDaily($start_date,$end_date){

        $queryAdd = "GROUP BY DAY(_date) ORDER BY _date DESC ";

        if ($start_date and $end_date){

            $start_date = explode('/',$start_date);
            $end_date = explode('/',$end_date);

            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];
            $end_date   = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];

            $queryAdd = "WHERE _date BETWEEN '{$start_date}' and '{$end_date} 23:59:59' GROUP BY DAY(_date) ORDER BY _date DESC ";

        }

        $query = $this->db->fetchAllAssociative("SELECT DATE(_date) AS _date,
                                                    SUM(income) AS incomes,
                                                    SUM(expens) AS expenses,
                                                    SUM(income) - SUM(expens) AS profit
                                                            FROM (
                                                            
                                                                SELECT r.created_at AS _date,
                                                                    r.cout AS income,
                                                                    0 AS expens
                                                                FROM `t_reservations` r
                                                                
                                                                UNION ALL
                                                                
                                                                SELECT e.created_at AS _date,
                                                                    0 AS income,
                                                                    e.montant AS expens
                                                                    
                                                                    FROM `t_expenses` e			
                                                    )  t1 $queryAdd");


        return $query;
    }
    public function getIncomeExpenseMonthly($year = ''){

        $query = "WHERE YEAR(_date) = YEAR(curdate()) GROUP BY MONTH(_date) ORDER BY _date DESC";
        if ($year){
            $query = "WHERE YEAR(_date) = '$year' GROUP BY MONTH(_date) ORDER BY _date DESC";
        }

        $query = $this->db->fetchAllAssociative("SELECT DATE(_date) AS _date,
                                        SUM(income) AS incomes,
                                        SUM(expens) AS expenses,
                                        SUM(income) - SUM(expens) AS profit
                                            FROM (
                                            
                                                SELECT r.created_at AS _date,
                                                    r.cout AS income,
                                                    0 AS expens
                                                FROM `t_reservations` r
                                                
                                                UNION ALL
                                                
                                                SELECT e.created_at AS _date,
                                                    0 AS income,
                                                    e.montant AS expens
                                            
                                            FROM `t_expenses` e			
                                        )  t1 {$query} ");

        return $query;
    }

    public function getIncomeExpenseChart(){

        $query = $this->db->fetchAllAssociative("SELECT DATE(_date) AS _date,
	SUM(income) AS incomes,
	SUM(expens) AS expenses,
	SUM(income) - SUM(expens) AS profit
FROM (

	SELECT r.created_at AS _date,
		r.cout AS income,
		0 AS expens
	FROM `t_reservations` r
	
	UNION ALL
	
	SELECT e.created_at AS _date,
		0 AS income,
		e.montant AS expens
		
		FROM `t_expenses` e			
	)  t1 GROUP BY DAY(_date) ORDER BY YEAR(_date) DESC 
	");

        return $query;

    }


    public function getExpenses(){

        $query = $this->db->fetchAllAssociative("SELECT * FROM `t_expenses` ORDER BY DATE(created_at) asc");

        return $query;
    }

    public function getRecu($id){

        $query = $this->db->fetchAssociative("SELECT r.cout,r.reservation_id,r.stardate,r.endate,
                                r.nombre_personne,c.noms,c.prenom,c.telephone,ch.`numero_chambre`
                                FROM
                                `t_reservations` r
                                INNER JOIN `t_clients` c
                                    ON c.`client_uniqid` = r.`client_uniqid`
                                INNER JOIN `t_chambres` ch
                                    ON ch.`chambre_uniqid` = r.`chambre_uniqid`
                                WHERE `reservation_id`= '{$id}'");

        return $query;

    }

    public function getDailyHourTendace($_type_tendace){

        $query = "SELECT SUM(cout) AS day_hourly_income,TIME(created_at) AS hours_
                        FROM `t_reservations`
                        WHERE TIME(created_at)  BETWEEN TIME(created_at) AND TIME(NOW()) 
                        AND DAY(created_at) = DAY(NOW()) GROUP BY TIME(created_at)";

        if ($_type_tendace == 'hours'){

            $query = "SELECT SUM(cout) AS day_hourly_income,CONCAT(HOUR(created_at),':00..59    ') AS hours_
                        FROM `t_reservations`
                        WHERE HOUR(created_at)  BETWEEN HOUR(created_at) AND HOUR(NOW()) 
                        AND DAY(created_at) = DAY(NOW()) GROUP BY HOUR(created_at)";

        }

        return $this->db->fetchAllAssociative($query);

    }
    public function geteMonthlyTendace($year_month){


        $queryAdd = "WHERE MONTH(created_at) = MONTH(NOW()) AND  YEAR(created_at) = YEAR(NOW())";

        if ($year_month){

            $year_month = explode('-',$year_month);
            $year = $year_month[0];
            $month = $year_month[1];

            $queryAdd = "WHERE MONTH(created_at) = {$month} AND  YEAR(created_at) = {$year}";
        }

        $query = "SELECT SUM(cout) AS monthly_income,DATE(created_at) AS month_
                        FROM `t_reservations`
                        {$queryAdd}
                        GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC ;";




        return $this->db->fetchAllAssociative($query);

    }
    public function getMonthToDate($firstMonth, $secondMonth){

        if ($firstMonth and $secondMonth){

            $_firstMonth = strip_tags($firstMonth);
            $_secondMonth = strip_tags($secondMonth);

            $_array_one = array();
            $_array_two = array();


            $first_query = "SELECT SUM(cout) AS monthly_income,DATE(created_at) AS date_
                        FROM `t_reservations`
                        WHERE DATE_FORMAT(created_at,'%Y-%m') = '{$_firstMonth}' 
                        GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC ;";

            $second_query = "SELECT SUM(cout) AS monthly_income,DATE(created_at) AS date_
                        FROM `t_reservations`
                        WHERE DATE_FORMAT(created_at,'%Y-%m') = '{$_secondMonth}' 
                        GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC ;";

            $execut_first = $this->db->fetchAllAssociative($first_query);
            $execut_second = $this->db->fetchAllAssociative($second_query);


            foreach ($execut_first as $value){
                $_array_one [] = (int)$value['monthly_income'];
            }

            foreach ($execut_second as $value){
                $_array_two [] = (int)$value['monthly_income'];
            }

            return array('1'=>array('date'=>$firstMonth,'data'=>$_array_one),
                            '2'=>array('date'=>$secondMonth,'data'=>$_array_two));
        }

    }


    private function getCreatedDate(){ return date('Y-m-d H:i:s');}
    private function getUpdatedDate(){ return date('Y-m-d H:i:s');}
    private function getTransactionUid(){ return Security::randomizer_integer(15,979);}

    private function getExpensUid(){ return Security::randomizer_integer(10,'exp');}




}