<?php
$initBlock=TRUE;

$blacklist_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "min" =>"0",
                "auto increment",
                "form hidden"
            ),
            "block"=>array(
                "type"=>"VARCHAR",
                "text",
                "mandatory",
                "max" =>"255",
                "min" =>"1",
                "list",
            )
        ),
        "primary_key"=>"id",
        "delete_confirm"=>"block",
        "sort_criteria_attr"=>"block",
        "sort_criteria_dir"=>"a"
    );
    
class Blacklist extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $generalRight, $lll;
        $generalRight = TRUE;
        hasAdminRights($isAdm);
        $hasRight = $isAdm;
        if( !$hasRight && $giveError )
        {
            handleError($lll["permission_denied"]);
        }
    }

    function showDetailsTool()
    {
        return "";
    }
}

function blockBlacklist(&$what)
{
    global $HTTP_SERVER_VARS;
    global $gorumuser,$gorumauthlevel;
    global $blacklist_typ, $applName;


    $what="";
    $query="SELECT block from $applName"."_blacklist".
           " WHERE block='".addcslashes($gorumuser->name,"'\\")."' OR".
           " block='$HTTP_SERVER_VARS[REMOTE_ADDR]'";
    if (isset($gorumuser->email) && $gorumuser->email!="") {
        $query.=" OR block='$gorumuser->email'";
    }
    $result = executeQuery($query);
    $num=mysql_num_rows($result);
    if ($num>0) {
        $row=mysql_fetch_row($result);
        $what=$row[0];
        $gorumauthlevel=Loginlib_NewUser;
    }
}

function showBlocked()
{
    global $blockThat,$lll,$adminEmail;
    $s="";
    if ($blockThat!="") {
        $s.="<center><strong>";
        $s.=htmlspecialchars($blockThat)." ".$lll["is_blocked"].
            ". ".$lll["blocked_contact"];
        if ($adminEmail!="") $s.=" ($adminEmail)";
        $s.="</strong></center>";
    }
    return $s;
}

?>
