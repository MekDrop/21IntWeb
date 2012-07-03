<?php

// TypeInfo Help
// ---------------------------------
// A kovetkezo pelda egy olyan attributumot mutat, ami a DB-ben INT-kent
// van tarolva, default erteke 1, es auto increment.
// - Az a_form-ban text mezokent jelenik meg - a mezo hossza 10, az 
//   input hossza 20.
// - A b_form-ban textarea-kent jelenik meg, 30x40-es mezoben,
// - A c_form-ban multiple selection-kent jelenik meg, a lista 
//   eredmenyekent kaphato ertekek: 1, 2, 3, 4. A listaban lathato 
//   sorok: "egy", "ketto", "harom", "negy". A lista magassaga 2.
// - Az attributum minden mas form eseten date-mezokent jelenik meg.
//
// Az attributum az a_form-ban mandatory, a d_formban hidden, az 
// e_form-ban pedig invisible.
//
//
//    "textareaAttr"=>array(
//        "type"=>"INT",
//        "default" => 1,
//        "auto increment",
//        "a_form: text",
//        "max" => 10,
//        "length" => 20,
//        "b_form: textarea",
//        "rows" => 30,
//        "cols" => 40,
//        "c_form: multipleselection",
//        "values" => array(1,2,3,4),
//        "size" => 2,
//        "date",
//        "a_form: mandatory",
//        "d_form: form hidden",
//        "e_form: form invisible"
//     )
//
// Az SQL tablat meghatarozo kulcsszavak osztalyszinten:
// tableName, primary_key, unique_keys, keys, heap, select
//
// Az SQL tablat meghatarozo kulcsszavak attributumszinten:
// type, default, auto increment, no column, length, max, values
//
// A form egeszet meghatarozo kulcsszavak:
// submit, delete_confirm
//
// Az egyes form mezoket meghatarozo kulcsszavak:
// form hidden, form invisible, mandatory
// text, textarea, readonly, password, button, file, multipleselection,
// selection, date, bool, radio, classselection
// max, min, length, rows, cols, values, names, size, class, labelAttr,
// query
//
// A listas megjelenest meghatarozo kulcsszavak:
// list, sorta, sortd, notools, centered, 
// sort_criteria_attr, sort_criteria_dir, sort_criteria_sql
//
// Egyeb kulcsszavak: details
//
// Specialitasok:
// - a selection mezo egy olyan erteket var, ami a values tombben benne
//   van - ez lesz a listaban alapbol szelektalva. Ha nem ilyet kap, az 
//   elso elem lesz a listaban kiszelektalva. Vissza is egy ilyen
//   erteket ad.
// - a multipleselection mezo olyan ertekeknek egy tombjet varja, 
//   amelyek a values tombben szerepelnek - ezek lesznek a listaban 
//   alapbol szelektalva. Ha a tomb ures (vagy nem is tomb), akkor semmi 
//   se lesz szelektalva. Vissza is egy ilyen tombot ad, vagy ha semmit
//   se valasztottunk ki akkor az az init() altal megallapitott default
//   marad.
// - a date mezo egy timestamp-et var. Ha nullat kap, az aktualis 
//   datum lesz. Vissza egy aszociativ tombot ad az attributumban, ahol
//   a kulcsok: year, month es day. 
// - a radio mezo ugyanugy mukodik, mint a selection  
//
// A date tipusu mezonek a DB-ben INT-nek kell lenni, a 
// multipleselectionnak pedig valamilyen sztringnek


