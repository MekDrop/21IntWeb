<?php
function showTools($base,$rights)
{
    global $gorumroll,$showIcon;
    $s=$s1=$s2=$s3="";
    if( $gorumroll->method!="showdetails" )$s1=$base->showDetailsTool();
    $s2=$base->showModTool($rights);
    $s3=$base->showDelTool($rights);
    if ($showIcon) $sep=" ";
    else $sep=" | \n";
    if( $s1!="" || $s2!="" || $s3!="")
    { 
        $s.=$s1;
        if ($s && $s2) $s.=$sep;
        $s.=$s2;
        if ($s && $s3) $s.=$sep;
        $s.=$s3;
    }    
    if (!$s) $s="&nbsp;";
    return $s;
}

function showModTool($base, $rights)
{
    global $lll,$gorumroll,$xi,$showIcon;
    
    $s="";
    if (!isset($rights[Priv_modify])) {
        $base->hasObjectRights($hasRight,Priv_modify);
    }
    else $hasRight = $rights[Priv_modify];
    if ($hasRight) {    
        $tempRoll = $gorumroll;
        $tempRoll->method="modify_form";
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);
        $tempRoll->id = $base->id;
        if( $showIcon )
        {
            $s.=$tempRoll->generImageAnchor("$xi/modify.gif",
                                   $lll["icon_modify"],17,22);
        }
        else {
            $s.=$tempRoll->generAnchor($lll["icon_modify"]);
        }
    }
    return $s;
}

function showDelTool($base, $rights)
{
    global $lll,$gorumroll,$xi,$showIcon;

    $s="";
    if (!isset($rights[Priv_delete])) {
        $base->hasObjectRights($hasRight,Priv_delete);
    }
    else $hasRight = $rights[Priv_delete];
    if ($hasRight) {    
        $tempRoll = $gorumroll;
        $tempRoll->method="delete_form";
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);        
        $tempRoll->id = $base->id;
        if( $showIcon )
        {
            $s.=$tempRoll->generImageAnchor("$xi/delete.gif",
                                       $lll["icon_delete"],17,22);
        }
        else {
            $s.=$tempRoll->generAnchor($lll["icon_delete"]);
        }
    }
    return $s;
}

function showDetailsTool($base)
{
    global $lll,$gorumroll,$xi,$showIcon;
    $s="";
    $tempRoll = $gorumroll;
    $tempRoll->list=$gorumroll->list;
    $tempRoll->method="showdetails";
    $tempRoll->rollid = $base->id;
    saveInFromFrom($tempRoll);        
    if( $showIcon )
    {
        $s.=$tempRoll->generImageAnchor("$xi/details.gif",
                                   $lll["icon_details"],17,22);
    }
    else {
        $s.=$tempRoll->generAnchor($lll["icon_details"]);
    }
    return $s;
}

function showNewTool($base,$rights)
{
    global $lll,$gorumroll;

    $s="";
    if (!isset($rights[Priv_create])) {
        $base->hasObjectRights($hasRight,Priv_create);
    }
    else $hasRight = $rights[Priv_create];
    if ($hasRight) {
        $tempRoll = $gorumroll;
        $tempRoll->class = $gorumroll->class;
        $tempRoll->method = "create_form";   
        $tempRoll->rollid = 0;   
        saveInFrom($tempRoll);
        $base->showNewToolPlusUrl($tempRoll);
        $label=$lll[$tempRoll->class."_newitem"];
        $s.=$tempRoll->generAnchor($label);
    }
    return $s;
}

function showCsvExportTool($base)
{
    global $lll,$gorumroll;

    $s="";
    hasAdminRights($isAdm);
    if ($isAdm) {
        $tempRoll = $gorumroll;
        $tempRoll->method = "showcsv";   
        $label=$lll["showcsv"];
        $s.=$tempRoll->generAnchor($label);
    }
    return $s;
}

