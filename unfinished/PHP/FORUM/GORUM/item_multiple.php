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
                "type"=>"TEXT",
                "multipleclassselection",
                "class"=>$categoryClassName,
                "labelAttr"=>"wholeName",
                "ordered"=>"wholeName ASC",
                "no column",
                "mandatory",
                "min"=>"1",
                "size"=>"5"
            ),
            "primaryCid"=>array(
                "type"=>"INT",
                "classselection",
                "class"=>$categoryClassName,
                "labelAttr"=>"wholeName",
                "ordered"=>"wholeName ASC",
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
                "create_form: form invisible",
                "modify_form: form hidden",
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
    
$dbClasses[]="itemnode";

$itemnode_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment"
            ),
            "cid"=>array(
                "type"=>"INT"
            ),
            "firstCid"=>array(
                "type"=>"INT"
            ),
            "iid"=>array(
                "type"=>"INT"
            )
        ),    
        "primary_key"=>"id"
    );

class ItemNode extends Object
{
}

class Item extends Object
{

function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $lll,$generalRight,$gorumrecognised,$gorumuser;
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
    if( !$hasRight && $giveError )
    {
        handleError($lll["permission_denied"]);
    }
} 

function showListVal($attr)
{
    global $itemClassName, $categoryClassName, $lll;

    $s="";
    if ($attr=="cName" || $attr=="firstCid") {
        $s=$this->{$attr};
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
                  "WHERE ownerId='$gorumuser->id' AND c.id=n.primaryCid";
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
                  "WHERE c.id=n.primaryCid".
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
                  "WHERE c.id=n.primaryCid".
                  " AND n.active='1' AND $search->query";
        $item_typ["attributes"]["cName"][]="list";
    }
    //nem aktiv items
    elseif ($gorumroll->list==$itemClassName."_inactive") {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE c.id=n.primaryCid".
                  " AND n.active='0'";
        $item_typ["attributes"]["cName"][]="list";
    }
    //a legnepszerubbek listaja
    elseif ($gorumroll->list==$itemClassName."_popular") {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "c.wholeName AS cName ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_$categoryClassName AS c ".
                  "WHERE c.id=n.primaryCid".
                  " AND n.active='1'";
        $item_typ["attributes"]["cName"][]="list";
        $item_typ["attributes"]["clicked"][]="list";
        $item_typ["sort_criteria_sql"]="clicked DESC, creationtime DESC";
    }
    // Egy adott kategoria itemjei:
    else {
        $select = "SELECT ".getAllColumns($item_typ, "n").
                  "i.iid AS iid ".
                  "FROM $applName"."_$itemClassName AS n, ".
                  $applName."_itemnode AS i ".
                  "WHERE i.cid='$gorumroll->rollid' AND n.id=i.iid".
                  " AND n.active='1'";
    }
    return $select;
}

function showNewToolPlusUrl(&$tempRoll)
{
    global $gorumroll;

    if( $gorumroll->rollid ) $tempRoll->cid = $gorumroll->rollid;
    else $tempRoll->cid = 1;

}

function createForm(&$s)
{
    global $gorumroll;
    
    if( $gorumroll->invalid ) $gorumroll->invalid=FALSE;
    elseif( isset($this->cid) && $this->cid )
    {   
        $this->primaryCid = $this->cid;
        $this->cid = array($this->cid);
    }
    $this->hasObjectRights($hasRight, Priv_create, TRUE);
    $this->generForm($s);
}

function modifyForm(&$s)
{
    global $gorumroll, $lll, $applName;
    
    if( !$gorumroll->invalid )
    {    
        $ret = $this->load();
        if( $ret )
        {
            $txt = $lll["not_found_in_db"];
            handleError($txt);
        }
        $query = "SELECT cid FROM $applName"."_itemnode WHERE iid='$this->id'";
        $itemNodes = new ItemNode;
        loadObjectsSql( $itemNodes, $query, $itemNodes );
        $this->cid = array();
        foreach( $itemNodes as $itemNode )
        {
            $this->cid[]=$itemNode->cid;
        }    
    } 
    else $gorumroll->invalid = FALSE;
    $this->hasObjectRights($hasRight, Priv_modify, TRUE);
    $this->generForm($s);
}

function create($overrulePrivilege=FALSE)
{
    global $categoryClassName,$immediateAppear, $whatHappened;
    global $fatherCatList;
    
    $this->active=$immediateAppear;
    Object::create($overrulePrivilege);
    if( $whatHappened!="form_submitted" ) 
    {
        return;
    }
    if( !in_array($this->primaryCid, $this->cid) )
    {
        $this->cid[]=$this->primaryCid;
    }
    foreach( $this->cid as $cid )
    {
        $itemNode = new ItemNode;
        $itemNode->cid = $cid;
        $itemNode->iid = $this->id;
        $c = new $categoryClassName;
        $c->id = $cid;
        load($c);
        $fatherCatList = 0;
        $c->cacheFatherObjects();
        $itemNode->firstCid = isset($fatherCatList[0]) ? 
                              $fatherCatList[0]->id : $cid;
        create($itemNode);                      
        if( $this->active ) $c->increaseDirectItemNum();
    }
} 

function modify( $whereFields="", $overrulePrivilege=FALSE )
{
    global $immediateAppear,$gorumuser, $whatHappened, $fatherCatList;
    global $categoryClassName, $applName;
    Object::modify( $whereFields, $overrulePrivilege );
    if( $whatHappened!="form_submitted" ) 
    {
        return;
    }
    if (!$immediateAppear && !$gorumuser->isAdm) 
    {
        $this->active=FALSE;
        modify($this);
    }
    if( !in_array($this->primaryCid, $this->cid) )
    {
        $this->cid[]=$this->primaryCid;
    }
    $query = "SELECT cid, id FROM $applName"."_itemnode WHERE iid='$this->id";
    $itemNodes = new ItemNode;
    $oldCategoryIds = array();
    foreach( $itemNodes as $itemNode )
    {
        if( !is_array( $itemNode->cid, $this->cid) )
        {
            delete( $itemNode );
            if( $this->active ) 
            {
                $c = new $categoryClassName;
                $c->id = $itemNode->cid;
                load($c);
                $c->decreaseDirectItemNum();
            }    
        }
        else $oldCategoryIds[]=$itemNode->cid;
    }
    foreach( $this->cid as $cid )
    {
        if( !is_array($cid, $oldCategoryIds) )
        {
            $itemNode = new ItemNode;
            $itemNode->cid = $cid;
            $itemNode->iid = $this->id;
            $c = new $categoryClassName;
            $c->id = $cid;
            load($c);
            $fatherCatList = 0;
            $c->cacheFatherObjects();
            $itemNode->firstCid = isset($fatherCatList[0]) ? 
                                  $fatherCatList[0]->id : $cid;
            create($itemNode);                      
            if( $this->active ) $c->increaseDirectItemNum();
        }
    }
}

function delete( $whereFields="", $overrulePrivilege=FALSE )
{
    global $categoryClassName, $applName;
    
    load($this);
    Object::delete($whereFields,$overrulePrivilege);
    $query = "SELECT cid, id FROM $applName"."_itemnode WHERE iid='$this->id";
    $itemNodes = new ItemNode;
    foreach( $itemNodes as $itemNode )
    {
        delete( $itemNode );
        if( $this->active ) 
        {
            $c = new $categoryClassName;
            $c->id = $itemNode->cid;
            load($c);
            $c->decreaseDirectItemNum();
        }    
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
