<?php
function generSimpleDateField($name,$label,$explText,$value, $tdCl="", 
                              $spanCl="",$withLabel=TRUE,
                              $withTime=FALSE,$attrInfo)
{
    global $list2Colors;
    $s="";
    $labelCl="label";
    if (isset($list2Colors)) {
        if ($list2Colors && $tdCl=="cell") {
            $tdCl="cell2";
            $labelCl="label2";
        }
        $list2Colors = ($list2Colors + 1) % 2;
    }        
    if ($withLabel) {
        $s.="<tr";
        if ($tdCl!="") $s.=" class='$tdCl'";
        $s.="><td";
        if ($tdCl!="") $s.=" class='$labelCl'";
        $s.=">";
        if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
        else $s.=$label;
        if($explText) $s.=generExplanation( $label, $explText );        
        $s.="</td>\n";
        $s.="<td>";
    }
    $s.=generDateWidget($name, $value,"horizontal",$spanCl,$withTime,
                        $attrInfo);
    if ($withLabel) {
        $s.="</td></tr>\n";
    }
    return $s;
}

function generComplexDateField($name,$label,$explText,$value,
                               $labels,$values,$selected=0, 
                               $tdCl="", $spanCl="")
{
    global $lll,$list2Colors;
    $s="";
    $labelCl="label";
    if (isset($list2Colors)) {
        if ($list2Colors && $tdCl=="cell") {
            $tdCl="cell2";
            $labelCl="label2";
        }
        $list2Colors = ($list2Colors + 1) % 2;
    }        
    $s.="<tr";
    if ($tdCl!="") $s.=" class='$tdCl'";
    $s.="><td";
    if ($tdCl!="") $s.=" class='$labelCl'";
    $s.=">";
    if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
    else $s.=$label;
    if($explText) $s.=generExplanation( $label, $explText );        
    $s.="</td>\n";
    $s.="<td>";
    $s.="<table cellpadding='5' cellspacing='5'><tr><td valign='top'>";
    $s.="<select name='".$name."[relative]'>\n";
    foreach($labels as $key=>$label ){
        $values[$key]=htmlspecialchars($values[$key]);
        $s.="<option value=\"".$values[$key]."\"";
        if ((gettype($selected)=="array" && $selected[$key]) ||
            $key==$selected)
        {
            $s.=" selected";
        }
        $s.=">$label</option>\n";
    }
    $s.="</select>\n";
    if ($spanCl!="") {
        $s.="<span class='$spanCl'>, <br>".$lll["orSelectConcreteTime"].
            ":</span>";
    }
    else $s.=", <br>$lll[orSelectConcreteTime]:";
    $s.="</td><td>";
    $s.=generDateWidget($name, $value, "vertical", $spanCl);
    $s.="</td></tr></table></td></tr>\n";
    return $s;
}

