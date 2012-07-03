<?php
// TODO  
function hasAdminRights( &$hasRight, $base="", $method="" )
{
    global $gorumuser;
    global $gorumrecognised;
    $hasRight = ($gorumrecognised && $gorumuser->isAdm);    
    return ok;
}

function hasGeneralRights($base, &$rights)
{
    global $generalRight;
    
    $rights = array();
    $base->hasObjectRights($hasRight, Priv_load);
    if( $generalRight ) $rights[Priv_load] = $hasRight;
    $base->hasObjectRights($hasRight, Priv_create);
    if( $generalRight ) $rights[Priv_create] = $hasRight;
    $base->hasObjectRights($hasRight, Priv_modify);
    if( $generalRight ) $rights[Priv_modify] = $hasRight;
    $base->hasObjectRights($hasRight, Priv_delete);
    if( $generalRight ) $rights[Priv_delete] = $hasRight;
}

function privilegeLoad(&$base, $whereFields="",$whatFields="*",$overrulePrivilege=FALSE) 
{
    global $lll;
    global $gorumuser;
      
    $ret = load( $base, $whereFields, $whatFields );
    if ($ret==not_found_in_db) {
        return $ret;
    }    
    if( $overrulePrivilege )
    {
        return ok;
    }
    $base->hasObjectRights( $hasRight, Priv_load, TRUE );
}

function privilegeCreate(&$base, $overrulePrivilege=FALSE)
{
    global $whatHappened, $infoText, $lll;
    global $gorumuser, $gorumroll;
      
    $whatHappened = "form_submitted";
    if( !$overrulePrivilege )
    {
        $base->hasObjectRights( $hasRight, Priv_create, TRUE );
        $typ = $base->getTypeInfo();
        if( isset($typ["attributes"]["ownerId"]) )
        {
            $base->ownerId = $gorumuser->id;  
        }    
    }
    
    create($base);

    if( $whatHappened!="invalid_form" )
    {
        if( isset($lll[$gorumroll->class."_created"]) )
        {
            $infoText = $lll[$gorumroll->class."_created"];
        }
        else
        {
            $infoText = sprintf($lll["created"], 
                                $lll[$gorumroll->class]);
        }                    
    }
}

function privilegeModify( $base, $whereFields="",$overrulePrivilege=FALSE )
{
    global $lll, $whatHappened, $infoText, $gorumroll;
      
    $whatHappened = "form_submitted";
    if( $overrulePrivilege)
    {
        modify( $base, $whereFields);
        if( $whatHappened!="invalid_form" )
        {
            if( isset($lll[$gorumroll->class."_modified"]) )
            {
                $infoText = $lll[$gorumroll->class."_modified"];
            }
            else
            {
                $infoText = sprintf($lll["modified"], 
                                    $lll[$gorumroll->class]);
            }                    
        }
        return ok;
    }
    $className = get_class($base);
    $oldBase = new $className;
    $oldBase->copy( $base, $whereFields );
    $ret = load( $oldBase, $whereFields );
    if( $ret )
    {
        handleError($lll["not_found_in_db"]);
    }
    $oldBase->hasObjectRights( $hasRight, Priv_modify, TRUE );
    
    // Ellenorizzuk, hogy nem-e a private attributumokat modositotta vki
    $typ = $base->getTypeInfo();
    $attrs = get_object_vars($base);
    $noErr = TRUE;
    foreach( $attrs as $attr=>$value )
    {
        $visibility = $base->getVisibility( $typ, $attr );
        if( $visibility==Form_invisible )
        {
            unset($base->{$attr});        
        } 
    }
    modify( $base, $whereFields);
    if( $whatHappened!="invalid_form" )
    {
        if( isset($lll[$gorumroll->class."_modified"]) )
        {
            $infoText = $lll[$gorumroll->class."_modified"];
        }
        else
        {
            $infoText = sprintf($lll["modified"], 
                                $lll[$gorumroll->class]);
        }                    
    }
}

function privilegeDelete( &$base, $whereFields="",$overrulePrivilege=FALSE )
{
    global $lll, $whatHappened, $infoText, $gorumroll;
      
    $whatHappened = "form_submitted";
    $ret = load( $base, $whereFields );
    if( $ret )
    {
        handleError($lll["not_found_in_db"]);
    }
    if( $overrulePrivilege )
    {
        delete( $base, $whereFields);
        return ok;
    }
    $base->hasObjectRights( $hasRight, Priv_delete, TRUE );
    delete( $base, $whereFields );
    if( isset($lll[$gorumroll->class."_deleted"]) )
    {
        $infoText = $lll[$gorumroll->class."_deleted"];
    }
    else
    {
        $infoText = sprintf($lll["deleted"], 
                            $lll[$gorumroll->class]);
    }                    
    return ok;
}
  
function privilegeMultipleDelete( &$base, $overrulePrivilege=FALSE )
{
    global $lll, $whatHappened, $infoText, $gorumroll;
    global $resultList;
      
    $whatHappened = "form_submitted";
    if( !$overrulePrivilege )
    {
        $base->hasObjectRights( $overrulePrivilege, Priv_multipledelete, FALSE );
    }
    $length = count($resultList);
    if( $overrulePrivilege )
    {
        $deletedCount = $length;
        foreach( $resultList as $base->id ) delete($base);
    }
    else
    {
        $deletedCount = 0;
        foreach( $resultList as $base->id )
        {
            $ret = load($base);
            if( $ret )
            {
                handleError($lll["not_found_in_db"]);
            }
            $base->hasObjectRights( $hasRight, Priv_delete, FALSE );
            if( $hasRight )
            {
                delete($base);
                $deletedCount++;
            }
        }
    }
    $infoText = sprintf($lll["multipleDeleted"],$deletedCount, $length);
    if( $deletedCount<$length ) $infoText.="<br>".$lll["cantDeleteTheRest"];
    return ok;
}
  
   
?>