class Object
{
function & getTypeInfo()
{
    
    $typ = get_class($this)."_typ";
    global ${$typ};
    if( !isset(${$typ}) )
    {
        $parent = get_parent_class($this);
        $typ = $parent."_typ";
        global ${$typ};
        if( !isset(${$typ}) )
        {
            $obj = new $parent;
            $typ = get_parent_class($obj)."_typ";
            global ${$typ};
            if( !isset(${$typ}) )
            {
            }
        }
    }
    return ${$typ};
}

function getVisibility( $typ, $attr )
{
    global $gorumroll;
    
    $attrInfo = & $typ["attributes"][$attr];
    // Ez a fg nem csak a formok esteben lesz meghivva:
    $method = strstr($gorumroll->method, "_form") ?
              $gorumroll->method : $gorumroll->method."_form";
    if( in_array("$method: form invisible",$attrInfo) ){
        $visibility = Form_invisible;
    }
    elseif( in_array("$method: form hidden",$attrInfo) ){     
        $visibility = Form_hidden;
    }
    elseif( in_array("$method: readonly",$attrInfo) ){     
        $visibility = Form_readonly;
    }
    elseif( in_array("form hidden",$attrInfo) ){     
        $visibility = Form_hidden;
    }
    elseif( in_array("form invisible",$attrInfo) ){     
        $visibility = Form_invisible;
    }
    elseif( in_array("form readonly",$attrInfo) ){     
        $visibility = Form_readonly;
    }
    else 
    {
        $visibility = Form_visible;
    }
    return $visibility;
}

function init( $members="" )
{
    $typ = $this->getTypeInfo(); 
    $attrs = array_keys( $typ["attributes"] );
    foreach( $members as $attr=>$value )
    {
        if( isset($members[$attr]) && 
            in_array($attr, $attrs) )
        {
            $this->{$attr} = $value;
        }
        else if( !isset($members[$attr]) ) {
            unset($this->{$attr});
        }
    }
    return ok;
}
     
function getDefault( $typ, $attr )
{
    if( !isset( $typ["attributes"][$attr] ) ) {
        return "";
    }
    else $attrInfo = & $typ["attributes"][$attr];
    //if (!isset($attrInfo["type"])) echo "attr:$attr ";
    if( isset($attrInfo["default"]) ) {
        return $attrInfo["default"];
    }
    if( ereg("INT", $attrInfo["type"]) ) {
        return 0;
        // Note: even if min==1 !!!
    }
    if( ereg("CHAR", $attrInfo["type"]) ) {
        return "";
        // Note: even if min==1 !!!
    }
    if( ereg("TEXT", $attrInfo["type"]) ) {
        return "";
    }
} 

function validAttribute( $typ, $attr )
{
    global $lll;
    global $infoText, $whatHappened;
    global $gorumroll, $HTTP_POST_FILES;
    
    $value = $this->{$attr};
    if( !isset( $typ["attributes"][$attr] ) ) 
    {
        return ok;
    }
    else $attrInfo = & $typ["attributes"][$attr];
    
    $className = $gorumroll->class;
    $attrName = "";
    if (isset($lll[$className."_".$attr])) 
    {
        $attrName=$lll[$className."_".$attr];
    }
    else if( isset($lll[$attr]) ) $attrName=$lll[$attr];
    if( is_array($value) )  // vagy multipleselection, vagy date
    {
        if( ereg("INT", $attrInfo["type"]) ||
            ereg("DATE", $attrInfo["type"]) )  // date
        {
            if( isset($this->{$attr}["month"]) &&
                !checkdate($this->{$attr}["month"],
                           $this->{$attr}["day"],
                           $this->{$attr}["year"]) )
            {
                $infoText=$lll["invalidDate"];
                $whatHappened = "invalid_form";
                return ok;
            }               
        }
        else
        {
            if( isset($attrInfo["min"]) && count($value)<$attrInfo["min"] )
            {
                $infoText = sprintf( $lll["selectAtLeastOne"], 
                                     $attrInfo["min"], $attrName );
                $whatHappened = "invalid_form";                    
                return ok;
            }
        }
        return ok;
    }
    if (!isset($attrInfo["type"])) var_dump($attrInfo);
    if( ereg("INT", $attrInfo["type"]) )
    {
        if( !ereg("^[0-9-]*$", $value))
        {
            $infoText = sprintf( $lll["mustBeInt"],$attrName);
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["min"]) && $value<$attrInfo["min"] )
        {
            $infoText = sprintf( $lll["mustBeGreaterInt"], 
                                   $attrName, $attrInfo["min"] );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["max"]) && $value>$attrInfo["max"] )
        {
            $infoText = sprintf( $lll["mustBeSmallerInt"], 
                                   $attrName, $attrInfo["max"] );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        return ok;
    }
    if( ereg("FLOAT", $attrInfo["type"]) )
    {
        if( $value && !ereg("^-?[0-9]*\.?[0-9]+$", $value))
        {
            $infoText = sprintf( $lll["mustBeFloat"],$attrName);
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["min"]) && $value<$attrInfo["min"] )
        {
            $infoText = sprintf( $lll["mustBeGreaterInt"], 
                                   $attrName, $attrInfo["min"] );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["max"]) && $value>$attrInfo["max"] )
        {
            $infoText = sprintf( $lll["mustBeSmallerInt"], 
                                   $attrName, $attrInfo["max"] );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        return ok;
    }
    if( ereg("CHAR", $attrInfo["type"]) )
    {
        if( gettype($value)!="string" )
        {
            $infoText = sprintf( $lll["mustBeString"], $attrName );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["min"]) &&
            strlen($value)<$attrInfo["min"] )
        {
            if ($attrInfo["min"]=="1") {
                $infoText = sprintf( $lll["mandatoryField"], 
                                     $attrName);
            }
            else {
                $infoText = sprintf( $lll["mustBeGreaterString"], 
                                     $attrName, $attrInfo["min"] );
            }
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["max"]) && 
            strlen($value)>$attrInfo["max"] )
        {
            $infoText = sprintf( $lll["mustBeSmallerString"], 
                                 $attrName, $attrInfo["max"] );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( in_array("file", $attrInfo) )
        {
            if (isset($HTTP_POST_FILES[$attr]["name"]) &&
                strstr($HTTP_POST_FILES[$attr]["name"]," "))
            {
                $whatHappened = "invalid_form";
                $infoText = $lll["spacenoatt"];
                return;
            }
        }
        return ok;
    }
    if( ereg("TEXT", $attrInfo["type"]) )
    {
        if( isset($attrInfo["min"]) &&
            strlen($value)<$attrInfo["min"] )
        {
            if ($attrInfo["min"]=="1") {
                $infoText = sprintf( $lll["mandatoryField"], 
                                     $attrName);
            }
            else {
                $infoText = sprintf( $lll["mustBeGreaterString"], 
                                     $attrName, $attrInfo["min"] );
            }
            $whatHappened = "invalid_form";                    
            return ok;
        }
        if( isset($attrInfo["max"]) && 
            strlen($value)>$attrInfo["max"] )
        {
            $infoText = sprintf( $lll["mustBeSmallerString"], 
                                 $attrName, $attrInfo["max"] );
            $whatHappened = "invalid_form";                    
            return ok;
        }
        return ok;
    }
}

