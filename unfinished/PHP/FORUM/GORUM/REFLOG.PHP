<?php
$dbClasses[]="reflog";
$reflog_typ = 
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment"
            ),
            "http_referer"=>array(
                "type"=>"TEXT",
            ),            
            "referer_site"=>array(
                "type"=>"TEXT",
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
class RefLog extends Object
{
function create()
{
    global $HTTP_SERVER_VARS,$gorumuser;
    if (!isset($HTTP_SERVER_VARS["HTTP_REFERER"])) {
        $t="";
    }
    else $t=$HTTP_SERVER_VARS["HTTP_REFERER"];
    while (eregi("/$",$t)) $t=substr($t,0,strrpos($t,"/"));
    if (strstr($t,$HTTP_SERVER_VARS["HTTP_HOST"])) $t="";
    if ($t!="") {
        $this->userid=$gorumuser->id;
        $this->http_referer=$t;
        if (ereg("http:/"."/",$t)) $t=substr($t,7,strlen($t)-7);
        if (ereg("^www.",$t)) $t=substr($t,4,strlen($t)-4);
        $ta=explode("/",$t);
        $this->referer_site=$ta[0];
        create($this);
    }
}
}//END Class
?>