function showSortTool($base, $attr)
{
    global $gorumroll;
    global $lll;
    global $xi;
    global $HTTP_COOKIE_VARS;

    $s = "";
    $typ = $base->getTypeInfo();
    //if (!isset($typ["sort_criteria_attr"])) return $s;
    $val=$typ["attributes"][$attr];
    if( !in_array("sorta",$val) && !in_array("sortd",$val)) {
        return $s;
    }   
    $tempRoll = $gorumroll;
    $tempRoll->list = $gorumroll->list;
    $tempRoll->method = "sort";   
    $tempRoll->rollid = 0;//Ez miert van itt ???
    $tempRoll->sortby = $attr;
    $tempRoll->rangeoffset = $tempRoll->offset = 0;
    saveInFrom($tempRoll);
    $className = get_class($base);
    $cookieSortName=$className."_sort";
    $globalSortAttr=$typ["sort_criteria_attr"];
    if (isset($HTTP_COOKIE_VARS[$cookieSortName]) && 
        $HTTP_COOKIE_VARS[$cookieSortName]==$attr) {
        $cookieSortDir=$className."_sortdir";
        if ($HTTP_COOKIE_VARS[$cookieSortDir]=="a") $sortdir="d";
        else $sortdir="a";
    }
    elseif ($globalSortAttr==$attr && 
            !isset($HTTP_COOKIE_VARS[$cookieSortName]) ) {
        $globalSortDir=$typ["sort_criteria_dir"];
        if ($globalSortDir=="a") $sortdir="d";
        else $sortdir="a";
    }
    else {
        $sortdir="";
    }
    $s.=" ";
    if ($sortdir=="" || $sortdir=="d") {
        $tempRoll->sortdir="d";
        $s.=$tempRoll->generImageAnchor(
            "$xi/asc.gif",$lll["icon_asc"],12,6);
    }
    if ($sortdir=="" || $sortdir=="a") {
        $tempRoll->sortdir="a";
        $s.=$tempRoll->generImageAnchor(
            "$xi/desc.gif",$lll["icon_desc"],12,6);
    }
    return $s;
}

function showRangeSelector( $base )
{
    global $gorumroll, $lll, $xi;
    global $blockSize, $rangeBlockSize;
    global $HTTP_COOKIE_VARS;
    
    $typ = $base->getTypeInfo();
    $bs=$blockSize;       
    $rbs=$rangeBlockSize;       
    $ro=isset($HTTP_COOKIE_VARS["kuki_rangeoffset"]) ? 
        $HTTP_COOKIE_VARS["kuki_rangeoffset"] : 0;
    $off=isset($HTTP_COOKIE_VARS["kuki_offset"]) ?
         $HTTP_COOKIE_VARS["kuki_offset"] : 0;
    $base->getCount($count);
    if( $count<=$bs || $bs==0 || $rbs==0 )
    {
        return "";
    }
    $tempRollWithoutOffset=$gorumroll;
    saveInFrom($tempRollWithoutOffset);
    unset($tempRollWithoutOffset->offset);
    $tempRollWithoutOffset->method = "range";
    $tempRollWithoutOffset->list = 
        isset($gorumroll->generallist) && $gorumroll->generallist ? 
        $gorumroll->generallist : $gorumroll->list;    
    $tempRollWithoutRangeOffset=$gorumroll;
    saveInFrom($tempRollWithoutRangeOffset);
    unset($tempRollWithoutRangeOffset->rangeoffset);
    $tempRollWithoutRangeOffset->method = "range";
    $tempRollWithoutRangeOffset->list = 
        isset($gorumroll->generallist) && $gorumroll->generallist ? 
        $gorumroll->generallist : $gorumroll->list;    
    $s="";
    $s.="<table border='0' cellspacing='0' cellpadding='0'><tr>";
    if( $off!=0 )
    {
        $tempRoll = $tempRollWithoutOffset;
        $tempRoll->offset = 0;
        $tempRoll->rangeoffset = $ro; 
        $s.="<td width='20' valign='middle' class='pager'>";
        $s.=$tempRoll->generImageAnchor("$xi/first.gif", $lll["first"]);
        $s.="</td>\n";
        $newOffset = max($off-$bs, 0);
        $tempRoll->offset = $newOffset;
        $tempRoll->rangeoffset = $ro; 
        $s.="<td width='15' valign='middle' class='pager'>";
        $s.=$tempRoll->generImageAnchor("$xi/prev.gif", $lll["prev"]);
        $s.="</td>\n";
    }
    if( $ro!=0 )
    {
        $newOffset = max($ro-$rbs, 0);
        $tempRoll = $tempRollWithoutRangeOffset;
        $tempRoll->rangeoffset = $newOffset;
        $tempRoll->offset = $off; //*
        $s.="<td width='15' valign='middle' class='pager'>";
        $s.=$tempRoll->generAnchor("...", "rangeSelector");
        $s.="</td>\n";
    }
    $upperRange=$count;
    for( $i=$ro; $i<$ro+$rbs; $i++ )
    {
        $newOffset = $i * $bs;
        if( $newOffset>=$count ) break;
        $tempRoll = $tempRollWithoutOffset;
        $tempRoll->offset = $newOffset;
        $tempRoll->rangeoffset = $ro; 
        if( $off/$bs==$i ) {
            $s.="<td valign='middle' class='pageract'>";
            $s.=($i+1);
            $s.="</td>\n";
        }
        else {
            $s.="<td valign='middle' class='pager'>";
            $s.=$tempRoll->generAnchor($i+1, "rangeSelector");
            $s.="</td>\n";
        }
        $s.="<td width='10' valign='middle'>";
        $s.="<img src='$xi/b.gif' width='10'>";
        $s.="</td>\n";
        $upperRange = min($newOffset+$bs, $count);
    }        
    if( $count > $upperRange )           
    {
        if(($ro+$rbs-1)*$bs>$count) $newOffset=($count/$bs)+1-$rbs;
        else $newOffset = $ro + $rbs;
        $tempRoll = $tempRollWithoutRangeOffset;
        $tempRoll->rangeoffset = $newOffset;
        $tempRoll->offset = $off; //*
        $s.="<td width='15' valign='middle' class='pager'>";
        $s.=$tempRoll->generAnchor("...", "rangeSelector");
        $s.="</td>\n";
    }
    if( $off + $bs < $count )
    {
        $newOffset = $off+$bs;
        $tempRoll = $tempRollWithoutOffset;
        $tempRoll->offset = $newOffset;
        $tempRoll->rangeoffset = $ro; 
        $s.="<td width='15' valign='middle' class='pager'>";
        $s.=$tempRoll->generImageAnchor("$xi/next.gif", $lll["next"]);
        $s.="</td>\n";
        if( $count % $bs ) $newOffset = $count - ($count % $bs);
        else $newOffset = $count - $bs;
        $tempRoll->offset = $newOffset;
        $tempRoll->rangeoffset = $ro; 
        $s.="<td width='20' valign='middle' class='pager'>";
        $s.=$tempRoll->generImageAnchor("$xi/last.gif", $lll["last"]);
        $s.="</td>\n";
    }
    $s.="</tr></table>\n";
    return $s;
}