function valid()
{
     
    $typ = $this->getTypeInfo();
    $object_vars = get_object_vars($this);
    global $whatHappened;
    foreach( $object_vars as $attr=>$value )
    {
        $this->validAttribute( $typ, $attr );
        if( $whatHappened=="invalid_form" ) break;
    }
}

function copy( $base, $whereFields="" )
{

    $typ = $this->getTypeInfo();
    if( $whereFields=="" ) $whereFields=getPrimaryKey($typ);
    foreach( $whereFields as $attribute )
    {
        if( isset($base->{$attribute}) )
        {
            $this->{$attribute} = $base->{$attribute};
        }
        else
        {
            unset($this->{$attribute});
        }
    }
}

function createForm(&$s)
{
    global $gorumroll;
    
    if( $gorumroll->invalid ) $gorumroll->invalid=FALSE;
    $this->hasObjectRights($hasRight, Priv_create, TRUE);
    $this->generForm($s);
}

function modifyForm(&$s)
{
    global $gorumroll, $lll;
    
    if( !$gorumroll->invalid )
    {    
        $ret = $this->load();
        if( $ret )
        {
            $txt = $lll["not_found_in_db"];
            handleError($txt);
        }
    } 
    else $gorumroll->invalid = FALSE;
    $this->hasObjectRights($hasRight, Priv_modify, TRUE);
    $this->generForm($s);
}

