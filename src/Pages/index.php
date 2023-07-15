<?php
/*if (!in_array('simple',$_SESSION['roles'])){
    $params = [
        'title' => '403 forbidden - access restricted',
        'app_full_url' => get_app_full_url()];
k
    render('access_denied.html.twig', $params);
    exit();
}
*/

use App\Chart\ChartOptions;
$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './users', 'name' => 'Accueil']
];
$params = ['page_title'=>'Accueil', 'breadcrumb' => $breadcrumb];

$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){

    case 'facturation-pie':

        $get = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);
        $start_date = str_replace('/','-',$get['start_date']);
        $start_date = date('Y-m-d',strtotime($start_date));

        $end_date = str_replace('/','-',$get['end_date']);
        $end_date = date('Y-m-d',strtotime($end_date));
        $array = array($start_date,"$end_date 23:59:59");

        $query = $db->fetchAll("select count(*) as count_, `ocs_result_code`,`ocs_result_desc`,
                                case `ocs_result_code`
                                    when '0' then 'Réussi'
                                    when 'S-ACT-00112' then 'Balance insuffisant'
                                    when 'S-DAT-00021' then 'Echec mise à jour requête'
                                    else 'Echec'
                                end as state
                                
                                from `tb_logs_facturation` where `created_at` between ? and ? 
                                group by state",$array);

        $new_array = array();
        $details = '';

        foreach ($query as $value){
            $new_array [] = array('name'=>(string)$value['state'],'y'=>(int)$value['count_']);
        }

        if ($get['start_date']==$get['end_date']){
            $details = "Rapport du {$get['start_date']}";

        }else{
            $details = "Rapport du {$get['start_date']} au {$get['end_date']}";
        }

        $chart = [];
        $chart['container'] = "facturation-pie";
        $cOptions = new ChartOptions();
        $cOptions->setChart("pie",false,null,null);
        $cOptions->setTitle($details);
        $cOptions->setTooltip(['pointFormat' =>'{series.name}: <b>{point.percentage:.1f}%</b>']);
        $cOptions->setAccessibility(['point'=>['valueSuffix'=> '%']]);
        $cOptions->setPlotOptions( [
            'pie'=>[
                'allowPointSelect' =>true,
                'cursor' =>'pointer',
                'dataLabels'=>['enabled' =>true,'format'=>'<b>{point.name}</b>: {point.percentage:.1f} %'],
                'showInLegend'=>true
            ]]);
        $cOptions->appendSeries('Total', $new_array,true);

        $options = $cOptions->getOptions();

        $chart = array_merge($chart, $options);


        $response  = \GuzzleHttp\json_encode($chart);
        echo $response;
        break;
    case 'facturation-histogram':

        $get = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);
        $start_date = str_replace('/','-',$get['start_date']);
        $start_date = date('Y-m-d',strtotime($start_date));

        $end_date = str_replace('/','-',$get['end_date']);
        $end_date = date('Y-m-d',strtotime($end_date));
        $array = array($start_date,"$end_date 23:59:59");

        $query = $db->fetchAll("select count(*) as count_, `ocs_result_code`,`ocs_result_desc`,
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
        echo $response;
        break;
    case 'subscription-pie':
        $get = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);
        $start_date = str_replace('/','-',$get['start_date']);
        $start_date = date('Y-m-d',strtotime($start_date));

        $end_date = str_replace('/','-',$get['end_date']);
        $end_date = date('Y-m-d',strtotime($end_date));
        $array = array($start_date,"$end_date 23:59:59");

        $query = $db->fetchAll("select count(*) as count_, status as status_,
                                case status
                                    when '1' then 'Actif'
                                    when '0' then 'Inactif'
                                    else 'Expired or Inactif'
                                end as state
                                from `tb_souscriptions` where `created_at` between ? and ? 
                                group by state",$array);

        $new_array = array();
        $details = '';

        foreach ($query as $value){
            $new_array [] = array('name'=>(string)$value['state'],'y'=>(int)$value['count_']);
        }

        if ($get['start_date']==$get['end_date']){
            $details = "Rapport du {$get['start_date']}";

        }else{
            $details = "Rapport du {$get['start_date']} au {$get['end_date']}";
        }

        $chart = [];
        $chart['container'] = "subscription-pie";
        $cOptions = new ChartOptions();
        $cOptions->setChart("pie",false,null,null);
        $cOptions->setTitle($details);
        $cOptions->setTooltip(['pointFormat' =>'{series.name}: <b>{point.percentage:.1f}%</b>']);
        $cOptions->setAccessibility(['point'=>['valueSuffix'=> '%']]);
        $cOptions->setPlotOptions( [
            'pie'=>[
                'allowPointSelect' =>true,
                'cursor' =>'pointer',
                'dataLabels'=>['enabled' =>true,'format'=>'<b>{point.name}</b>: {point.percentage:.1f} %'],
                'showInLegend'=>true
            ]]);
        $cOptions->appendSeries('Total', $new_array,true);

        $options = $cOptions->getOptions();

        $chart = array_merge($chart, $options);


        $response  = \GuzzleHttp\json_encode($chart);
        echo $response;

        break;
    case 'subscription-histogram':

        $get = \GuzzleHttp\json_decode(file_get_contents('php://input'),true);
        $start_date = str_replace('/','-',$get['start_date']);
        $start_date = date('Y-m-d',strtotime($start_date));

        $end_date = str_replace('/','-',$get['end_date']);
        $end_date = date('Y-m-d',strtotime($end_date));
        $array = array($start_date,"$end_date 23:59:59");

        $query = $db->fetchAll("select count(*) as count_, status as status_,
                                case status
                                    when '1' then 'Actif'
                                    when '0' then 'Inactif'
                                    else 'Expired or Inactif'
                                end as state
                                from `tb_souscriptions` where `created_at` between ? and ? 
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
        $chart['container'] = "subscription-facturation";
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
        echo $response;
        break;
    default:

        $reservation = new \App\Model\Reservations();
        $count_chambre = new \App\Model\Chambres();

        $params ['count_total_chambre_dispo'] = $count_chambre->getTotalChambreDisponible();
        $params ['count_reservation'] = $reservation->getTotalReservation();
        $params ['curent_amount_reservation_month'] = $reservation->getCurrentMonthReservationAmount();
        $params ['count_clients'] = $reservation->getTotalClient();
        render('index.html.twig', $params);
}