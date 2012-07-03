<?php
$category_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "unsigned",
                "auto increment",
                "form hidden"
            ),            
            "up"=>array(
                "type"=>"INT",
                "form hidden"
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"250",
                "min" =>"1",
                "mandatory",
                "list",
                "details"
            ),
            "wholeName"=>array(
                "type"=>"TEXT",
                "form hidden"
            ),
            "subCatNum"=>array(
                "type"=>"INT",
                "form invisible"
            ),
            "directSubCatNum"=>array(
                "type"=>"INT",
                "form invisible"
            ),
            "itemNum"=>array(
                "type"=>"INT",
                "form invisible"
            ),
            "directItemNum"=>array(
                "type"=>"INT",
                "form invisible"
            )
        ),    
        "primary_key"=>"id",
        "delete_confirm"=>"name",
        "sort_criteria_attr"=>"name",
        "sort_criteria_dir"=>"a"
    );


class Category extends Object
{
function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $lll,$generalRight, $gorumuser, $gorumrecognised;

    hasAdminRights($isAdm);
    $generalRight = FALSE;
    if( $method==Priv_load ) {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }        
    elseif( $isAdm ) {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }
    else {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }
    if( !$hasRight && $giveError ) {
        handleError($lll["permission_denied"]);
    }        
} 

function showListVal($attr)
{
    global $lll,$gorumuser,$gorumroll;
    global $htmlListForum, $categoryClassName;

    $s="";
    if( $attr=="name" )
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $categoryClassName;
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = "$this->id";
        $s=$tempRoll->generAnchor(htmlspecialchars($this->name));
        return $s;
    }
    else
    {
        $s=htmlspecialchars($this->{$attr});
    }
    return $s;
} 

function create($overrulePrivilege=FALSE)
{
    global $categoryClassName;   
     
    $this->wholeName=$this->name;
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $father->increaseDirectSubCatNum();
        $this->wholeName = $father->wholeName." - ".$this->wholeName;
    }
    Object::create($overrulePrivilege);
} 


function modify( $whereFields="", $overrulePrivilege=FALSE )
{
    global $categoryClassName, $applName;
    
    
    // Vigyazva kell barmit is beepiteni meg ebbe a modify-ba, mert
    // rekurziv is hivodhat.
    $this->wholeName=$this->name;
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $this->wholeName = $father->wholeName." - ".$this->wholeName;
    }
    Object::modify($whereFields,$overrulePrivilege);
    $children = new $categoryClassName;
    $query ="SELECT id, up, name FROM $applName"."_$categoryClassName ".
            "WHERE up='$this->id'";
    loadObjectsSql( $children, $query, $children );
    global $recursive;
    foreach( $children as $child ) 
    {
        $recursive=TRUE;         
        $child->modify();  // rekurziv
        $recursive=FALSE;
    }    
    // Ez a recursive kavaras azert kell, hogy pontosan ez a fg hivodjon
    // rekurzivan es ne a leszarmaztatt osztaly modify-ja.    
}


function delete( $whereFields="", $overrulePrivilege=FALSE )
{
    global $categoryClassName, $itemClassName, $applName;

    // Elobb kitoroljuk az itemeit (ha az item egyaltalan be van 
    // inkudalva). Azert kell elotte, mert az 
    // item->delete() hivhat load-ot a categoriara
    if( class_exists($itemClassName) )
    {
        $items = new $itemClassName;
        $query = "SELECT * FROM $applName"."_$itemClassName ".
                 "WHERE cid='$this->id'";
        loadObjectsSql( $items, $query, $items );         
        foreach( $items as $item ) $item->delete();
    }
    Object::delete($whereFields,$overrulePrivilege);

    // Rekurziv esetben elobb a levelre hivodik meg a kovetkezo 
    // kodreszlet, majd az o folott allo objektumra es igy tovabb 
    // egeszen addig az objektumig, amelyre tulajdonkeppen meghivtuk a 
    // delete-et. A subCatNum ennek soran egyesevel csokkenget a lanc
    // tagjaiban:
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $father->decreaseDirectSubCatNum();
    }
} 

function getListSelect()
{
    global $gorumroll, $categoryClassName, $applName;

    $select = "SELECT * FROM $applName"."_$categoryClassName".
              " WHERE up=".(int)$gorumroll->rollid;
    
    return $select;
} 

function showNewToolPlusUrl(&$roll)
{
    global $gorumroll;
    if ($gorumroll->rollid) $roll->up = $gorumroll->rollid;
}   
 
function cacheFatherObjects()
{
    global $fatherCatList;
    global $gorumroll;
    if( (!isset($this->id) || !$this->id) && $gorumroll->rollid ) 
    {
        $this->id=$gorumroll->rollid;
        $ret = load( $this );
        if( $ret ) {
            $txt="can not load category object";
            handleError($txt);
        } 
    }    
    if( !isset($fatherCatList) || !$fatherCatList)
    {
        $fatherCatList = array();
        //if ($withOwn) $fatherCatList[] = $this;
        $childCat = $this;
        while( $childCat->up )
        {
            $childCat->id = $childCat->up;
            $childCat->up = 0;
            $ret = load( $childCat );
            if( $ret ) {
                $txt="can not load child object";
                handleError($txt);
            } 
            $fatherCatList[] = $childCat;
        }
        $fatherCatList=array_reverse($fatherCatList);
    }
}

function increaseDirectItemNum()
{
    $this->directItemNum++;
    $this->increaseItemNum();
}   

function increaseItemNum()
{
    global $categoryClassName;
    
    $this->itemNum++;
    modify( $this ); 
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $father->increaseItemNum();  // rekurziv
    }
}   

function decreaseDirectItemNum()
{
    $this->directItemNum--;
    $this->decreaseItemNum();
}   

function decreaseItemNum( $mennyivel=1 )
{
    global $categoryClassName;
    
    $this->itemNum -= $mennyivel;
    modify( $this ); 
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $father->decreaseItemNum($mennyivel);  // rekurziv
    }
}   

function increaseDirectSubCatNum()
{
    $this->directSubCatNum++;
    $this->increaseSubCatNum();
}   

function increaseSubCatNum()
{
    global $categoryClassName;
    
    $this->subCatNum++;
    modify( $this ); 
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $father->increaseSubCatNum();  // rekurziv
    }
}   

function decreaseDirectSubCatNum()
{
    $this->directSubCatNum--;
    $this->decreaseSubCatNum();
}   

function decreaseSubCatNum()
{
    global $categoryClassName;
    
    $this->subCatNum--;
    modify( $this ); 
    if( $this->up )
    {
        $father = new $categoryClassName;
        $father->id = $this->up;
        load($father);
        $father->decreaseSubCatNum();  // rekurziv
    }
}   


}

?>