function deleteForm(&$s)
{
    global $gorumroll;
    global $lll;

    $ret=$this->load();  // Ez azert kell, kogy a hasRights mukodjon
    if( $ret )
    {
        $txt = $lll["not_found_in_db"];
        handleError($txt);
    }
    $this->hasObjectRights($hasRight, Priv_delete, TRUE);
    $this->generDeleteForm($s);
}

function multipleDeleteForm(&$s)
{
    global $gorumroll;
    global $lll;

    $this->generMultipleDeleteForm($s);
}

function complexActionForm(&$s)
{
    global $gorumroll, $lll;
    
    if( $gorumroll->invalid ) $gorumroll->invalid=FALSE;
    $typ = $this->getTypeInfo();
    $options = $typ["complexes"][$gorumroll->method];
    $class = $gorumroll->class;
    $objects = new $class;
    if( isset($options["query"]) ) $query = $options["query"];
    else
    {
        $query = "SELECT id, ".$options["labelAttr"].
                 " FROM ".$applName."_$class ORDER BY ".
                 $options["labelAttr"];
    }
    loadObjectsSQL( $objects, $query, $objects );
    $this->generComplexForm($s, $objects);
}   

function generComplexForm(&$s, &$objects)
{
    return generComplexForm($this,$s, $objects);
}

function generForm(&$s)
{
    return generForm($this,$s);
}

function generDeleteForm(&$s)
{
    return generDeleteForm($this,$s);
}
function generMultipleDeleteForm(&$s)
{
    return generMultipleDeleteForm($this,$s);
}

function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
     
    $hasRight = FALSE;
    if( $giveError )
    {
        global $lll;
        $txt = $lll["permission_denied"];
        handleError($txt);
    }
} 

function load( $whereFields="",$whatFields="*",$overrulePrivilege=FALSE)
{
    return privilegeLoad($this, $whereFields,$whatFields,
                         $overrulePrivilege) ;
}
   
function loadObjectsSQL($query, &$objArr, $overrulePrivilege=FALSE) 
{
    return loadObjectsSql($this, $query, $objArr );
}   

function create($overrulePrivilege=FALSE)
{
    privilegeCreate($this,$overrulePrivilege);
} 
   
function modify( $whereFields="", $overrulePrivilege=FALSE )
{
    privilegeModify($this, $whereFields, $overrulePrivilege);
} 
      
function delete( $whereFields="", $overrulePrivilege=FALSE )
{
    privilegeDelete($this, $whereFields, $overrulePrivilege);
}
      
function multipleDelete( $overrulePrivilege=FALSE )
{
    privilegeMultipleDelete($this, $overrulePrivilege);
}
      
function complexAction(&$s)
{
    global $gorumroll, $lll, $whatHappened, $infoText;
    global $resultList;
    global $allowedMethods;
    
    $typ = $this->getTypeInfo();
    $options = $typ["complexes"][$gorumroll->method."_form"];
    $class = $gorumroll->class;
    $method = $gorumroll->method;
    foreach( $options["buttons"] as $button=>$selectivity )
    {
        if( $lll[$class."_".$method."_form_".$button]==$gorumroll->submit ) break;
    }
    $length = isset($resultList) ? count($resultList) : 0;
    if( $selectivity=="one selected" && $length>1 )
    {
        $infoText = $lll["onlyOneCanBeSelected"];
        $whatHappened = "invalid_form";
        return;
    }
    elseif( ($selectivity=="one selected" || 
             $selectivity=="more selected") && $length<1 )
    {
        $infoText = $lll["youMustSelectOne"];
        $whatHappened = "invalid_form";
        return;
    }
    elseif( $selectivity!="no selected" && $length==1 &&
            $resultList[0]==0 )  // valaki az (Empty list)-et szelektalta
    {
        $infoText = $lll["youMustSelectOne"];
        $whatHappened = "invalid_form";
        return;
    }
    elseif( $selectivity=="one selected" )
    {
        $this->id = $resultList[0];
    }
    $gorumroll->frommethod = $gorumroll->method."_form";
    $gorumroll->fromlist = $gorumroll->list;
    $gorumroll->method = $button;
    $methodCall = $allowedMethods[$gorumroll->method];
    $base = $this;
    eval($methodCall);
}   