function makeSort()
{
    global $HTTP_COOKIE_VARS;
    global $whatHappened,$gorumroll,$sortby,$sortdir;
    $cookieSortName=$gorumroll->list."_sort";
    $HTTP_COOKIE_VARS[$cookieSortName]=$gorumroll->sortby;
    $cookieSortDir=$gorumroll->list."_sortdir";
    $HTTP_COOKIE_VARS[$cookieSortDir]=$gorumroll->sortdir;
    setcookie($cookieSortName,$gorumroll->sortby);
    setcookie($cookieSortDir,$gorumroll->sortdir);
    $whatHappened = "form_submitted";
    //$gorumroll->method=$gorumroll->frommethod;
    // Ha uj rendezes van, mindig elolrol kezdjuk a listat:
    clearOffsets();
}

function makeRange()
{
    global $whatHappened,$gorumroll,$offset,$rangeoffset;
    global $HTTP_COOKIE_VARS, $HTTP_GET_VARS;
    if( isset($HTTP_COOKIE_VARS["kuki_offset"]) && 
        (!isset($HTTP_GET_VARS["offset"])||!$HTTP_GET_VARS["offset"]))
    {
        setcookie("kuki_offset","");
        $HTTP_COOKIE_VARS["kuki_offset"]=0;
    }
    elseif( isset($HTTP_GET_VARS["offset"])&&$HTTP_GET_VARS["offset"])
    {
        setcookie("kuki_offset",$HTTP_GET_VARS["offset"]);
        $HTTP_COOKIE_VARS["kuki_offset"]=$HTTP_GET_VARS["offset"];
    }
    if( isset($HTTP_COOKIE_VARS["kuki_rangeoffset"]) && 
        (!isset($HTTP_GET_VARS["rangeoffset"]) || 
         !$HTTP_GET_VARS["rangeoffset"]) )
    {
        setcookie("kuki_rangeoffset","");
        $HTTP_COOKIE_VARS["kuki_rangeoffset"]=0;
    }
    elseif( isset($HTTP_GET_VARS["rangeoffset"]) && 
            $HTTP_GET_VARS["rangeoffset"] )
    {
        setcookie("kuki_rangeoffset",$HTTP_GET_VARS["rangeoffset"]);
        $HTTP_COOKIE_VARS["kuki_rangeoffset"]=
            $HTTP_GET_VARS["rangeoffset"];
    }
    $whatHappened = "form_submitted";
    $gorumroll->method=$gorumroll->frommethod;
    //$gorumroll->list=$gorumroll->fromlist;
}

function clearOffsets()
{
    global $HTTP_COOKIE_VARS;
    setcookie("kuki_offset","");
    $HTTP_COOKIE_VARS["kuki_offset"]=0;
    setcookie("kuki_rangeoffset","");
    $HTTP_COOKIE_VARS["kuki_rangeoffset"]=0;
}
?>
