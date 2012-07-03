<?php

function treeListPreCreate(&$base,$where)
{
    global $treeIdxBase;
    global $treeinserttop;
    global $g_treedepth,$g_treeidx;
    global $applName;
    //ha a g_treeidx be van allitva, akkor azutan kell bepakolni
    //az uj elemet, a treeidx utan, g_treedepth szintre

    
    $class = get_class($base);
    $tableName = $applName."_".get_class($base);
    $preObj = new $class;
    if ($g_treeidx==0) {
        $preObj->id = $base->up;
        $ret=$preObj->load("", "*", TRUE);//privilege???
    }
    else {
        $preObj->treeidx=$g_treeidx;
        $depth=$g_treedepth;
        $ret=ok;
    }
    if ($ret==not_found_in_db) {//no up, insert to the end or top
        $depth=0;
        if ($treeinserttop==1) $order="ASC";
        else $order="DESC";
        $query="SELECT treeidx FROM $tableName";
        if ($where!="") $query.=" WHERE $where";
        $query.=" ORDER BY treeidx $order LIMIT 1";
        $result=executeQuery($query);
        $num = mysql_num_rows($result);
        if ($num==0) {
            $treeidx=$treeIdxBase/2;
            $prevIdx=0;
        }
        else {//only one row in table
            $row=mysql_fetch_array($result, MYSQL_ASSOC);
            $prevIdx=$row["treeidx"];
            if ($treeinserttop==1) {
                $treeidx=(int)($prevIdx / 2);
            }
            else {
                $treeidx=$prevIdx+
                         (int)(($treeIdxBase - $prevIdx) / 2);
            }
        }
    }
    elseif ($ret==ok) {
        //get next elem after up
        //insert as last child
        if( $class=="forum" )
        {
            $preObj->hasChild="1";
            modify($preObj);
        }
        getLastGrandChildFromDb($preObj,$child);
        if (is_object($child)) {
            if ($g_treeidx==0) $depth=$preObj->treedepth+1;
            $preObj=$child;
        }
        elseif ($g_treeidx==0) $depth=$preObj->treedepth+1;
        //end

        //if ($g_treeidx==0) $depth=$preObj->treedepth+1;
        $query="SELECT * FROM $tableName WHERE treeidx > $preObj->treeidx";
        if ($where!="") $query.=" AND $where";
        $query.=" ORDER BY treeidx LIMIT 1";
        $result=executeQuery($query);
        $num = mysql_num_rows($result);
        $prevIdx=$preObj->treeidx;
        if ($num==0) {//up is the last entry
            $treeidx=$prevIdx+
                     (int)(($treeIdxBase - $prevIdx) / 2);
        }
        else {
            $row=mysql_fetch_array($result, MYSQL_ASSOC);
            $treeidx=$prevIdx+
                     (int)(($row["treeidx"] - $prevIdx ) / 2);
        }
    }
    else {//ERROR at loading up
        $txt="Error at loading up";
        handleError($txt);
    }
    if ($treeidx==$prevIdx) {//no place for new item
        //$ret=reindexTree($class,$treeidx);
        reindexAllTree($tableName,$where);
        global $treeListRecursive;
        if (isset($treeListRecursive)&&$treeListRecursive) {
            $txt = $lll["TLP10"];
            handleError($txt);
        }
        $treeListRecursive=TRUE;
        treeListPreCreate($base,$where);//recursive retry
        $treeListRecursive=FALSE;
        return ok;
    }
    else {
        $base->treedepth=$depth;
        $base->treeidx=$treeidx;
    }
    return ok;
}

