<?php

function loadHtmlList($base,&$list)
{
    
    $base->hasObjectRights($hasRight, Priv_load, TRUE);
    $query=getListQuery($base);
    $base->loadObjectsSQL($query,$list, TRUE);
    return ok;
    
}

function showHtmlList($base,&$s)
{
    global $lll,$colspNum, $searchClassName;
    $s="";
    $base->loadHtmlList($list);
    $base->hasGeneralRights($rights);
    $rangeSelText = showRangeSelector($base);  

    $typ=$base->getTypeInfo();
    $attrs=$typ["attributes"];
    $colspNum=0;
    if( !in_array("notools", $typ) ) $colspNum++;
    foreach( $attrs as $attr=>$val ) {
        if (in_array("list",$val) && !in_array("in new line", $val)) {
            $colspNum++;
        }
    }

    /*
    $search = new $searchClassName;
    $s1 = $search->showSpecialSearchFunctions();
    if( $s1 ) $s.=$s1;
    */
    
    $rangeText="";
    if( $rangeSelText )
    {
         $s2="";
         $s2.="<table border='0' width='100%' cellpadding='0'".
              " cellspacing='0'><tr>\n";
         $s2.="<td align='right' class='rangeSelector'>";
         $s2.=$rangeSelText;
         $s2.="</td>\n";
         $s2.="</tr></table>\n";
         $s2.="\n";
         $s.=$s2;
         $s.=vertSpacer(5);
         $rangeText=$s2;
    }
    global $mainBoxWidth,$mainBoxPadding;
    if (!isset($mainBoxWidth)) $mainBoxWidth="100%";
    if (!isset($mainBoxPadding)) $mainBoxPadding="2";
    $s.=generBoxUp($mainBoxWidth,$mainBoxPadding);
    $s.= $base->showListHeader();  
    foreach( $list as $listItem ) {
        $listItem->showOneRow($rights,$s1);
        $s.=$s1;
    }
    if (!isset($list[0])) {
        $s.="<tr><td class='cell' align='center' colspan='$colspNum'>".
            "$lll[emptylist]</td></tr>\n";
    }
    $s.=generBoxDown();
    /*
    if (!isset($list[0])) {
        $s.="<center><span class='cell'>$lll[emptylist]</span>".
            "</center><br><br>\n";
    }
    */
    if( $rangeText ) {
        $s.=vertSpacer(5);
        $s.=$rangeText;
    }
    $s1=$base->showBelowList();
    if( $s1 ) $s.="<br>$s1";
    return ok;
}

function showOneRow($base,$rights,&$s,$tdClass="listmethod")
{
    global $list2Colors;
    
    $s="<tr>";
    $typ=$base->getTypeInfo();
    if( !in_array("notools", $typ) )
    {
        if ($list2Colors && $tdClass=="listmethod") $tdClass.="2";
        $s1=$base->showTools($rights);
        if (!$s1) $s1="&nbsp;";
        $s.="<td class='$tdClass' valign='middle' nowrap>";
        $s.=$s1;
        $s.="</td>\n";
    }    
    $s.=$base->showListEntry($rights);
    $s.="</tr>\n";
    return ok;
}

function showListHeader($base, $isEmpty=FALSE)
{
    global $lll,$gorumroll,$headerAlign,$headerHeight,$colspNum;
    global $enableCsvExport;

    $s="";

    $typ=$base->getTypeInfo();
    $attributeList = isset($typ["listOrder"]) ? 
                     $typ["listOrder"] : array_keys($typ["attributes"]);

    /*
    global $rangeText;
    if ($rangeText) {
        $s.="<tr>";
        $s.="<th class='cell' colspan='$colspNum' align='center'>";
        $s.=$rangeText;
        $s.="</th></tr>\n";
    }
    */


    $s.="<tr>";
    $s.="<th class='header' colspan='$colspNum' align='left'>";
    $s.="<table width='100%' cellpadding='0' cellspacing='0'".
        " border='0'><tr>\n";
    $s.="<th class='header'>";
    $s.=$lll[$gorumroll->list."_ttitle"];
    $s.="</th>\n";
    $s.="<th class='headermethod'>";
    $base->hasGeneralRights($rights);
    $s1=$s2="";
    $s1=$base->showNewTool($rights);
    if( $enableCsvExport ) $s2=$base->showCsvExportTool();
    $s .= $s2 ? ($s1 ? "$s1 | $s2" : $s2) : $s1;
    
    $s.="</th>\n";
    $s.="</tr></table>";
    $s.="</th></tr>\n";


    $s.="<tr class='colheader'>";
    if( !in_array("notools", $typ) ) {
        $s.="<th class='colheader'";
        if (isset($headerHeight)) $s.=" height='$headerHeight'";
        $s.=">&nbsp;</th>\n";
    }
    foreach( $attributeList as $attr ) 
    {
        $val = & $typ["attributes"][$attr];
        if (in_array("list",$val) && !in_array("in new line", $val)) {
            $s.="<th nowrap class='colheader' valign='top'";
            if (isset($headerAlign)) $s.=" align='$headerAlign'";
            if (isset($headerHeight)) $s.=" height='$headerHeight'";
            $s.=">";
            if (isset($lll[$gorumroll->class."_".$attr])) {
                $s.=$lll[$gorumroll->class."_".$attr];
            }
            else $s.=$lll[$attr];
            $s.=$base->showSortTool($attr);
            $s.="</th>\n";
        }
    }        
    $s.="</tr>\n";
    return $s;
}

