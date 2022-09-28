<?php
namespace App\Chart;

class ChartOptions
{
    private $chart       = [];
    private $title       = [];
    private $subtitle    = [];
    private $xAxis       = [];
    private $yAxis       = [];
    private $legend      = [];
    private $tooltip     = [];
    private $series      = [];
    private $accessibility= [];
    private $plotOptions = [];
    private $shadow = [];
    
    function setChart($type, $plotShadow = false, $plotBorderWidth = null,$plotBackgroundColor= null){
        $this->chart['type'] = $type;
        $this->chart['plotShadow'] = $plotShadow;
        $this->chart['plotBorderWidth'] = $plotBorderWidth;
        $this->chart['plotBackgroundColor'] = $plotBackgroundColor;
    }
    function getChart(){
        return $this->chart;
    }
    
    function setTitle($title){
        $this->title['text'] = $title;
    }
    function getTitle(){
        return $this->title;
    }
    
    function setSubtitle($title){
        $this->subtitle['text'] = $title;
    }
    function getSubtitle(){
        return $this->subtitle;
    }
    
    function setXAxis($type='', $labels = ['rotation'=>-45,'style'=>['fontSize'=>'13px','fontFamily'=>'Verdana, sans-serif']],$visible){
        $this->xAxis['type'] = $type;
        $this->xAxis['labels'] = $labels;
        $this->xAxis['visible'] = $visible;
    }
    function getXAxis(){
        return $this->xAxis;
    }
    
    function setYAxis($min,$title = ['text'=>""]){
        $this->yAxis['min'] = $min;
        $this->yAxis['title'] = $title;
    }
    function getYAxis(){
        return $this->yAxis;
    }
    
    function setLegend($enabled){
        $this->legend['enabled'] = $enabled;
    }
    function getLegend(){
        return $this->legend;
    }


    function setTooltip($tooltip){
        if(@$tooltip['pointFormat']) {
            $this->tooltip['pointFormat'] = @$tooltip['pointFormat'];
        }

        if (@$tooltip['headerFormat']){
            $this->tooltip['headerFormat'] = @$tooltip['headerFormat'];
        }
    }
    function getTooltip(){
        return $this->tooltip;
    }
    
    function appendSeries($name, $data = [], $colorByPoint = true){
        $this->series[] = ['name' => $name, 'data' => $data, 'colorByPoint' => $colorByPoint];
    }
    function getSeries(){
        return $this->series;
    }
    function getShadow(){
        return $this->shadow;
    }
    function setShadow($shadow = ['text'=>'']){
        $this->shadow = $shadow;
    }
    function setAccessibility($accessibility){
        $this->accessibility = $accessibility;
    }
    function getAccessibility(){
        return $this->accessibility;
    }
    
    function setPlotOptions($plotOptions = []){
        $this->plotOptions = $plotOptions;
    }
    function getPlotOptions(){
        return $this->plotOptions;
    }
    
    
    function getOptions(){
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