function loadHtmlList(&$list)
{
    return loadHtmlList($this,$list);
}    
function showHtmlList(&$s)
{
    return showHtmlList($this,$s);
}    
function csvExport()
{
    return csvExport($this);
}    
function showJumperTool(&$s)
{
    $s="";
}    
function showOneRow($rights,&$s)
{
    return showOneRow($this,$rights,$s);
}    
function showTools($rights)
{
    return showTools($this,$rights);
}    
function showListHeader($isEmpty=FALSE)
{
    return showListHeader($this, $isEmpty);
}    
function showListEntry($rights,$tdClass="cell")
{
    return showListEntry($this, $rights, $tdClass);
}    
function showSortTool($attr)
{
    return showSortTool($this, $attr);
}    
function showDelTool($rights,$from="showHtmlList")
{
    return showDelTool($this, $rights);
}    
function showDetailsTool()
{
    return showDetailsTool($this);
}    
function showListVal($attr)
{
    return nl2br(htmlspecialchars($this->{$attr}));
}    
function showDetails(&$s, $whereFields="", $withLoad=TRUE,$headText="")
{
    return showDetails($this,$s,$whereFields,$withLoad,$headText);
}    
function showNewTool($rights)
{
    return showNewTool($this,$rights);
}
function showCsvExportTool()
{
    return showCsvExportTool($this);
}
function showModTool($rights)
{
    return showModTool($this,$rights);
}

function showNewToolPlusUrl(&$roll)
{
    return ok;
}    
function showHtmlListTitle(&$s)
{
    $s="";
    return ok;
}
function showObjectTools(&$s)
{
    $s="";
    return ok;
}
function showDetailsMethods()
{
    return "";
}
function hasAdminRights( &$hasRight, $method="" )
{
    return hasAdminRights( $hasRight, $this, $method );
}   
function hasGeneralRights(&$rights)
{
    return hasGeneralRights($this, $rights);
}
function showBelowList() 
{
    return "";
}
function getSelect()
{
    return "";
}
function getLimit()
{
    return getLimit($this);
}
function getOrderBy()
{
    return getOrderBy($this);
}
function getListSelect()
{
    global $applName;
    $tableName = $applName."_".get_class($this);
    $select = "SELECT * FROM $tableName";
    return $select;
}
function getCount(&$count)
{

    $select = $this->getListSelect();
    if( $select=="" ) {
        handleError("No select from getSelect class:".get_class($this));
    }
    $query = ereg_replace("SELECT .* FROM", "SELECT COUNT(*) FROM",
                          $select);
    getDbCount($count, $query);
}
function removeFromTypeInfo($attr,$setting, $typ="")
{
    
    if( $typ )
    {
        global ${$typ};
        $typ = & ${$typ};
    }
    else $typ= & $this->getTypeInfo();
    $found=FALSE;
    for($i=0;isset($typ["attributes"][$attr][$i]);$i++) {
        if ($typ["attributes"][$attr][$i]==$setting) {
            $found=TRUE;
            break;
        }
    }
    if ($found) unset($typ["attributes"][$attr][$i]);
    return $found;
}  
   
function showNavBar()
{
    global $initClassName;
    $init = new $initClassName;
    return $init->showNavBar();
}     

}

?>
