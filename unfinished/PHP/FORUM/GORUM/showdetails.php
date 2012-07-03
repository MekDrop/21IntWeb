<?php
function showDetails(&$base,&$s, $whereFields,$withLoad=TRUE,
                     $headText="")
{
    global $lll,$gorumroll,$infoText,$list2Colors;
    

    $s="";
    if( $withLoad )
    {
        //A gorumroll->rollid-bol,vagy id, vagy name jon attol fuggoen, 
        //hogy egyszeru showDetailsrol, vagy showUserLinkrol van-e szo:
        $base->{$whereFields} = $gorumroll->rollid;
        $ret=$base->load(array($whereFields));    
        if ($ret==not_found_in_db)
        {
            $infoText = $lll["not_found_deleted"];
            return ok;
        }
    }
    
    $class = get_class($base);
    $typ=$base->getTypeInfo();
    $attrs=$typ["attributes"];
    $base->hasGeneralRights($rights);
    $attributeList = isset($typ["order"]) ? 
                     $typ["order"] : array_keys($typ["attributes"]);
    global $mainBoxWidth,$mainBoxPadding;
    if (!isset($mainBoxWidth)) $mainBoxWidth="100%";
    if (!isset($mainBoxPadding)) $mainBoxPadding="2";
    $s.=generBoxUp($mainBoxWidth,$mainBoxPadding);    

    $s1=$base->showTools($rights);
    $s.="<tr><th class='header' colspan='2'>";
    $s.="<table border='0' width='100%' cellpadding='0'".
        " cellspacing='0'><tr>\n";
    $s.="<th class='header'>";
    if ($headText) $s.=$headText;
    else $s.=sprintf($lll["detail_info"],$lll[$class]);
    $s.="</th>\n";
    $s.="<th class='headermethod' nowrap>$s1</th>\n";
    $s.="</tr></table>";
    $s.="</th></tr>\n";

    foreach( $attributeList as $attr ) 
    {
        $tdClass="cell";
        if (isset($list2Colors)) {
            if ($list2Colors && $tdClass=="cell") $tdClass="cell2";
            $list2Colors = ($list2Colors + 1) % 2;
        }
        if (isset($list2Colors)) $colHeadClass=$tdClass;
        else $colHeadClass="colheader";
        $val = & $typ["attributes"][$attr];
        if ( !in_array("details",$val) ) continue;
        if( in_array("section",$val) )
        {
            if( isset($lll[$attr]) )
            {
                $s.="<tr><th class='separator' colspan='2'>";
                $s.=$lll[$attr]; 
                $s.="</th></tr>\n";
            }    
            continue;
        }
        if (isset($lll[$class."_".$attr])) {
            $txt=$lll[$class."_".$attr];
        }
        else $txt=$lll[$attr];
        $s.="<tr><td class='$colHeadClass' width='30%'>";
        $s.=$txt;
        $s.="</td>\n";
        $s.="<td class='$tdClass' width='70%'>";
        $valTxt=$base->showListVal($attr,$rights);
        $s.=$valTxt ? $valTxt : "&nbsp;";
        $s.="</td></tr>\n";
    }
    $s.=$base->showDetailsMethods();

    $s.=generBoxDown();
    return ok;
    
}
?>
