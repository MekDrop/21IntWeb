<?php
$globalstat_typ = 
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "min" =>"0",
                "auto increment",
                "form hidden"
            ),
            "instver"=>array(
                "type"=>"VARCHAR",
                "max" =>"120",
                "min" =>"1",
            ),            
            "forumnum"=>array(
                "type"=>"INT",
                "min" =>"0",
            ),
            "topicnum"=>array(
                "type"=>"INT",
                "min" =>"0",
            ),
            "entrynum"=>array(
                "type"=>"INT",
                "min" =>"0",
            ),
            "usernum"=>array(
                "type"=>"INT",
                "min" =>"0",
            )
        ),
        "primary_key"=>array("id")
    );
class GlobalStat extends Object
{
}
function showUsersOnlineRow()
{
    global $lll, $applName, $userClassName;
    
    $s="";
    $uOT=time()-user_online_time;
    $query ="SELECT DISTINCT id,name FROM $applName"."_$userClassName".
            " WHERE lastClickTime>$uOT";
    $result=executeQuery($query);
    $num = mysql_num_rows($result);
    $unum=$gnum=0;
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_array($result, MYSQL_ASSOC);
        if ($row["id"]==$row["name"]) $gnum++;
        else $unum++;
    }
    //$s.=$lll["curr_online_num"];
    $s.=sprintf($lll["curr_online_num"],(int)$gnum,(int)$unum)." ".
        $lll["curr_online_link"].".";
    /*
    TODO
    goForward( $tempRoll, "user_online" );
    $s.=$tempRoll->generAnchor($lll["curr_online_link"]);
    */
    return $s;
}
?>
