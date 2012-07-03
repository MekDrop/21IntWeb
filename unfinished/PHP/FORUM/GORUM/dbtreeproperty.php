<?php
//relType: can be brothers or children
//fields: string after SELECT e.g. * ; title,url
function makeSqlGetRelatives($base, $relType,$fields,&$query)
{
    global $applName,$noTreeIdx;

    if ($relType!="brothers" && $relType!="children") {
        handleError("brothers or children for makeSqlGetRelatives!");
    }
    $typ = $base->getTypeInfo();
    $tableName=$applName."_".get_class($base);
    if ($relType=="brothers") {
        if (!isset($base->up)) {
            handleError("up not set in makeSqlGetRelatives!");
        }
        else {
            $query="SELECT $fields FROM ".
                   $tableName.
                   " WHERE up='$base->up'";
        }
    }
    if ($relType=="children") {
        if (!isset($base->id)) {
            handleError("id not set in makeSqlGetRelatives!");
        }
        else {
            $query="SELECT $fields FROM ".
                   $tableName.
                   " WHERE up='".$base->id."'";
        }
    }        
    if (!isset($noTreeIdx)) $query.=" ORDER BY treeidx";
}
function getRelativesFromDb($base, $relType,$fields,&$relatives)
{


    makeSqlGetRelatives($base, $relType,$fields,$query);
    $base->loadObjectsSQL($query,$relatives,TRUE);
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
    global $lll;
    global $infoText;
    
    if ($deep==0) $ancestors=array();
    if (!isset($base->up)) {
        handleError("up not set in getAncestors!");
    }
    $deep++;
    if ($deep>10) {//too deep, may be error in structure!!!
        $txt=$lll["deep_struct"];
        $infoText = $txt;
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
        $txt=$lll["no_father"];
        $infoText = $txt;
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
function deleteRec( $base, $whereFields="" )
{
    global $lll;
    

    if (isset($base->up)) {//recursive delete
        if (!isset($base->id)) {
            $ret = $base->load($whereFields);
            if ($ret==not_found_in_db) {
                return $ret;
            }
        }
        getChildrenFromDb($base,$children);
        foreach($children as $child) {
            $ret=$child->deleteRec($whereFields);
            if ($ret!=ok) {
                handleError("child not exists in delete");
            }
        }
        $base->delete($whereFields);
    }
    return ok;
}
?>
