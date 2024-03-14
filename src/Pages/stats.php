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

    case 'tendance':

        $breadcrumb = [
            [ 'path' => './', 'name' => 'Dashboard'],
            [ 'path' => './stats', 'name' => 'Tendances Journalière'],
        ];

        $params = ['page_title'=>'Tendances Journalière', 'breadcrumb' => $breadcrumb];

        $_type_tendance = isset($_GET['_type_tendance']) ? $_GET['_type_tendance']: null;
        $params ['_type_tendance'] = $_type_tendance ;


        $result = new \App\Model\Transactions();
        $dailyData = $result->getDailyHourTendace($_type_tendance);



        $new_array = array();
        $series_name = array();
        $details = 'KPI';

        $hours_categorie= array();

        foreach ($dailyData as $hours){
            $hours_categorie [] = $hours['hours_'];
        }

        $chart = [];
        $chart['container'] = "tendance_daily_histogram";
        $cOptions = new \App\Classes\Highchart();
        $cOptions->setChart("column",false,null,null);
        $cOptions->setTitle($details);
        $cOptions->setShadow(['text'=>false]);
        $cOptions->setTooltip(['pointFormat' =>'{series.name}: <b>{point.y}</b>','headerFormat'=>'<b>{series.name}</b><br><br>']);
        $cOptions->setXAxis('',['rotation'=>-45,'style'=>['fontSize'=>'13px','fontFamily'=>'Verdana, sans-serif']],true,'');
        $cOptions->setYAxis(0,['text'=>'Montant '],false,['format'=>'{value} $']);
        $cOptions->setAccessibility(['point'=>['valueSuffix'=> '%']]);
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
        foreach ($dailyData as $value){
            $cOptions->appendSeries((string)$value['hours_'],[(int)$value['day_hourly_income']],
                false,'column',[],0,$tooltip=['valueSuffix'=>'$']);
        }

        $options = $cOptions->getOptions();

        $chart = array_merge($chart, $options);
    
        $params ['tendace_daily_data'] = $chart;

        render('tendaces/tendances.html.twig', $params);

        break;
    case 'mensuel':

        $breadcrumb = [
            [ 'path' => './', 'name' => 'Dashboard'],
            [ 'path' => './stats', 'name' => 'Tendances Mensuel'],
        ];

        $params = ['page_title'=>'Tendances Mensuel', 'breadcrumb' => $breadcrumb];

        $_year_month = isset($_GET['_year_month']) ? $_GET['_year_month']: null;
        $params ['_year_month'] = $_year_month ;

        $result = new \App\Model\Transactions();
        $monthlyData = $result->geteMonthlyTendace($_year_month);

        $new_array = array();
        $series_name = array();
        $details = 'KPI';

        $month_categories= array();

        foreach ($monthlyData as $month_){
            $month_categories [] = $month_['month_'];
        }

        $chart = [];
        $chart['container'] = "tendance_monthly_histogram";
        $cOptions = new \App\Classes\Highchart();
        $cOptions->setChart("column",false,null,null);
        $cOptions->setTitle($details);
        $cOptions->setShadow(['text'=>false]);
        $cOptions->setTooltip(['pointFormat' =>'{series.name}: <b>{point.y}</b>','headerFormat'=>'<b>{series.name}</b><br><br>']);
        $cOptions->setXAxis('',['rotation'=>-45,'style'=>['fontSize'=>'13px','fontFamily'=>'Verdana, sans-serif']],true,'');
        $cOptions->setYAxis(0,['text'=>'Montant '],false,['format'=>'{value} $']);
        $cOptions->setAccessibility(['point'=>['valueSuffix'=> '%']]);
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
        foreach ($monthlyData as $value){
            $cOptions->appendSeries((string)$value['month_'],[(int)$value['monthly_income']],
                false,'column',[],0,$tooltip=['valueSuffix'=>'$']);
        }

        $options = $cOptions->getOptions();

        $chart = array_merge($chart, $options);

        $params ['tendance_monthly_data'] = $chart;

        render('tendaces/tendances_mensuel.html.twig', $params);
        break;
    default:

}
