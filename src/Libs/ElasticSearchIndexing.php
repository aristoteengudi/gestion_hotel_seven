<?php

namespace App;

use App\ElasticSearch\ElasticIndexing;

class ElasticSearchIndexing
{
    public $indexSearch;
    public $_id;

    function __construct($host, $index, $username, $password){
        $this->indexSearch = new ElasticIndexing($host, $username, $password);
        $this->indexSearch->index = $index;
    }
    
    function setId($id){
        $this->indexSearch->id = $id;
    }
    
    function setFields($fields){
        $this->indexSearch->fields = $fields;
    }
    

    function mapping(){
        return $this->indexSearch->mapping();
    }

    function setSettings($index){
        $this->indexSearch->setSettings($index);
    }

    public function upSettings($index){
        return $this->indexSearch->upSettings($index);
    }

    function indexing($data){
        return $this->indexSearch->bulkIndex($data);
    }
    
    function singleIndex($data){
        return $this->indexSearch->singleIndex($data);
    }
}
