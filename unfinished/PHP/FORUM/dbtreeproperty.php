<?php
//  !!!!!!!!!!!!!!!!!!!!!!!!
//
// EZ A FILE NINCS INCLUDOLVA!
//
//  !!!!!!!!!!!!!!!!!!!!!!!!
//
//  Ki kell torolni innen, ha mar tuti, hogy nem kell.
//  A modositott verzio atkerult a gorumba.
//
//  Ezt ne modositsd!
//
//relType: can be brothers or children
//fields: string after SELECT e.g. * ; title,url
function makeSqlGetRelatives($base, $relType,$fields,&$query)
{
    global $applName;

    if ($relType!="brothers" && $relType!="children") {
        $txt="Incorrect input for makeSqlGetRelatives";
        handleError($txt);
    }
    $tableName = $applName."_".get_class($base);
    if ($relType=="brothers") {
        if (!isset($base->up)) {
            $txt="up not set for makeSqlGetRelatives";
            handleError($txt);
        }
        $query="SELECT $fields FROM ".
               $tableName.
               " WHERE up='$base->up'";
    }
    if ($relType=="children") {
        if (!isset($base->id)) {
            $txt="id not set for makeSqlGetRelatives";
            handleError($txt);
        }
        $query="SELECT $fields FROM ".
               $tableName.
               " WHERE up='".$base->id."'";
    }        
    $query.=" ORDER BY treeidx";
}
function getRelativesFromDb($base, $relType,$fields,&$relatives)
{


    $relatives=array();
    makeSqlGetRelatives($base, $relType,$fields,$query);
    $base->loadObjectsSQL($query,$relatives,TRUE);
    return ok;
}    
function getBrothersFromDb($base, &$brothers,$fields="*")
{
    getRelativesFromDb($base, "brothers",$fields,$brothers);
}
function getChildrenFromDb($base, &$children,$fields="*")
{
    getRelativesFromDb($base, "children",$fields,$children);
}
function getLastChildFromDb($base, &$child,$fields="*")
{
    $child=0;
    getChildrenFromDb($base,$children,$fields);
    if (is_array($children)&&isset($children[0])) {
        $child=$children[count($children)-1];
    }
}
function getLastGrandChildFromDb($base, &$child,$fields="*")
{
    $child=0;
    getChildrenFromDb($base,$children,$fields);
    if (is_array($children)&&isset($children[0])) {
        getLastGrandChildFromDb($children[count($children)-1],
                                $child,$fields);
        if (!is_object($child)) {
            $child=$children[count($children)-1];
        }
    }
}
//rootId is the root->id
function getAncestors(&$base,&$ancestors,$rootId=0,$withOwn=0)
{
    global $connectionLink; 
    static $deep=0;
    
    if ($deep==0) $ancestors=array();
    if (!isset($base->up)) {
        $txt="up not set for getAncestors";
        handleError($txt);
    }
    $deep++;
    if ($deep>10) {//too deep, may be error in structure!!!
        $txt="structure is too deep or incorrect in getAncestors";
        $deep--;
        return deep_struct;
    }    
    if ($withOwn!=0) $ancestors[] = $base;
    if ($base->up==0 || $base->up==$rootId) {
        $deep--;
        return ok;
    }
    $className = get_class($base);
    $a = new $className;
    $a->id=$base->up;
    $ret = $a->load();
    if ($ret==not_found_in_db) {//up not found, we are on the top
        $deep--;
        $txt="no father in getAncestors";
        return no_father;
    }
    if ($ret!=ok) {
        $deep--;
        return $ret;
    }
    $ancestors[] = $a;
    if ($a->up==0 || $a->id==$rootId) {
        $deep--;
        return ok;
    }    
    $ret = getAncestors($a,$ancestors,$rootId);
    $deep--;
    return $ret;
}
?>
