<?php


/*
if (!in_array('admin',$_SESSION['roles'])){
    $params = [
        'title' => '403 forbidden - access restricted',
        'app_full_url' => get_app_full_url()];


    render('access_denied.html.twig', $params);
    exit();
}

*/

$breadcrumb = [
    [ 'path' => './', 'name' => 'Dashboard'],
    [ 'path' => './Recettes', 'name' => 'Recettes Journalière'],
];


$params = ['page_title'=>'Recettes Journalière', 'breadcrumb' => $breadcrumb];


$action = app_request();
$date_time = date('Y-m-d H:i:s');


switch ($action){

    case 'filter':
        echo '<pre style="margin:100px 0 0 100px;">';print_r($_GET);echo '</pre>';
        break;
    case 'mensuel':


        //print_r($_GET);
        //die;
        $breadcrumb = [
            [ 'path' => './', 'name' => 'Dashboard'],
            [ 'path' => './reservations', 'name' => 'Recettes Mensuel'],
        ];

        $params = ['page_title'=>'Recettes Mensuel', 'breadcrumb' => $breadcrumb];

        $year = null;
        if (isset($_GET['year'])){

            $year = $_GET['year'];
        }

        $income_expense = new \App\Model\Transactions();

        $params ['income_expense_monthly'] = $income_expense->getIncomeExpenseMonthly($year);
        $params ['year'] = $year;

        //echo '<pre style="margin:100px 0 0 100px;">';print_r($params ['income_expense']);echo '</pre>';

        render('recettes_monthly.html.twig', $params);

        break;
    case 'chart-expens-income':

        $transactions = new \App\Model\Transactions();
        $income_expenses_chart = $transactions->getIncomeExpenseChart();


        $new_array = array();
        $series_name = array();
        $details = 'kpi';

        $mois_trans_categorie= array();

        foreach ($income_expenses_chart as $mois){
            $mois_trans_categorie [] = $mois['_date'];
        }


        //echo '<pre>';
        //print_r($mois_trans_categorie);
        //die ();
        $chart = [];
        $chart['container'] = "recette_histogram";
        $cOptions = new \App\Classes\Highchart();
        $cOptions->setChart("column",false,null,null);
        $cOptions->setTitle($details);
        $cOptions->setShadow(['text'=>false]);
        $cOptions->setTooltip(['pointFormat' =>'{series.name}: <b>{point.y}</b>','headerFormat'=>'<b>{series.name}</b><br><br>']);
        $cOptions->setXAxis('',['rotation'=>-45,'style'=>['fontSize'=>'13px','fontFamily'=>'Verdana, sans-serif']],true,$mois_trans_categorie);
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
        foreach ($income_expenses_chart as $value){
            $cOptions->appendSeries((string)$value['_date'],[(int)$value['incomes']],false);
            $cOptions->appendSeries((string)$value['_date'],[(int)$value['expenses']],false);

            //$new_array [] = array('name'=>(string)$value['region_name'],'data'=>[(int)$value['total']]);
        }

        $options = $cOptions->getOptions();

        $chart = array_merge($chart, $options);


        $response  = \GuzzleHttp\json_encode($chart);
        echo $response;

        break;
    default:

        $start_date          = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date            = isset($_GET['end_date']) ? $_GET['end_date'] : null;

        $params ['start_date']  = $start_date;
        $params ['end_date']    = $end_date;

        $income_expense = new \App\Model\Transactions();

        $params ['income_expense'] = $income_expense->getIncomeExpenseDaily($start_date,$end_date);



        //echo '<pre style="margin:100px 0 0 100px;">';print_r($params ['income_expense']);echo '</pre>';

        render('recettes.html.twig', $params);
}
