<?php

namespace App\Classes;

/***
 * Class ChartOptions
 * @package App\Chart
 *
 * Aristote ENGUDI
 * engudiaristote@gmail.com
 */
class Highchart
{
    private $chart       = [];
    private $title       = [];
    private $subtitle    = [];
    private $xAxis       = [];
    private $yAxis       = [];
    private $legend      = [];
    private $tooltip     = [];
    private $series      = [];
    private $accessibility = [];
    private $plotOptions = [];
    private $shadow = [];
    
    public function  setChart($type, $plotShadow = false, $plotBorderWidth = null,$plotBackgroundColor= null,$zoomType = 'xy'){
        $this->chart['type'] = $type;
        $this->chart['plotShadow'] = $plotShadow;
        $this->chart['plotBorderWidth'] = $plotBorderWidth;
        $this->chart['plotBackgroundColor'] = $plotBackgroundColor;
        $this->chart['zoomType'] = $zoomType;
    }
    public function  getChart(){
        return $this->chart;
    }
    
    public function  setTitle($title){
        $this->title['text'] = $title;
    }
    public function  getTitle(){
        return $this->title;
    }
    
    public function  setSubtitle($title){
        $this->subtitle['text'] = $title;
    }
    public function  getSubtitle(){
        return $this->subtitle;
    }
    
    public function  setXAxis($type='', $labels = ['rotation'=>-45,'style'=>['fontSize'=>'13px','fontFamily'=>'Verdana, sans-serif']],$visible,$categories=[]){
        $this->xAxis['type'] = $type;
        $this->xAxis['labels'] = $labels;
        $this->xAxis['visible'] = $visible;
        if (!empty($categories)){
            $this->xAxis['categories'] = $categories;
        }
    }
    public function  getXAxis(){
        return $this->xAxis;
    }
    
    public function  setYAxis($min,$title = ['text'=>""],$opposite = false ,$labels=['format'=>'']){
        $min_table = array('min'=>$min,'title'=>$title,'opposite'=>$opposite,'labels'=>$labels);
        $this->yAxis [] = $min_table;
    }
    public function  getYAxis(){
        return $this->yAxis;
    }
    
    public function  setLegend($enabled){
        $this->legend['enabled'] = $enabled;
    }
    public function  getLegend(){
        return $this->legend;
    }


    public function  setTooltip($tooltip){
        if(@$tooltip['pointFormat']) {
            $this->tooltip['pointFormat'] = @$tooltip['pointFormat'];
        }

        if (@$tooltip['headerFormat']){
            $this->tooltip['headerFormat'] = @$tooltip['headerFormat'];
        }
    }
    public function  getTooltip(){
        return $this->tooltip;
    }
    
    public function  appendSeries($name, $data = [],$colorByPoint = true,$type = 'column',$marker=[],$yAxis = 0,$tooltip=['valueSuffix'=>'']){
        $this->series[] = ['name' => $name, 'data' => $data, 'colorByPoint' => $colorByPoint,'type'=>$type,'marker'=>$marker,'yAxis'=>$yAxis,'tooltip'=>$tooltip];
    }
    public function  getSeries(){
        return $this->series;
    }
    public function  getShadow(){
        return $this->shadow;
    }
    public function  setShadow($shadow = ['text'=>'']){
        $this->shadow = $shadow;
    }
    public function  setAccessibility($accessibility){
        $this->accessibility = $accessibility;
    }
    public function  getAccessibility(){
        return $this->accessibility;
    }
    
    public function  setPlotOptions($plotOptions = []){
        $this->plotOptions = $plotOptions;
    }
    public function  getPlotOptions(){
        return $this->plotOptions;
    }
    
    
    public function  getOptions(){
        $result = [];
        $reflection = new \ReflectionClass($this);
        $classProperties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($classProperties as $classProperty) {
            $property = $classProperty->getName();
            $method = "get".ucfirst($property);
            if($value = call_user_func([$this, $method]))
                $result[$property] = $value;
        }
        return $result;
    }
}