function showListEntry($base, $rights,$tdClass="cell")
{
    global $lll,$allCellValign,$colspNum,$list2Colors;

    $typ=$base->getTypeInfo();
    $attributeList = isset($typ["listOrder"]) ? 
                     $typ["listOrder"] : array_keys($typ["attributes"]);
    $s="";
    if (isset($list2Colors)) {
        if ($list2Colors && $tdClass=="cell") $tdClass="cell2";
        $list2Colors = ($list2Colors + 1) % 2;
    }
    $rowspan=1;
    $colspan=$colspNum;
    foreach( $typ["attributes"] as $attr=>$val ) {
        if (in_array("list",$val) && in_array("in new line", $val)) {
            $rowspan++;
        }
    }
    foreach( $attributeList as $attr ) 
    {
        $val = & $typ["attributes"][$attr];
        if (in_array("list",$val) && !in_array("in new line", $val)) {
            $s.="<td class='$tdClass'";
            if (in_array("centered",$val)) $s.=" align='center'";
            if (in_array("rowspan",$val)) {
                $s.=" rowspan='$rowspan'";
                $colspan--;
            }
            if (in_array("alignright",$val)) $s.=" align='right'";
            if (in_array("100%",$val)) $s.=" width='100%'";
            if (isset($allCellValign)) $s.=" valign='$allCellValign'";
            $s.=">";
            $s1=$base->showListVal($attr, $rights);
            $s.= $s1==="" ? "&nbsp;" : $s1;
            $s.="</td>\n";
        }
    } 
    foreach( $attributeList as $attr ) 
    {
        $val = & $typ["attributes"][$attr];
        if (in_array("list",$val) && in_array("in new line", $val)) {
            $s.="</tr><tr><td colspan='$colspan' class='$tdClass'";
            if (in_array("centered",$val)) $s.=" align='center'";
            if (in_array("alignright",$val)) $s.=" align='right'";
            if (in_array("100%",$val)) $s.=" width='100%'";
            $s.=">";
            $s1=$base->showListVal($attr, $rights);
            $s.= $s1==="" ? "&nbsp;" : $s1;
            $s.="</td>\n";
        }
    } 
    return $s;
}

function getListQuery(&$base)
{
    global $gorumroll;
    
    $select  = $base->getListSelect();
    $orderby = $base->getOrderBy();
    $limit   = $base->getLimit();
    $query = "$select ORDER BY $orderby $limit";
    return $query;
}
function getOrderBy($base)
{
    global $gorumroll;
    global $HTTP_COOKIE_VARS;
    
    //TODO: ez a cookie-s rendezes nem egeszen tiszta (???)
    $list=$gorumroll->list;
    $typ = $base->getTypeInfo();

    $cookieSortName=$list."_sort";
    $cookieSortDir=$list."_sortdir";
    if (isset($typ["sort_criteria_sql"])) {
        $sortCriteriaSql = $typ["sort_criteria_sql"];
    }
    else {
        $sortCriteriaDir = $typ["sort_criteria_dir"];
        $sortCriteriaAttr = $typ["sort_criteria_attr"];
    }
    if (isset($HTTP_COOKIE_VARS[$cookieSortName])) {
        $sortattr=$HTTP_COOKIE_VARS[$cookieSortName];
        if ($HTTP_COOKIE_VARS[$cookieSortDir]=="a") $sortdir="ASC";
        else $sortdir="DESC";
        $query="$sortattr $sortdir";
    }   
    elseif (isset($sortCriteriaSql)) {
        $query=$sortCriteriaSql;
    }
    else {
        $attrName = $sortCriteriaAttr;
        if ($sortCriteriaDir=="a") $direction="ASC";
        else $direction="DESC";
        $query="$attrName $direction";
    }   
    return $query;
}
function getLimit(&$base)
{
    
    global $blockSize;
    global $blockSortDirection;
    global $HTTP_COOKIE_VARS;
    if( isset($HTTP_COOKIE_VARS["kuki_offset"]) ) 
        $off = $HTTP_COOKIE_VARS["kuki_offset"];
    else if( $blockSortDirection=="lastBlockLast" )
    {
        $off = 0;
    }
    else
    {
        $base->getCount($count);
        if( $count>$blockSize )
        {
            $off = $count-$blockSize;
            setcookie("kuki_offset",$off);
        }
        else $off = 0;
    }
    $limit = "LIMIT $off, $blockSize";
    
    return $limit;
}

function showTimestamp($ts)
{
    global $dateFormat;
    return date($dateFormat,$ts);
}

function csvExport($base)
{
    global $lll, $gorumroll;
    $s="";
    $base->hasObjectRights($hasRight, Priv_load, TRUE);
    $select  = $base->getListSelect();
    $orderby = $base->getOrderBy();
    $query = "$select ORDER BY $orderby";
    $base->loadObjectsSQL($query,$list, TRUE);

    $typ=$base->getTypeInfo();
    $attributeList = isset($typ["listOrder"]) ? 
                     $typ["listOrder"] : array_keys($typ["attributes"]);
    $first = TRUE;
    foreach( $attributeList as $attr ) 
    {
        $val = & $typ["attributes"][$attr];
        if( in_array("list",$val) ) 
        {
            if( $first ) $first=FALSE;
            else $s.=";";
            if (isset($lll[$gorumroll->class."_".$attr])) {
                $s.=$lll[$gorumroll->class."_".$attr];
            }
            else $s.=$lll[$attr];
        }    
    }        
    $s.="\n";
    foreach( $list as $listItem ) 
    {
        $first = TRUE;
        foreach( $attributeList as $attr ) 
        {
            $val = & $typ["attributes"][$attr];
            if (in_array("list",$val) ) 
            {
                if( $first ) $first=FALSE;
                else $s.=";";
                $s.=$listItem->{$attr};
            }
        } 
        $s.="\n";
    }
    
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"listdump.csv\"");
    //header("Content-lenght: close");
    //header("Connection: close");
    //header("Expires: 0");
    echo $s;
    die();
    return ok;
}

?>
