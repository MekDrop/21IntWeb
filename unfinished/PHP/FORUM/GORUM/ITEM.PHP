<?php

if (!isset($immediateAppear)) $immediateAppear=TRUE;

$item_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "cid"=>array(
                // Ez az attr altalaban hidden, mivel automatikusan 
                // beallitodik arra a kategoriara, ahol a user eppen
                // van. Ha megis classselection-kent akarjuk latni
                // (pl Patrick), akkor az applikacioban el kell 
                // tavolitani a kovetkezo form hiddent, valamint a 
                // a cName attr-hoz form invisible-t kell irni
                "form hidden",
                "type"=>"INT",
                "classselection",
                "class"=>$categoryClassName,
                "labelAttr"=>"wholeName",
                "ordered"=>"wholeName ASC",
            ),
            "firstCid"=>array(
                // Ez mondja meg, hogy melyik fo kategoriaban van az 
                // item. Ez a fokategoriankenti kereseshez kell.
                "type"=>"INT",
                "form invisible",
            ),
            "cName"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"120",
                "details",
                "form readonly",
                "no column",
            ),
            "title"=>array(
                "type"=>"VARCHAR",
                "text",
                "mandatory",
                "min" =>"1",
                "max" =>"120",
                "list",
                "details",
            ),
            "creationtime"=>array(
                "type"=>"INT",
                "form invisible",
                "details"
                //"list",
            ),            
            "active"=>array(
                "type"=>"INT",
                "bool",
                "default"=>1,
                //"details",
                "create_form: form invisible",
                //"form readonly",
                "form invisible",
            ),
            "clicked"=>array(
                "type"=>"INT",
                "form invisible"
            ),
            "responded"=>array(
                "type"=>"INT",
                "form invisible"
            ),
            "ownerId"=>array(
                "type"=>"INT",
                "form invisible"
            )
        ),    
        "primary_key"=>"id",
        "delete_confirm"=>"title",
        "sort_criteria_attr"=>"creationtime",
        "sort_criteria_dir"=>"d"
    );
    
class Item extends Object
{

function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $lll,$generalRight,$gorumrecognised,$gorumuser;
    global $applName,$itemClassName;
    $hasRight=FALSE;
    $generalRight = FALSE;
    if( $method==Priv_load )
    {
        $hasRight=TRUE;
    }
    elseif( !$gorumrecognised )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }
    elseif( $gorumuser->isAdm )
    {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }
    elseif( $method==Priv_create )
    {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }
    elseif( isset($this->ownerId) && $this->ownerId==$gorumuser->id )
    {
        $hasRight=TRUE;
    }
    // Ez akkor van, mikor a modify az invalid formra visz:
    elseif( !isset($this->ownerId) && $method==Priv_modify )
    {
        if( isset($this->id ) )
        {
            $query = "SELECT ownerId ".
                     "FROM $applName"."_$itemClassName ".
                     "WHERE id=$this->id";
            $ret = loadSql( $this, $query );
            $hasRight = (!$ret && $this->ownerId==$gorumuser->id);
        }
        else $hasRight = FALSE;
    }
    if( !$hasRight && $giveError )
    {
        handleError($lll["permission_denied"]);
    }
} 

function showListVal($attr)
{
    global $itemClassName, $categoryClassName, $lll;

    $s="";
    if ($attr=="cName") {
        if( !$this->cName )
        {
            $cat = new $categoryClassName;
            $cat->id = $this->cid;
            load($cat);
            $this->cName = $cat->name;
        }
        $tempRoll = new Roll;
        $tempRoll->method = "showhtmllist";
        $tempRoll->list = $categoryClassName;
        $tempRoll->rollid = $this->cid;
        $s=$tempRoll->generAnchor(htmlspecialchars($this->cName), "itemtitle");
    }    
    elseif ($attr=="title") {
        $tempRoll = new Roll;
        $tempRoll->method = "showdetails";
        $tempRoll->list = $itemClassName;
        $tempRoll->rollid = $this->id;
        saveInFromFrom($tempRoll);
        $s.=$tempRoll->generAnchor($this->title, "itemtitle");
    }    
    elseif( $attr=="active" ) {
        if ($this->active) $s=$lll["yes"];
        else $s=$lll["no"];
    }
    elseif( $attr=="creationtime" )
    {
        $s=showTimestamp($this->{$attr});
    }    
    else
    {
        $s=htmlspecialchars($this->{$attr});
    }
    return $s;
}  
  
