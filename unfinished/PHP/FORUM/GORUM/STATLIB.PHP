<?php
$dbClasses[]="stathelp";
$dbClasses[]="statcache";

$allowedMethods["stat"]='$ret=showStat($s);';


$stathelp_typ =  
    array(
        "attributes"=>array(   
            "txt"=>array(
                "type"=>"VARCHAR",
                "max" =>"100",
            )            
        ),
    );
class StatHelp extends Object
{
}
$statcache_typ =  
    array(
        "attributes"=>array(   
            "timestamp"=>array(
                "type"=>"INT"
            ),
            "inttype"=>array(
                "type"=>"INT"
            ),
            "stattype"=>array(
                "type"=>"INT"
            ),
            "value"=>array(
                "type"=>"INT"
            )
        ),
        "primary_key"=>array("timestamp","inttype","stattype")
    );
class StatCache extends Object
{
}
define("stat_main",0);
define("stat_users",1);
define("stat_reg",2);
define("stat_allusers",3);
define("stat_refpage",4);
define("stat_refsite",5);

define("stat_daily",1);
define("stat_weekly",2);
define("stat_monthly",3);
define("stat_yearly",4);
define("stat_all",5);
function showStat(&$s)
{
    global $lll,$xi;
    global $gorumroll,$gorumuser,$gorumrecognised;
    global $HTTP_GET_VARS;
    global $timestamp,$inttype,$stattype;
    global $infoText;
    global $applName,$userClassName;
    
    $s="";
    if (isset($HTTP_GET_VARS["timestamp"])) {
        $timestamp = $HTTP_GET_VARS["timestamp"];
    }
    if (isset($HTTP_GET_VARS["inttype"])) {
        $inttype = $HTTP_GET_VARS["inttype"];
    }
    if (isset($HTTP_GET_VARS["stattype"])) {
        $stattype = $HTTP_GET_VARS["stattype"];
    }
    if (!$gorumrecognised || !$gorumuser->isAdm) {
        handleError($lll["permission_denied"]);
    }
    loadWeekDays();
    $currenttime=time();
    if (isset($timestamp)) $t=$timestamp;
    else $t=time();
    $a=getdate($t);
    $timestamp=mktime(0,0,0,$a["mon"],$a["mday"],$a["year"]);    
    $a=getdate($timestamp);
    if (!isset($inttype)) $inttype=stat_weekly;
    if (!isset($stattype)) {
        $stattype=stat_main;    
        $inttype=0;
    }
    $s.=showStatTypeMenu();
    if ($stattype!=stat_main) $s.=showIntMenu();
    if ($stattype==stat_main) {
        $ret=showMainStat($s1);
        if ($ret!=ok) {
            return $ret;
        } 
        $s.=$s1;
    }
    elseif ($stattype==stat_refsite) {
        $ret=showRefStat($s1);
        if ($ret!=ok) {
            return $ret;
        } 
        $s.=$s1;
    }
    elseif ($stattype==stat_refpage) {
        $ret=showRefStat($s1,FALSE);
        if ($ret!=ok) {
            return $ret;
        } 
        $s.=$s1;
    }
    else {
        if ($inttype==stat_daily) {
            $num=24;
            $from=$timestamp;
            $interval=3600;
        }
        elseif ($inttype==stat_weekly) {
            $num=7;
            $a=getdate($timestamp);
            //TODO: first day monday, sunday
            if ($a["wday"]==0) $d=6;
            else $d=$a["wday"]-1;
            $tsFirstDayOfWeek=mktime(0,0,0,$a["mon"],$a["mday"]-$d,
                $a["year"]);
            $from=$tsFirstDayOfWeek;
            $interval=86400;
        }
        if ($inttype==stat_monthly) {
            $a=getdate($timestamp);
            $tsFirstDayOfMonth=mktime(0,0,0,$a["mon"],1,$a["year"]);
            $tsLastDayOfMonth=mktime(0,0,0,$a["mon"]+1,0,$a["year"]);
            $a1=getdate($tsLastDayOfMonth);
            $num=$a1["mday"];
            $from=$tsFirstDayOfMonth;
            $interval=86400;
        }
        $s.="<p>";
        $to=$from+$interval;
        for($i=0;$i<$num;$i++) {
            $fillCache=FALSE;
            if ($to<$currenttime) {
                $query="SELECT value FROM $applName"."_statcache".
                       " WHERE timestamp=$from".
                       " AND inttype=$inttype AND stattype=$stattype";
                $result=executeQuery($query);
                $n=mysql_num_rows($result);
                if ($n) {
                    $row=mysql_fetch_row($result);
                }
                else $fillCache=TRUE;
            }
            if ($from>$currenttime) {
                $row=array();
                $row[]=0;
            }
            elseif ($to>=$currenttime || $fillCache) {
                if ($stattype==stat_allusers) {
                    $query="SELECT DISTINCT userid".
                           " FROM $applName"."_statlog".
                           " WHERE creationtime>=$from".
                           " AND creationtime<$to";
                }
                else {
                    $query="SELECT COUNT(*) FROM $applName".
                           "_$userClassName".
                           " WHERE creationtime>=$from".
                           " AND creationtime<$to";
                }
                if ($stattype==stat_reg) {
                    $query.=" AND id!=name";
                }
                $result=executeQuery($query);
                $row=mysql_fetch_row($result);
                if ($stattype==stat_allusers) {
                    $row=array();
                    $row[]=mysql_num_rows($result);
                }
            }
            if ($fillCache) {
                $query="INSERT INTO $applName"."_statcache".
                       " SET timestamp=$from, inttype=$inttype,".
                       " stattype=$stattype, value=".$row[0];
                $result=executeQuery($query);
            }
            $a=getdate($from);
            if ($inttype==stat_daily) {
                $data[$i][0]=$a["hours"];
            }
            elseif ($inttype==stat_weekly) {
                if ($a["wday"]==0) $d=6;
                else $d=$a["wday"]-1;
                global $lllwd;
                //$data[$i][0]=$lllwd[$d];
                $tempRoll = $gorumroll;
                $tempRoll->inttype = stat_daily;
                $tempRoll->stattype = $stattype;
                $tempRoll->timestamp = $from;
                $data[$i][0]=$tempRoll->generAnchor($a["mday"].". ".
                                $lllwd[$d]);
                //$data[$i][0].=printTime($from);
            }
            elseif ($inttype==stat_monthly) {
                $tempRoll = $gorumroll;
                $tempRoll->inttype = stat_daily;
                $tempRoll->stattype = $stattype;
                $tempRoll->timestamp = $from;
                $data[$i][0]=$tempRoll->generAnchor("$a[mday]."
                                );
                //$data[$i][0].=printTime($from);
            }
            $data[$i][1]=$row[0];
            //$a1=getdate($from);
            //$a2=getdate($to);
            //$data[$i][0].="from:$a1[mon].$a1[mday].$a1[hours]:$a1[minutes]:$a1[seconds]";
            //$data[$i][0].=" to:$a2[mon].$a2[mday].$a2[hours]:$a2[minutes]:$a2[seconds]";
            $from=$to;
            $to+=$interval;
        }
        if ($stattype==stat_users) {
            $title=$lll["stat_tit_users"];
        }
        elseif ($stattype==stat_reg) {
            $title=$lll["stat_tit_reg"];
        }
        elseif ($stattype==stat_allusers) {
            $title=$lll["stat_tit_allusers"];
        }
        $title.=" - ";
        if ($inttype==stat_daily) {
            //TODO: datum formatum
            $title.="$a[mday]. $a[mon]. $a[year]";
        }
        elseif ($inttype==stat_weekly) {
            $yd=((int)($a["yday"]/7))+1;
            $title.="$a[year]. $yd. ".$lll["st_week"];
        }
        if ($inttype==stat_monthly) {
            //TODO: datum formatum
            $title.="$a[mon]. $a[year]";
        }
        $ret=showGraph($title,$data,$num,$sgra);///TODO lang
        if ($ret!=ok) {
            return $ret;
        }
        $tempRoll = $gorumroll;
        $tempRoll->inttype = $inttype;
        $tempRoll->stattype = $stattype;
        if ($inttype==stat_daily) {
            $tempRoll->timestamp = $timestamp-86400;
        }
        elseif ($inttype==stat_weekly) {
            $tempRoll->timestamp = $timestamp-604800;
        }
        elseif ($inttype==stat_monthly) {
            $tempRoll->timestamp = $tsFirstDayOfMonth-1;
        }
        $spre=$tempRoll->generImageAnchor("$xi/prev.gif",$lll["stat_prev"]);
        if ($inttype==stat_daily) {
            $tempRoll->timestamp = $timestamp+86400;
        }
        elseif ($inttype==stat_weekly) {
            $tempRoll->timestamp = $timestamp+604800;
        }
        elseif ($inttype==stat_monthly) {
            $tempRoll->timestamp = $tsLastDayOfMonth+86401;
        }
        $sne=$tempRoll->generImageAnchor("$xi/next.gif",$lll["stat_next"]);
        $s.="<center><table border='0'><tr>".
            "<td valign='middle' align='center' class='statprev'>".
            "$spre&nbsp;&nbsp;&nbsp;$sne ".
            "</td></tr>".
            "<tr><td valign='middle'>$sgra</td>".
            "</tr></table></center>\n";
    }
    return ok;
}
function showRefStat(&$s,$site=TRUE)
{
    global $lll,$xi;
    global $gorumroll,$gorumuser;
    global $timestamp,$inttype,$stattype;
    global $applName;
    $a=getdate($timestamp);
    if ($inttype==stat_daily) {
        $from=$timestamp;
        $to=$from+86400;
    }
    elseif ($inttype==stat_weekly) {
        //TODO: first day monday, sunday
        if ($a["wday"]==0) $d=6;
        else $d=$a["wday"]-1;
        $tsFirstDayOfWeek=mktime(0,0,0,$a["mon"],$a["mday"]-$d,
            $a["year"]);
        $from=$tsFirstDayOfWeek;
        $to=$from+604800;
    }
    elseif ($inttype==stat_monthly) {
        $tsFirstDayOfMonth=mktime(0,0,0,$a["mon"],1,$a["year"]);
        $tsLastDayOfMonth=mktime(0,0,0,$a["mon"]+1,0,$a["year"]);
        $from=$tsFirstDayOfMonth;
        $to=$tsLastDayOfMonth;
    }
    elseif ($inttype==stat_all) {
        $from=0;
        $to=2000000000;
    }
    $a=getdate($timestamp);
//    $fa=getdate($from);
//    $ta=getdate($to);
//echo "$from-$to";
//    echo "$fa[mday].$fa[mon].$fa[year]-$ta[mday].$ta[mon].$ta[year]";

    $title="";
    $title.=" - ";
    if ($inttype==stat_daily) {
        //TODO: datum formatum
        $title.="$a[mday]. $a[mon]. $a[year]";
    }
    elseif ($inttype==stat_weekly) {
        $yd=((int)($a["yday"]/7))+1;
        $title.="$a[year]. $yd. ".$lll["st_week"];
    }
    elseif ($inttype==stat_monthly) {
        //TODO: datum formatum
        $title.="$a[mon]. $a[year]";
    }
    else $title="";

    $s="";
    $sT="";
    $sT.=generBoxUp();    
    $sT.="<tr><th class='header' colspan='2'>";
    if ($site) $sT.=$lll["stat_refsite"];
    else $sT.=$lll["stat_refpage"];
    $sT.="$title</th></tr>";
    //---
    if ($site) $what="referer_site";
    else $what="http_referer";
    $query="INSERT INTO $applName"."_stathelp".
           " SELECT $what FROM $applName"."_reflog".
           " WHERE creationtime>=$from AND creationtime<$to";
    executeQuery($query);
    $query="SELECT txt,COUNT(*) AS num FROM $applName"."_stathelp".
           " GROUP BY txt ".
           " ORDER BY num DESC LIMIT 30";    
    $result=executeQuery($query);
    $num=mysql_num_rows($result);
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_array($result);
        $sT.="<tr><td class='cell'><a href='$row[txt]' ".
             "target='_blank'>$row[txt]</a></td>".
             "<td class='cell'>".$row["num"]."</td></tr>";
    }
    $query="DELETE FROM $applName"."_stathelp";
    $result=mysql_query($query);
    if( $result==0 ) {
        $infoText[] = mysql_error();
        return general_mysql_error;
    }
    //end
    $sT.=generBoxDown();    

    $tempRoll = $gorumroll;
    $tempRoll->inttype = $inttype;
    $tempRoll->stattype = $stattype;
    if ($inttype==stat_daily) {
        $tempRoll->timestamp = $timestamp-86400;
    }
    elseif ($inttype==stat_weekly) {
        $tempRoll->timestamp = $timestamp-604800;
    }
    elseif ($inttype==stat_monthly) {
        $tempRoll->timestamp = $tsFirstDayOfMonth-1;
    }
    $spre=$tempRoll->generImageAnchor("$xi/prev.gif",$lll["stat_prev"]);
    if ($inttype==stat_daily) {
        $tempRoll->timestamp = $timestamp+86400;
    }
    elseif ($inttype==stat_weekly) {
        $tempRoll->timestamp = $timestamp+604800;
    }
    elseif ($inttype==stat_monthly) {
        $tempRoll->timestamp = $tsLastDayOfMonth+86401;
    }
    $sne=$tempRoll->generImageAnchor("$xi/next.gif",$lll["stat_next"]);
    $s.="<center><table border='0'><tr>".
        "<td valign='middle' align='center' class='statprev'>".
        //"<img src='$xi/prev.gif' width='7' height='13'> ".
        "$spre&nbsp;&nbsp;&nbsp;$sne ".
        //"<img src='$xi/next.gif' width='7' height='13'> ".
        "</td></tr>".
        "<tr><td valign='middle'>$sT</td>".
        "</tr></table></center>\n";
    return ok;
}
function showStatTypeMenu()
{
    global $gorumroll,$lll;
    global $timestamp,$inttype,$stattype,$xi;
    $s="";
    $s.="<table border='0' cellpadding='5' cellspacing='0'>";
    //begin
    $s.="<tr>";
    //---
    $tempRoll = $gorumroll;
    $tempRoll->stattype = stat_main;
    $tempRoll->timestamp = 0;
    $tempRoll->inttype = 0;
    if ($stattype==stat_main) $s.="<td class='statmenuact'";
    else $s.="<td class='statmenupass'";
    $s.=">";
    $s.=$tempRoll->generAnchor($lll["stat_main"]);
    $s.="</td>\n";
    if ($stattype==stat_main) $s.="<td class='statmenuact'";
    else $s.="<td class='statmenupass'";
    $s.="><img src='$xi/menupoint2.gif' width='6' height='6'></td>\n";
    //---
    if ($inttype!=stat_all) {
       if( class_exists("statlog") ) {
           $tempRoll = $gorumroll;
           $tempRoll->stattype = stat_allusers;
           $tempRoll->timestamp = $timestamp;
           $tempRoll->inttype = $inttype;
           if ($stattype==stat_allusers) $s.="<td class='statmenuact'";
           else $s.="<td class='statmenupass'";
           $s.=">";
           $s.=$tempRoll->generAnchor($lll["stat_allusers"]);
           $s.="</td>\n";
           if ($stattype==stat_allusers) $s.="<td class='statmenuact'";
           else $s.="<td class='statmenupass'";
           $s.="><img src='$xi/menupoint2.gif' width='6' height='6'></td>\n";
       }
       //---
       $tempRoll = $gorumroll;
       $tempRoll->stattype = stat_users;
       $tempRoll->timestamp = $timestamp;
       $tempRoll->inttype = $inttype;
       if ($stattype==stat_users) $s.="<td class='statmenuact'";
       else $s.="<td class='statmenupass'";
       $s.=">";
       $s.=$tempRoll->generAnchor($lll["stat_users"]);
       $s.="</td>\n";
       if ($stattype==stat_users) $s.="<td class='statmenuact'";
       else $s.="<td class='statmenupass'";
       $s.="><img src='$xi/menupoint2.gif' width='6' height='6'></td>\n";
       //---
       $tempRoll = $gorumroll;
       $tempRoll->stattype = stat_reg;
       $tempRoll->timestamp = $timestamp;
       $tempRoll->inttype = $inttype;
       if ($stattype==stat_reg) $s.="<td class='statmenuact'";
       else $s.="<td class='statmenupass'";
       $s.=">";
       $s.=$tempRoll->generAnchor($lll["stat_reg"]);
       $s.="</td>\n";
       if ($stattype==stat_reg) $s.="<td class='statmenuact'";
       else $s.="<td class='statmenupass'";
       $s.="><img src='$xi/menupoint2.gif' width='6' height='6'></td>\n";
    }
    //---
    if( class_exists("reflog") ) {
        $tempRoll = $gorumroll;
        $tempRoll->stattype = stat_refsite;
        $tempRoll->timestamp = $timestamp;
        $tempRoll->inttype = $inttype;
        if ($stattype==stat_refsite) $s.="<td class='statmenuact'";
        else $s.="<td class='statmenupass'";
        $s.=">";
        $s.=$tempRoll->generAnchor($lll["stat_refsite"]);
        $s.="</td>\n";
        if ($stattype==stat_refsite) $s.="<td class='statmenuact'";
        else $s.="<td class='statmenupass'";
        $s.="><img src='$xi/menupoint2.gif' width='6' height='6'></td>\n";
        //---
        $tempRoll = $gorumroll;
        $tempRoll->stattype = stat_refpage;
        $tempRoll->timestamp = $timestamp;
        $tempRoll->inttype = $inttype;
        if ($stattype==stat_refpage) $s.="<td class='statmenuact'";
        else $s.="<td class='statmenupass'";
        $s.=">";
        $s.=$tempRoll->generAnchor($lll["stat_refpage"]);
        $s.="</td>\n";
        if ($stattype==stat_refpage) $s.="<td class='statmenuact'";
        else $s.="<td class='statmenupass'";
        $s.="><img src='$xi/menupoint2.gif' width='6' height='6'></td>\n";
    }
    //end
    $s.="</tr>";
    $s.="</table>";
    
    return $s;
}
function showIntMenu()
{
    global $gorumroll,$lll;
    global $timestamp,$inttype,$stattype,$xi;
    $s="";
    $s.="<table border='0' cellpadding='5' cellspacing='0'>";
    //begin
    $s.="<tr>";
    //---
    $tempRoll = $gorumroll;
    $tempRoll->stattype = $stattype;
    $tempRoll->timestamp = $timestamp;
    $tempRoll->inttype = stat_daily;
    if ($inttype==stat_daily) $s.="<td class='stintmenuact'";
    else $s.="<td class='stintmenupass'";
    $s.=">";
    $s.=$tempRoll->generAnchor($lll["st_daily"]);
    $s.="</td>\n";
    if ($inttype==stat_daily) $s.="<td class='stintmenuact'";
    else $s.="<td class='stintmenupass'";
    $s.="><img src='$xi/menupoint.gif' width='6' height='6'></td>\n";
    //---
    $tempRoll = $gorumroll;
    $tempRoll->stattype = $stattype;
    $tempRoll->timestamp = $timestamp;
    $tempRoll->inttype = stat_weekly;
    if ($inttype==stat_weekly) $s.="<td class='stintmenuact'";
    else $s.="<td class='stintmenupass'";
    $s.=">";
    $s.=$tempRoll->generAnchor($lll["st_weekly"]);
    $s.="</td>\n";
    if ($inttype==stat_weekly) $s.="<td class='stintmenuact'";
    else $s.="<td class='stintmenupass'";
    $s.="><img src='$xi/menupoint.gif' width='6' height='6'></td>\n";
    //---
    $tempRoll = $gorumroll;
    $tempRoll->stattype = $stattype;
    $tempRoll->timestamp = $timestamp;
    $tempRoll->inttype = stat_monthly;
    if ($inttype==stat_monthly) $s.="<td class='stintmenuact'";
    else $s.="<td class='stintmenupass'";
    $s.=">";
    $s.=$tempRoll->generAnchor($lll["st_monthly"]);
    $s.="</td>\n";
    if ($inttype==stat_monthly) $s.="<td class='stintmenuact'";
    else $s.="<td class='stintmenupass'";
    $s.="><img src='$xi/menupoint.gif' width='6' height='6'></td>\n";
    //---
    if ($stattype==stat_refsite || $stattype==stat_refpage) {
        $tempRoll = $gorumroll;
        $tempRoll->stattype = $stattype;
        $tempRoll->timestamp = time();
        $tempRoll->inttype = stat_all;
        if ($inttype==stat_all) {
            $s.="<td class='stintmenuact'";
        }
        else {
            $s.="<td class='stintmenupass'";
        }
        $s.=">";
        $s.=$tempRoll->generAnchor($lll["st_all"]);
        $s.="</td>\n";
    }
    //---
    //end
    $s.="</tr>";
    $s.="</table>";
    return $s;
}
function showMainStat(&$s)
{
    global $infoText,$lll,$applName,$userClassName;
    
    $s="";
    $s.=vertSpacer();
    $s.=generBoxUp();
    $s.="<tr><th class='header' colspan='2'>".
        $lll["main_stat_tit"]."</th></tr>";
    //---
    $query="SELECT COUNT(*) FROM $applName"."_$userClassName";
    $result=executeQuery($query);
    $row=mysql_fetch_row($result);
    $s.="<tr><td class='cell'>".$lll["stat_allguest"]."</td>".
        "<td class='cell'>".$row[0]."</td></tr>\n";
    //---
    $query="SELECT COUNT(*) FROM $applName".
           "_$userClassName WHERE id!=name";
    $result=mysql_query($query);
    if( $result==0 ) {
        $infoText[] = mysql_error();
        return general_mysql_error;
    }
    $row=mysql_fetch_row($result);
    $s.="<tr><td class='cell'>".$lll["stat_allreg"]."</td>".
        "<td class='cell'>".$row[0]."</td></tr>\n";
    //---
    $t=time();
    $t-=86400;//24 hours
    $query="SELECT COUNT(*) FROM $applName"."_$userClassName".
           " WHERE $t<creationtime";
    $result=mysql_query($query);
    if( $result==0 ) {
        $infoText[] = mysql_error();
        return general_mysql_error;
    }
    $row=mysql_fetch_row($result);
    $s.="<tr><td class='cell'>".$lll["stat_all_24"]."</td>".
        "<td class='cell'>".$row[0]."</td></tr>\n";
    //---
    $query="SELECT COUNT(*) FROM $applName"."_$userClassName".
           " WHERE $t<creationtime".
           " AND id!=name";
    $result=mysql_query($query);
    if( $result==0 ) {
        $infoText[] = mysql_error();
        return general_mysql_error;
    }
    $row=mysql_fetch_row($result);
    $s.="<tr><td class='cell'>".$lll["stat_reg_24"]."</td>".
        "<td class='cell'>".$row[0]."</td></tr>\n";
    //---
    $t=time();
    $t-=604800;//168 hours
    $query="SELECT COUNT(*) FROM $applName"."_$userClassName".
           " WHERE $t<creationtime";
    $result=executeQuery($query);
    $row=mysql_fetch_row($result);
    $s.="<tr><td class='cell'>".$lll["stat_all_168"]."</td>".
        "<td class='cell'>".$row[0]."</td></tr>\n";
    //---
    $query="SELECT COUNT(*) FROM $applName"."_$userClassName".
           " WHERE $t<creationtime".
           " AND id!=name";
    $result=mysql_query($query);
    if( $result==0 ) {
        $infoText[] = mysql_error();
        return general_mysql_error;
    }
    $row=mysql_fetch_row($result);
    $s.="<tr><td class='cell'>".$lll["stat_reg_168"]."</td>".
        "<td class='cell'>".$row[0]."</td></tr>\n";
    //end
    $s.=generBoxDown();
    
    return ok;
}
function showGraph($title,$data,$num,&$s)
{
    global $xi;

    $s="";
    $maxHeight=150;
    $max=$data[0][1];
    for($i=0;$i<$num;$i++) {
        if ($max<$data[$i][1]) $max=$data[$i][1];
    }
    if ($max==0) $sus=1;
    else $sus=$maxHeight/$max;
    $s.=generBoxUp();
    $s.="<tr><th colspan='$num' class='header'>".
        "$title</th></tr>\n";
    $s.="<tr>";
    $list2Colors=0;//nem global
    for($i=0;$i<$num;$i++) {
        $tdClass="cell";
        if ($list2Colors && $tdClass=="cell") $tdClass="cell2";
        $list2Colors = ($list2Colors + 1) % 2;
        $s.="<td class='$tdClass'>".$data[$i][0]."</td>";
    }
    $s.="</tr>\n";
    $s.="\n\n";
    $s.="<tr height='$maxHeight'>";
    $list2Colors=0;//nem global
    for($i=0;$i<$num;$i++) {
        $tdClass="cell";
        if ($list2Colors && $tdClass=="cell") $tdClass="cell2";
        $list2Colors = ($list2Colors + 1) % 2;
        $h=(int)($data[$i][1]*$sus)+1;
        $s.="<td valign='bottom' align='center' class='$tdClass'>".
            "<img src='$xi/stat2.gif' width='10' height='".//TODO: gif
            "$h'></td>";
    }
    $s.="</tr>\n";
    $s.="<tr>";
    $list2Colors=0;//nem global
    for($i=0;$i<$num;$i++) {
        $tdClass="cell";
        if ($list2Colors && $tdClass=="cell") $tdClass="cell2";
        $list2Colors = ($list2Colors + 1) % 2;
        $s.="<td class='$tdClass'>".$data[$i][1]."</td>";
    }
    $s.="</tr>\n";
    //end
    $s.=generBoxDown();
    return ok;
}
function loadWeekDays()
{
    global $lllwd,$lll;

    //TODO first day sunday - monday
    $lllwd[0]=$lll["monday"];
    $lllwd[1]=$lll["tuesday"];
    $lllwd[2]=$lll["wednesday"];
    $lllwd[3]=$lll["thursday"];
    $lllwd[4]=$lll["friday"];
    $lllwd[5]=$lll["saturday"];
    $lllwd[6]=$lll["sunday"];
}
function printTime($ts)
{
    $a=getdate($ts);
    return "$a[year].$a[mon].$a[mday]. ($a[weekday]) $a[hours]:$a[minutes]:$a[seconds] mday:$a[mday] wday:$a[wday]";
}
?>
