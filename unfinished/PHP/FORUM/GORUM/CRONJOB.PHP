<?php
// A cronjobok applikacionkent fixek. Az installibben kell oket 
// letrehozni. A user nem hozhat letre ujat es nem torolhet ki egyet 
// sem, maximum inactivra allithatja oket, vagy a vegrehajtasi 
// gyakorisagukat valtoztathatja.

$dbClasses[]="cronjob";

$cronjob_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "form hidden",
            ),            
            "title"=>array(
                "type"=>"VARCHAR",
                "max" =>"120",
                "list",
                "details",
                "form readonly"
            ),
            "lastExecutionTime"=>array(
                "type"=>"INT",
                "list",
                "details",
                "form readonly"
            ),
            "frequency"=>array(  // hours
                "type"=>"INT",
                "text",
                "min"=>1,
                "mandatory",
                "list",
                "details",
            ),    
            "active"=>array(
                "type"=>"INT",
                "bool",
                "default"=>1,
                "list",
                "details",
            ),            
            "function"=>array(
                "type"=>"VARCHAR",
                "form invisible",
                "max" =>"120",
            )
        ),    
        "primary_key"=>"id",
        "sort_criteria_attr"=>"id",
        "sort_criteria_dir"=>"d"
    );

class CronJob extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $generalRight, $lll;
        hasAdminRights($isAdm);
        $hasRight = $isAdm&&$method!=Priv_create&&$method!=Priv_delete;
        $generalRight = TRUE;
        if( !$hasRight && $giveError )
        {
            handleError($lll["permission_denied"]);
        }
        return ok;
    } 
     
    function showDetailsTool()
    {
        return "";
    }   
     
    function showListVal($attr)
    {
        global $itemClassName, $categoryClassName, $lll;
    
        $s="";
        if( $attr=="active" ) {
            if ($this->active) $s=$lll["yes"];
            else $s=$lll["no"];
        }
        elseif( $attr=="lastExecutionTime" )
        {
            $s=showTimestamp($this->{$attr});
        }    
        elseif( $attr=="frequency" )
        {
            $s=$this->{$attr}." ".$lll["hour(s)"];
        }    
        else
        {
            $s=htmlspecialchars($this->{$attr});
        }
        return $s;
    }  
  
}

function executeCronJobs()
{
    global $applName;
    
    $cronjobs = new CronJob;
    $query = "SELECT * FROM $applName"."_cronjob WHERE active='1'";
    loadObjectsSql( $cronjobs, $query, $cronjobs );
    foreach( $cronjobs as $cronjob )
    {
        if( time()-$cronjob->lastExecutionTime > 60*60*$cronjob->frequency )
        {            
            eval($cronjob->function);
            $cronjob->lastExecutionTime=time();
            modify( $cronjob );
        }    
    }    
}

?>