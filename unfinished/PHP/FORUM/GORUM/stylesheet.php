<?php

$stylesheet_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "max" =>"32",
                "list",
                "sorta",
                "text",
                "mandatory",
                "min" => 1
            ),            
            "css"=>array(
                "type"=>"TEXT",
                "textarea",
                "rows" => 20,
                "cols" => 50,
                "mandatory",
                "min" => 1
            ),            
        ),
        "primary_key"=>"id",
        "delete_confirm"=>"name",
        "sort_criteria_attr"=>"name",
        "sort_criteria_dir"=>"a"
    );


class Stylesheet extends Object
{
function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    $hasRight=TRUE;
} 
     
}

?>