function treeOrganiserServ($base, &$s)
{
    
    global $lll,$infoText,$gorumroll,$whatHappened,$applName;
    global $HTTP_POST_VARS;
    
    $whatHappened = "form_submitted";
    if( $gorumroll->submit==$lll["org_back"] )
    {   
        $gorumroll->fromclass = "forum";   
        $gorumroll->frommethod = "showhtmllist";   
        $gorumroll->fromid = 0;
        $infoText = $lll["operation_cancelled"];
        return ok;
    } 
    // loadHtmlList
    $tableName = $applName."_".get_class($base);
    $typ = $base->getTypeInfo();
    $attrs=$typ["attributes"];
    $query="SELECT * FROM $tableName ORDER BY treeidx";
    $ret = $base->loadObjectsSQL($query,$list, TRUE);
    
    for( $i=0; isset($list[$i]); $i++ ) 
    {
        /* Visszakommentezni kesobb
        if( !is_numeric($HTTP_POST_VARS["treedepth_$i"]) || 
            $HTTP_POST_VARS["treedepth_$i"]<0 )
        {
            $whatHappened = "invalid_form";
            $infoText = sprintf($lll["TLP40"], 
                                $HTTP_POST_VARS["treedepth_$i"]);
            return ok;
        }
        */
        if( !is_numeric($HTTP_POST_VARS["treeidx_$i"]) || 
            $HTTP_POST_VARS["treeidx_$i"]<0 )
        {
            $whatHappened = "invalid_form";
            $infoText = sprintf($lll["TLP50"], 
                                $HTTP_POST_VARS["treeidx_$i"]);
            return ok;
        }
        $list[$i]->treeidx = $HTTP_POST_VARS["treeidx_$i"];
        // Visszakommentezni kesobb
        //$list[$i]->treedepth = $HTTP_POST_VARS["treedepth_$i"];
    } 
    usort($list, "treeCmp");                    
    /* Visszakommentezni kesobb
    if( $list[0]->treedepth!= 0 )
    {
        $whatHappened = "invalid_form";
        $infoText = $lll["TLP60"];
        return ok;
    }
    */
    for( $i=1; isset($list[$i]); $i++ ) 
    {
        if( $list[$i]->treeidx==$list[$i-1]->treeidx )
        {
            $whatHappened = "invalid_form";
            $infoText = sprintf($lll["TLP70"],$list[$i]->treeidx);
            return ok;
        }
        /* Visszakommentezni kesobb
        if( $list[$i]->treedepth - $list[$i-1]->treedepth > 1 )
        {
            $whatHappened = "invalid_form";
            $infoText = sprintf($lll["TLP80"],
                                  $list[$i-1]->name,
                                  $list[$i]->name);
            return ok;
        }
        */
    }
    for( $i=0; isset($list[$i]); $i++ ) 
    {
        modify($list[$i]);
    }
    reindexAllTreeAndRebuildUps( $tableName, "" );
    $infoText = $lll["TLP90"];
    $gorumroll->fromclass = "forum";   
    $gorumroll->frommethod = "treeorganiser_form";   
    $gorumroll->fromid = 0;
    $whatHappened = "invalid_form";
    return ok;
}

function treeCmp($base1, $base2)
{
    if( $base2->treeidx > $base1->treeidx ) return -1;
    else if( $base2->treeidx == $base1->treeidx) return 0;
    else return 1;
}

