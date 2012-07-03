<?php
function generTextField($type,$name,$label,$explText,$value="",$size="",
                        $maxlength="",$tdCl="", $spanCl="",
                        $withLabel=TRUE, $afterField="")
// a password es a button mezot is ezzel csinaljuk
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
    $value=htmlspecialchars($value);
    if ($withLabel) {
        $s.="<tr";
        if ($tdCl!="") $s.=" class='$tdCl'";
        $s.="><td";
        if ($tdCl!="") $s.=" class='$labelCl'";
        $s.=">";
        if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
        else $s.=$label;
        if($explText) $s.=generExplanation( $label, $explText );        
        $s.="</td><td class='$tdCl'>\n";
    }
    $s.="<input type='$type' name='$name'";
    if ($value!="") $s.=" value=\"$value\"";
    if ($size!="") $s.=" size='$size'";
    if ($maxlength!="") $s.=" maxlength='$maxlength'";
    $s.=">\n";
    $s.=$afterField;
    if ($withLabel) {
        $s.="</td></tr>\n";
    }
    return $s;
}

function generTextAreaField($name,$label,$explText,$value="",$rows="",
                            $cols="",$tdCl="", $spanCl="", 
                            $afterField="")
{
    global $list2Colors;
    $value=htmlspecialchars($value);
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
    $s.="><td valign='middle'";
    if ($tdCl!="") $s.=" class='$labelCl'";
    $s.=">";
    if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
    else $s.=$label;
    if($explText) $s.=generExplanation( $label, $explText );        
    $s.="</td><td class='$tdCl'>\n";
    $s.="<textarea name='$name'";
    if ($rows!="") $s.=" rows='$rows'";
    if ($cols!="") $s.=" cols='$cols'";
    $s.=">\n";
    if ($value!="") $s.=$value;
    $s.="</textarea>";
    $s.=$afterField;
    $s.="</td></tr>\n";
    return $s;
}

function generSelectField($name,$label,$explText,$labels,$values,
                          $selected=0,$size=0,$tdCl="", $spanCl="",
                          $newTr=TRUE, $onChange=FALSE,$labelCl="label", 
                          $afterField="")
{
    global $list2Colors;
    $s="";
    //$labelCl="label";
    if (isset($list2Colors)) {
        if ($list2Colors && $tdCl=="cell") {
            $tdCl="cell2";
            $labelCl="label2";
        }
        $list2Colors = ($list2Colors + 1) % 2;
    }    
    if ($newTr) $s.="<tr class='$tdCl'>";
    $s.="<td class='$labelCl'>";
    if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
    else $s.=$label;
    if($explText) $s.=generExplanation( $label, $explText );        
    $s.="</td><td class='$tdCl'>\n";
    $s.="<select name='$name'";
    if ($size>0) $s.=" size='$size'";
    if ($onChange) $s.=' onChange="form.submit()"';
    $s.=">\n";
    if( !in_array($selected, $values) && isset($values[0])) {
        $selected=$values[0];
    }
    foreach($labels as $key=>$label ){
        $s.="<option value=\"".$values[$key]."\"";
        if ($selected==$values[$key])
        {
            $s.=" selected";
        }
        $s.=">".htmlspecialchars($label)."</option>\n";
    }
    $s.="</select>\n";
    $s.=$afterField;
    $s.="</td>\n";
    if ($newTr) $s.="</tr>\n";
    return $s;
}

function generRadioField($name,$label,$explText,$labels,$values,
                         $selected=0,$cols=1,$tdCl="", $spanCl="",
                         $newTr=TRUE, $afterField="")
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
    if ($newTr) $s.="<tr class='$tdCl'>";
    $s.="<td class='$labelCl'>";
    if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
    else $s.=$label;
    if($explText) $s.=generExplanation( $label, $explText );        
    $s.="</td><td class='$tdCl'>\n";
    $s.="<table border='0' colspan='0' rowspan='0'><tr><td class='$tdCl'>\n";
    $length = count($values);
    if( !in_array($selected, $values) ) $selected=$values[0];;
    for( $i=0; $i<$length/$cols; $i++ )
    {
        $s.="<tr>";
        for( $j=$i*$cols; $j<($i+1)*$cols; $j++ )
        {
            if( !isset($values[$j]) ) break(2);
            $s.="<td class='$tdCl'>";
            $s.="<input type='radio' name='$name' value='".$values[$j]."'";
            if( $selected==$values[$j] ) $s.=" checked";
            $s.=">".$labels[$j];
            $s.="</td>\n";            
        }
        $s.="</tr>\n";
    }
    $s.="</table>\n";
    $s.=$afterField;
    $s.="</td>\n";
    if ($newTr) $s.="</tr>\n";
    return $s;
}

function generReadonlyField($label,$value,$tdCl="", $spanCl="")
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
    $s.="<tr";
    if ($tdCl!="") $s.=" class='$tdCl'";
    $s.="><td";
    if ($tdCl!="") $s.=" class='$labelCl'";
    $s.=">";
    if ($spanCl!="") $s.="<span class='$spanCl'>$label</span>";
    else $s.=$label;
    $s.="</td><td";
    if ($tdCl!="") $s.=" class='$tdCl'";
    if( $value=="" ) $value="&nbsp;";
    $s.=">$value</td>\n";
    $s.="</tr>\n";
    return $s;
}

function generBoolField($name,$label,$explText,$value, 
                        $tdCl="", $spanCl="", $withLabel=TRUE, 
                        $afterField="")
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
        $s.="<td class='$tdCl'>";
    }
    $s.="<input type='checkbox' name='$name' value='1'";
    if( $value ) $s.=" checked";
    $s.=">";
    $s.=$afterField;
    if ($withLabel) $s.="</td></tr>\n";
    return $s;
}

function generExplanation($label, $explText)
{
    global $showExplanation, $lll, $xi, $scriptName;
    $s="";  
    $label = str_replace(" *", "", $label);  
    switch( $showExplanation )
    {
    case Explanation_text:
        $s.="<br><span class='explTxt'>$explText</span>";
        break;
    case Explanation_qhelp:
        $s.=" <a href='javascript:;'".
            " onClick=\"helpW=window.open('$scriptName?method=pophelp".
            "&expl=$explText&title=$label','Help','width=300,".
            "height=400')\" class='popup'>".
            "<img src='$xi/qhelp.gif' width='12' height='13'".
            " alt='$lll[quickhelp] : $explText'".
            " title='$lll[quickhelp] : $explText' border='0'>".
            "</a>";
        break;    
    case Explanation_popup:
        $s.=" <a href='javascript:;'".
            " onClick=\"helpW=window.open('$scriptName?method=pophelp&expl=$explText&title=$label','Help','width=400,height=400')\" class='popup'>".
            //"<img src='$xi/qhelp.gif' width='12' height='13'".
            //" alt='$lll[quickhelp]' border='0'></a>";
            "[?]</a>";
        break;    
    }
    return $s;
}

?>
