<?php
namespace common\components;

class PartnersUtils{
    
    function __construct() {
        
    }
    function getGategoryAlias($id){
        $map = ["746" => "47"];
       // if(IS_PROD)
        return $id;
      //  else
        return (isset($map[$id]))?$map[$id]:$id;
    }
    function getPropertyAlias($slug){
        $map = ["obem" => "emnist"];
     
        return $slug;
   
        return (isset($map[$slug]))?$map[$slug]:$slug;
    }
}