function getListSelect()
{
    global $itemClassName, $categoryClassName, $applName;
    global $searchClassName;
    global $item_typ, $gorumroll, $gorumuser, $immediateAppear;
    
    // Az adott user altal birtokolt itemek (ebben az esetben a cName-et
    // is bevesszuk a listaba):
    if ($gorumroll->list==$itemClassName."_my") {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE ownerId='$gorumuser->id' AND c.id=n.cid";
        $item_typ["attributes"]["cName"][]="list";
        $item_typ["attributes"]["clicked"][]="list";
        $item_typ["attributes"]["responded"][]="list";
        if(!$immediateAppear)$item_typ["attributes"]["active"][]="list";
    }
    //osszes aktiv
    elseif ($gorumroll->list==$itemClassName."_active") {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE c.id=n.cid".
                  " AND n.active='1'";
        $item_typ["attributes"]["cName"][]="list";
    }
    //egy kereses eredmenye:
    elseif ($gorumroll->list==$itemClassName."_search") {
        $search = new $searchClassName;
        $search->id=$gorumuser->id;
        load($search);
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE c.id=n.cid".
                  " AND n.active='1' AND $search->query";
        $item_typ["attributes"]["cName"][]="list";
    }
    //nem aktiv items
    elseif ($gorumroll->list==$itemClassName."_inactive") {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE c.id=n.cid".
                  " AND n.active='0'";
        $item_typ["attributes"]["cName"][]="list";
    }
    //a legnepszerubbek listaja
    elseif ($gorumroll->list==$itemClassName."_popular") {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE c.id=n.cid".
                  " AND n.active='1'";
        $item_typ["attributes"]["cName"][]="list";
        $item_typ["attributes"]["clicked"][]="list";
        $item_typ["attributes"]["responded"][]="list";
        $item_typ["sort_criteria_sql"]="clicked DESC, creationtime DESC";
    }
    // Egy adott kategoria itemjei:
    else {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE cid='$gorumroll->rollid' AND c.id=n.cid".
                  " AND n.active='1'";
    }
    return $select;
}

function getSelect()
{
    global $itemClassName, $categoryClassName, $applName;
    global $item_typ, $gorumroll;
    
    if( !isset($this->id) || !$this->id ) $select="";
    else
    {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                 "WHERE c.id=n.cid AND n.id='$this->id'";
    }         
    return $select;
}

function showNewToolPlusUrl(&$tempRoll)
{
    global $gorumroll;

    $tempRoll->cid = $gorumroll->rollid;

}

function create($overrulePrivilege=FALSE)
{
    global $categoryClassName,$immediateAppear;
    
    $this->active=$immediateAppear;
    $c = new $categoryClassName;
    $c->id = $this->cid;
    load($c);
    $c->cacheFatherObjects();
    global $fatherCatList;
    $this->firstCid = isset($fatherCatList[0]) ? 
                      $fatherCatList[0]->id : $this->cid;
    Object::create($overrulePrivilege);
    if( $this->active ) $c->increaseDirectItemNum();
    
} 

function modify( $whereFields="", $overrulePrivilege=FALSE )
{
    global $immediateAppear,$gorumuser, $whatHappened;
    Object::modify( $whereFields, $overrulePrivilege );
    if ($whatHappened=="form_submitted" && !$immediateAppear && 
        !$gorumuser->isAdm) 
    {
        $this->active=FALSE;
        modify($this);
    }
}

function delete( $whereFields="", $overrulePrivilege=FALSE )
{
    global $categoryClassName;
    
    load($this);
    Object::delete($whereFields,$overrulePrivilege);
    if( $this->active )
    {
        $c = new $categoryClassName;
        $c->id = $this->cid;
        $ret=load($c);
        $c->decreaseDirectItemNum();
    }    
}

function showDetails(&$s)
{
    global $lll,$gorumroll,$infoText, $gorumuser;
    $this->id = $gorumroll->rollid;
    $ret=load($this);
    if ($ret==not_found_in_db) {
        $infoText = $lll["not_found_deleted"];
        return ok;
    }
    if( $gorumuser->id!=$this->ownerId && !$gorumuser->isAdm )
    {
        $this->clicked++;
        modify($this);
    }
    $ret = Object::showDetails($s,"",FALSE);
    return $ret;
}

}
?>
