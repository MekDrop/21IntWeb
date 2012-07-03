<?php

$smiley_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "ubbBegin"=>array(
                "type"=>"VARCHAR",
                "text",
                "list",
                "details",
                "max" =>"255",
                "min" =>"1",
                "mandatory"
            ),
            "replacementBegin"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
                "mandatory"
            ),
            "enabled"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"0",
            ),            
            "comment"=>array(
                "type"=>"TEXT",
                "textarea",
                "rows" => 10,
                "cols" => 50,
                "list",
                "details"
            )
        ),
        "primary_key"=>"id",
        "delete_confirm"=>"comment",
        "sort_criteria_attr"=>"comment",
        "sort_criteria_dir"=>"a"
    );
    
class Smiley extends Ubb
{
}    
?>