function generDateWidget($name, $value, $alignment="horizontal", 
                         $spanCl="", $withTime=FALSE, $attrInfo)
{
// value a formba bemeno kezdeti ertek, ha 0, akkor az aktualis datumot
// vesszuk.
// A form eredmenye a $name[year], $name[month]es $name[day] valtozokban
// megy at. Pl. akarmi[year]=2001, akarmi[month]=December, akarmi[day]=6

    global $lll,$list2Colors;
    $value=htmlspecialchars($value);
    $s="";
    //TODO:
    //$labelCl="label";
    //if (isset($list2Colors)) {
    //    if ($list2Colors && $tdCl=="cell") {
    //        $tdCl="cell2";
    //        $labelCl="label2";
    //    }
    //    $list2Colors = ($list2Colors + 1) % 2;
    //}        
    if ($attrInfo["type"]=="DATE") $date=getDateMy($value);
    else $date = $value ? getdate($value) : getdate(time());
    if (!isset($attrInfo["fromyear"])) $attrInfo["fromyear"]=2001;
    if (!isset($attrInfo["toyear"])) $attrInfo["toyear"]=2010;
    if( $alignment=="vertical" ) 
    {
        $s.="<table><tr><td>";
        if ($spanCl=="") $s.="$lll[year]: ";
        else $s.="<span class='$spanCl'>".$lll["year"].": </span>";
        $s.="</td><td>";
        $s.="<select name='".$name."[year]'>\n";
        for($i=$attrInfo["fromyear"];$i<=$attrInfo["toyear"];$i++){
            $s.="<option value='$i'";
            if($date["year"]==$i)
            {
                $s.=" selected";
            }
            $s.=">$i</option>\n";
        }
        $s.="</select>\n";
        $s.="</td></tr>\n<tr><td>";
        if ($spanCl=="") $s.="$lll[month]: ";
        else $s.="<span class='$spanCl'>".$lll["month"].": </span>";
        $s.="</td><td>";
        $s.="<select name='".$name."[month]'>\n";
        for( $i=1; $i<13; $i++ ){
            $s.="<option value='$i'";
            if($date["mon"]==$i)
            {
                $s.=" selected";
            }
            $s.=">".$lll["month_$i"]."</option>\n";
        }
        $s.="</select>\n";
        $s.="</td></tr>\n<tr><td>";
        if ($spanCl=="") $s.="$lll[day]: ";
        else $s.="<span class='$spanCl'>".$lll["day"].": </span>";
        $s.="</td><td>";
        $s.="<select name='".$name."[day]'>\n";
        for( $i=1; $i<32; $i++ ){
            $s.="<option value='$i'";
            if($date["mday"]==$i)
            {
                $s.=" selected";
            }
            $s.=">$i</option>\n";
        }
        $s.="</select>\n";
        if( $withTime )
        {
            $s.="</td></tr>\n<tr><td>";
            if ($spanCl=="") $s.="$lll[hour]: ";
            else $s.="<span class='$spanCl'>".$lll["hour"].": </span>";
            $s.="</td><td>";
            $s.="<select name='".$name."[hour]'>\n";
            for( $i=0; $i<24; $i++ ){
                $s.="<option value='$i'";
                if($date["hours"]==$i)
                {
                    $s.=" selected";
                }
                $s.=">$i</option>\n";
            }
            $s.="</select>\n";
            $s.="</td></tr>\n<tr><td>";
            if ($spanCl=="") $s.="$lll[minute]: ";
            else $s.="<span class='$spanCl'>".$lll["minute"].": </span>";
            $s.="</td><td>";
            $s.="<select name='".$name."[hour]'>\n";
            for( $i=0; $i<60; $i++ ){
                $s.="<option value='$i'";
                if($date["minutes"]==$i)
                {
                    $s.=" selected";
                }
                $s.=">$i</option>\n";
            }
            $s.="</select>\n";
        }
        $s.="</td></tr></table>\n";
    }
    else
    {
        if ($spanCl=="") $s.="$lll[year]: ";
        else $s.="<span class='$spanCl'>".$lll["year"].": </span>";
        $s.="<select name='".$name."[year]'>\n";
        for($i=$attrInfo["fromyear"];$i<=$attrInfo["toyear"];$i++){
            $s.="<option value='$i'";
            if($date["year"]==$i)
            {
                $s.=" selected";
            }
            $s.=">$i</option>\n";
        }
        $s.="</select>\n";
        if ($spanCl=="") $s.="$lll[month]: ";
        else $s.="<span class='$spanCl'>, ".$lll["month"].": </span>";
        $s.="<select name='".$name."[month]'>\n";
        for( $i=1; $i<13; $i++ ){
            $s.="<option value='$i'";
            if($date["mon"]==$i)
            {
                $s.=" selected";
            }
            $s.=">".$lll["month_$i"]."</option>\n";
        }
        $s.="</select>\n";
        if ($spanCl=="") $s.="$lll[day]: ";
        else $s.="<span class='$spanCl'>, ".$lll["day"].": </span>";
        $s.="<select name='".$name."[day]'>\n";
        for( $i=1; $i<32; $i++ ){
            $s.="<option value='$i'";
            if($date["mday"]==$i)
            {
                $s.=" selected";
            }
            $s.=">$i</option>\n";
        }
        $s.="</select>\n";
        if( $withTime )
        {
            if ($spanCl=="") $s.="$lll[hour]: ";
            else $s.="<span class='$spanCl'>".$lll["hour"].": </span>";
            $s.="<select name='".$name."[hour]'>\n";
            for( $i=0; $i<24; $i++ ){
                $s.="<option value='$i'";
                if($date["hours"]==$i)
                {
                    $s.=" selected";
                }
                $s.=">$i</option>\n";
            }
            $s.="</select>\n";
            if ($spanCl=="") $s.="$lll[minute]: ";
            else $s.="<span class='$spanCl'>".$lll["minute"].": </span>";
            $s.="<select name='".$name."[minute]'>\n";
            for( $i=0; $i<60; $i++ ){
                $s.="<option value='$i'";
                if($date["minutes"]==$i)
                {
                    $s.=" selected";
                }
                $s.=">$i</option>\n";
            }
            $s.="</select>\n";
        }
    }
    return $s;   
}
function getDateMy($mydate)
{
    if ($mydate) list($y,$m,$d)=explode("-",$mydate);
    else $m=$d=$y=0;
    return getdate(mktime(0,0,0,$m,$d,$y));
}
?>
