<?php
function generButtonField($name="",$value="",$class="")
{
    $s="";
    $s.="<tr";
    if ($class!="") $s.=" class='$tdCl'";
    $s.="><td align='center' colspan='2' class='$class'>\n";
    $s.="<input type='button'";
    if ($value!="") $s.=" value='$value'";
    if ($name!="") $s.=" name='$name'";
    $s.=">\n";
    $s.="</td></tr>\n";
    return $s;
}

function generFileField($name,$label,$explText,$size="",$maxlength="",$tdCl="", $spanCl="")
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
    if($explText) $s.=generExplanation( $label, $explText );        
    $s.="</td><td>\n";
    $s.="<input type='FILE' name='$name'";
    if ($size!="") $s.=" size='$size'";
    if ($maxlength!="") $s.=" maxlength='$maxlength'";
    $s.=">\n";
    $s.="</td></tr>\n";
    return $s;
}
?>
