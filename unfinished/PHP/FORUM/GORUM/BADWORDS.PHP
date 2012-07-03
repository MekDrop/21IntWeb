<?php
$dbClasses[]="badwords";
$badwords_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "badWord"=>array(
                "type"=>"VARCHAR",
                "text",
                "list",
                "max" =>"255",
                "min" =>"1",
                "mandatory"
            ),
            "niceWord"=>array(
                "type"=>"VARCHAR",
                "text",
                "list",
                "max" =>"255",
            )
        ),
        "primary_key"=>"id",
        "delete_confirm"=>"badWord",
        "sort_criteria_attr"=>"badWord",
        "sort_criteria_dir"=>"a"
    );
    
class BadWords extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $generalRight, $lll;
        hasAdminRights($isAdm);
        $hasRight =  $isAdm;
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
} 

function replaceBadWords( $s )
{
    global $gorumbadwords;
    foreach( $gorumbadwords as $replacement )
    {
        $s = eregi_replace( $replacement->badWord, 
                            $replacement->niceWord, $s );
    }
    return $s;
}
?>
