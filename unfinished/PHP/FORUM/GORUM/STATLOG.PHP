<?php
$dbClasses[]="statlog";
$statlog_typ = 
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "userid"=>array(
                "type"=>"INT",
                "min" =>"0",
            ),
            "creationtime"=>array(
                "type"=>"INT"
            )
        ),
        "primary_key"=>array("id")
        );
class StatLog extends Object
{
function create()
{
    global $HTTP_SERVER_VARS,$gorumuser;
    $this->userid=$gorumuser->id;
    create($this);
}
}
?>
