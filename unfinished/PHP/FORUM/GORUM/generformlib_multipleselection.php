<?php
function generMultipleSelection($name, $label, $explText, $listNames, 
                                $listValues, $selected, $listSize, 
                                $tdCl="", $spanCl="",$withLabel=TRUE)
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
    if ($withLabel) {
        $s.="<tr";
        if ($tdCl!="") $s.=" class='$tdCl'";
        $s.="><td";
        if ($tdCl!="") $s.=" class='$labelCl'";
        $s.=">";
        if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
        else $s.=$label;
        if($explText) $s.=generExplanation( $label, $explText );        
        $s.="</td><td>\n";
    }
    $s.="<select name='$name"."[]' size='$listSize' multiple>\n";
    $length = count($listValues);
    if( !is_array($selected) ) $selected=array();
    if( $length )
    {
        foreach( $listValues as $key=>$value )
        {
            $s.="<option value='".$value."'";
            if( in_array($value, $selected) ) $s.=" selected";
            $s.=">".htmlspecialchars($listNames[$key])."</option>\n";
        }
    }
    else $s.="<option value='0'>".$lll["emptyList"]."</option>\n";    
    $s.="</select>\n";
    if ($withLabel) {
        $s.="</td></tr>\n";
    }
    return $s;
}
?>
