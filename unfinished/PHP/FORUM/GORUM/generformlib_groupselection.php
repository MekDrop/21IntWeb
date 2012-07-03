<?php
function generGroupSelection($fromListTitle, $toListTitle, 
                             $fromListNames, $toListNames,
                             $fromListValues, $toListValues,
                             $fromListResult, $toListResult,
                             $fromListSize, $toListSize,
                             $addButtonText, $removeButtonText,
                             $addAllButtonText, $removeAllButtonText,
                             $tdCl="", $spanCl="")
{
    global $lll;

    $s="<table border='0'>\n";
    $s.="<tr>";
    $s.="<td align='center'>";
    if ($spanCl!="") $s.="<span class='$spanCl'>$fromListTitle</span>";
    else $s.=$fromListTitle;
    $s.="</td>\n";
    $s.="<td>";
    $s.="&nbsp;";
    $s.="</td>\n";
    $s.="<td  align='center'>";
    if ($spanCl!="") $s.="<span class='$spanCl'>$toListTitle</span>";
    else $s.=$toListTitle;
    $s.="</td>";
    $s.="</tr>\n";
    $s.="<tr>";
    $s.="<td align='center'>";
    $s.="<select name='$fromListResult' size='$fromListSize' multiple>\n";
    $length = count($fromListValues);
    if( $length )
    {
        foreach( $fromListValues as $key=>$value )
        {
            $s.="<option value='".$value."'>".
                htmlspecialchars($fromListNames[$key])."</option>\n";
        }
    }
    else $s.="<option value='0'>".$lll["emptyList"]."</option>\n";    
    $s.="</select>\n";
    $s.="</td>\n";
    $s.="<td align='center'>\n";
    $s.="<input type='submit' value='$addButtonText' name='submit' class='button'><br><br>\n";
    $s.="<input type='submit' value='$removeButtonText' name='submit' class='button'><br><br>\n";
    $s.="<input type='submit' value='$addAllButtonText' name='submit' class='button'><br><br>\n";
    $s.="<input type='submit' value='$removeAllButtonText' name='submit' class='button'>\n";
    $s.="</td>\n";
    $s.="<td align='center'>";
    $s.="<select name='$toListResult' size='$toListSize' multiple>\n";
    $length = count($toListValues);
    if( $length )
    {
        foreach( $toListValues as $key=>$value )
        {
            $s.="<option value='".$value."'>".
                htmlspecialchars($toListNames[$key])."</option>\n";
        }
    }    
    else $s.="<option value='0'>".$lll["emptyList"]."</option>\n";        
    $s.="</select>\n";
    $s.="</td>";
    $s.="</tr>";
    $s.="</table>\n";
    return $s;
}
?>