function reindexAllTree($tableName,$where)
{
    global $treeIdxBase;
    global $infoText;

    $query="SELECT id, up FROM $tableName";
    if ($where!="") $query.=" WHERE $where ";
    $query.=" ORDER BY treeidx";
    $result=executeQuery($query);
    $num = mysql_num_rows($result);
    $dist=(int)($treeIdxBase / ($num+1) );//new distance
    if ($dist<1) {
        $txt = $lll["TLP20"];
        handleError($txt);
    }
    $idx=$dist;
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_row($result);
        $query="UPDATE $tableName SET treeidx=$idx".
               " WHERE id=".$row[0];
        $result1=executeQuery($query);
        $idx+=$dist;
    }
    return ok;
}
function reindexAllTreeAndRebuildUps($tableName,$where)
{
    global $treeIdxBase;
    global $infoText;

    $query="SELECT id,treeidx,up,treedepth FROM $tableName";
    if ($where!="") $query.=" WHERE $where ";
    $query.=" ORDER BY treeidx";
    $result=executeQuery($query);
    $num = mysql_num_rows($result);
    $dist=(int)($treeIdxBase / ($num+1) );//new distance
    if ($dist<1) {
        $txt = $lll["TLP20"];
        handleError($txt);
    }
    $idx=$dist;
    $up[0]=0;
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_array($result, MYSQL_ASSOC);
        $up[$row["treedepth"]+1]=$row["id"];
        $query="UPDATE $tableName SET treeidx=$idx".
               " , up=".$up[$row["treedepth"]].
               " WHERE id=".$row["id"];
        $result1=executeQuery($query);
        $idx+=$dist;
    }
    
    // setting hasChild:
    $query="SELECT id,up, name FROM $tableName";
    if ($where!="") $query.=" WHERE $where ";
    $query.=" ORDER BY treeidx";
    $result=executeQuery($query);
    $num = mysql_num_rows($result);
    $row=mysql_fetch_row($result);
    for($i=0;$i<$num;$i++) {
        if( $i<$num-1 ) 
        {
            $nextRow = mysql_fetch_row($result);
            if( $nextRow[1]==$row[0] ) // a kovetkezo up-ja egyenlo az elozo id-jevel
            {
                $hasChild="1";
            }
            else $hasChild="0";
        }
        else $hasChild="0";    
        $query="UPDATE $tableName SET hasChild='$hasChild'".
               " WHERE id=".$row[0];
        $result1=executeQuery($query);
        if( $i<$num-1 ) $row = $nextRow; 
    }
    return ok;
}
function treeOrganiserForm($base, &$s, $filledFromSavedValues=TRUE)
{
    global $gorumroll,$applName,$lll,$xi;
    
    hasAdminRights( $isAdm );
    if( !$isAdm )
    {
        handleError($lll["permission_denied"]);
    }
    // loadHtmlList
    $tableName = $applName."_".get_class($base);
    $typ = $base->getTypeInfo();
    $attrs=$typ["attributes"];
    $query="SELECT * FROM $tableName ORDER BY treeidx";
    $base->loadObjectsSQL($query,$list, TRUE);
    spreadIndexes( $list );                  
    $maxdepth=0;
    foreach($list as $obj) 
    {
        if ($obj->treedepth > $maxdepth) 
        {
            $maxdepth = $obj->treedepth;
        }
    }
    
    // showHtmlList
    $s = "";
    $tempRoll = $gorumroll;
    $tempRoll->method = "treeorganiser";
    $s.=$tempRoll->generFormHeaderAndHiddenFields();

    $s.=generBoxUp();
    
    // showListHeader    
    $s.="<tr class='header'>";
    $first=TRUE;
    foreach( $attrs as $attr=>$val ) 
    {
        if (in_array("organiser",$val)) 
        {
            $s.="<th nowrap";
            if( $first  ) 
            {
                $csp=$maxdepth+1;
                $s.=" colspan='$csp'";
                $first = FALSE;
            }
            $s.=">";
            $s.="<span class='header'>";
            if (isset($lll[$gorumroll->list."_".$attr])) {
                $s.=$lll[$gorumroll->list."_".$attr];
            }
            else $s.=$lll[$attr];
            $s.="</span>";
            $s.="</th>\n";
        }
    }    
    $s.="</tr>\n";

    // showHtmlList continued
    $modulus=5;//depth num
    for( $i=0; isset($list[$i]); $i++ ) 
    {
        $s.="<tr class='cell'>";
        for( $j=0;$j<$list[$i]->treedepth; $j++ ) 
        {
            $k=$j % $modulus;
            $s.="<td class='cell' width='1'>";
            //$s.="<td class='forumrow$k' width='1'>";TODO
            $s.="<img src='$xi/b.gif' width='10'>";
            $s.="</td>\n";
        }
        $first = TRUE;
        foreach( $attrs as $attr=>$val ) 
        {
            if (in_array("organiser",$val)) 
            {
                $c=$list[$i]->treedepth % $modulus;
                $s.="<td class='cell'";
                //$s.="<td class='forumrow$c'";TODO
                if ($first) 
                {
                    $csp=$maxdepth+1-$list[$i]->treedepth;
                    if ($csp>1) $s.=" colspan='$csp'";
                    $first = FALSE;
                }
                if (in_array("centered",$val)) $s.=" align='center'";
                $s.=">";
                $s.=$list[$i]->showListVal($attr, $i, $filledFromSavedValues);
                $s.="</td>\n";
            }
        } 
        $s.="</tr>\n";
    }
    $s.="<tr><td align='center' colspan='5' class='cell'>";
    $s.="<input type='submit' value='".$lll["organise"].
        "' name='submit' class='button'>\n";
    //Kikommenteztem, mert hibat adott, es nem iazan kell ez.    
    //$s.="<input type='submit' value='".$lll["org_back"].
    //    "' name='submit'>\n"; 
    $s.="</td></tr>\n";
    $s.=generBoxDown();
    
    if (!isset($list[0])) $s.=$lll["emptylist"]."\n";
    
    
}

function spreadIndexes(&$arr,$dist=10)
{
    $idx=$dist;
    for($i=0;isset($arr[$i]);$i++) {
        $arr[$i]->treeidx=$idx;
        $idx+=$dist;
    }
    return ok;
}

?>
