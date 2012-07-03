<?php
$group_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "text",
                "list",
                "details",
                "mandatory",
                "max" =>"255",
                "min" =>"1",
            ),            
            "comment"=>array(
                "type"=>"TEXT",
                "rows" => 10,
                "cols" => 50,
                "textarea",
                "details",
                "list"
            ),
            "moderator"=>array(
                "type"=>"INT",
                "mandatory",
                "list",
                "classselection",
                "class"=>$userClassName,
                "size"=>"10",
                "details",
                "where"=>"id!=name",
                "labelAttr"=>"name"
            ),            
            "ownerId"=>array(
                "type"=>"INT",
                "form invisible",
            )
        ),
        "primary_key"=>"id",
        "unique_keys"=>"name",           
        "delete_confirm"=>"name",
        "sort_criteria_attr"=>"name",
        "sort_criteria_dir"=>"a"
    );

class Group extends Object
{

function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $gorumrecognised, $gorumauthlevel, $gorumuser;
    global $generalRight;
    global $lll;
    $hasRight = FALSE;
    $generalRight = FALSE;
    if( $method==Priv_load ) {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }
    elseif( !$gorumrecognised )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }        
    else
    {
        if( $gorumuser->isAdm )
        {
            $generalRight = TRUE;
            $hasRight=TRUE;
        }
        elseif( $this->moderator==$gorumuser->id ) // O a moderatora
        {
            if( $method==Priv_delete )
            {
                $hasRight=FALSE;
                $generalRight=FALSE;
            }
            else
            {
                $hasRight=TRUE;
                $generalRight=FALSE;
            }
        }
    }
    if( !$hasRight && $giveError )
    {
        handleError($lll["permission_denied"]);
    }
    return ok;
} 

function delete($whereFields="",$overrulePrivilege=FALSE)
{
    global $lll,$whatHappened,$gorumroll,$infoText,$applName;
            
    $whatHappened = "form_submitted";
    Object::delete($whereFields,$overrulePrivilege);
    // kitoroljuk a tagokat is:
    $query="DELETE FROM $applName".
           "_groupmember WHERE groupId='$this->id'";
    executeQuery($query);

}

function showListVal($attr)
{
    $s="";
    if( $attr=="moderator" )
    {
        global $userClassName, $gorumroll;
        $user = new $userClassName;
        $user->id = $this->{$attr};
        load($user, "", "name");
        $tempRoll = $gorumroll;
        $tempRoll->list=$userClassName;
        $tempRoll->method="showdetails";
        $tempRoll->rollid=$user->id;
        saveInFromFrom($tempRoll);
        $s=$tempRoll->generAnchor($user->name);
    }
    else $s=nl2br(htmlspecialchars($this->{$attr}));
    return $s;
}

function showTools($rights)
{
    global $lll, $gorumroll, $groupClassName,$showIcon,$xi;
    
    $s = showTools($this,$rights);
    if (!isset($rights[Priv_modify])) {
        $this->hasObjectRights($hasRight,Priv_modify);
    }
    else $hasRight = $rights[Priv_modify];
    if ($hasRight) {    
        $tempRoll = $gorumroll;
        $tempRoll->list=$groupClassName;
        $tempRoll->method="edit_group_form";
        $tempRoll->id = $this->id;
        saveInFrom($tempRoll);
        $s.=" ";
        if( $showIcon )
        {
            $s.=$tempRoll->generImageAnchor("$xi/users.gif",
                                   $lll["group_editGroupTitle"],17,22);
        }
        else {
            $s.=$tempRoll->generAnchor($lll["group_editGroupTitle"]);
        }        
    }
    return $s;
} 
   
function editGroupForm(&$s)
{
    global $group_typ,$userClassName,$gorumroll,$lll,$applName;
    
    $typ = & $group_typ;
    $ret=load($this);
    if( $ret )
    {
        $txt = $lll["not_found_in_db"];
        handleError($txt);
    }
    $this->hasObjectRights($hasRight, Priv_modify, TRUE);
    
    $s="";
    $tempRoll = $gorumroll;
    $tempRoll->method = "edit_group";
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    $s.=generBoxUp();
    $s.="<tr><th class='header' colspan='2'>";
    $s.=$lll["group_editGroupTitle"];
    $s.="</th></tr>";
    $s.=generHiddenField("id",$this->id);
    
    $fromListNames = array();
    $fromListValues = array();
    $toListNames = array();
    $toListValues = array();
    $allNames = array();
    $allValues = array();
    if( $this->id ) // ha a group mar benn van a DB-ben
    {
        $query = "SELECT userId AS id, u.name AS name ".
                 "FROM $applName"."_groupmember AS g, $applName".
                 "_$userClassName AS u ".
                 "WHERE g.groupId='$this->id' AND g.userId=u.id ".
                 "ORDER BY name ASC";
        $usersInGroup = new $userClassName;         
        loadObjectsSql($usersInGroup, $query, $usersInGroup);
    }
    foreach( $usersInGroup as $user )
    {
        $toListNames[]=htmlspecialchars($user->name);
        $toListValues[]=$user->id;
    }
    $query = "SELECT id, name FROM $applName".
             "_$userClassName WHERE id!=name ORDER BY name ASC";
    $users = new $userClassName;       
    loadObjectsSql($users, $query, $users);
    foreach( $users as $user )
    {
        $allNames[]=htmlspecialchars($user->name);
        $allValues[]=$user->id;
    }
    $fromListNames = array_diff( $allNames, $toListNames );
    $fromListValues = array_diff( $allValues, $toListValues );
    $fromListTitle=$lll["fromListTitle"];
    $toListTitle=$lll["toListTitle"];
    $fromListResult = "fromListResult[]";
    $toListResult = "toListResult[]";
    $fromListSize=20;
    $toListSize=20;
    $addButtonText=$lll["addButtonText"];
    $removeButtonText=$lll["removeButtonText"];
    $addAllButtonText=$lll["addAllButtonText"];
    $removeAllButtonText=$lll["removeAllButtonText"];
    $s.="<tr><td class='cell' colspan='2' align='center'>";
    $s.=generGroupSelection($fromListTitle, $toListTitle, 
                            $fromListNames, $toListNames,
                            $fromListValues, $toListValues,
                            $fromListResult, $toListResult,
                            $fromListSize, $toListSize,
                            $addButtonText, $removeButtonText,
                            $addAllButtonText,$removeAllButtonText,
                            "cell", "item");
    $s.="</td></tr>";
    $s.="<tr><td align='center' colspan='2' class='cell'>";
    $s.="<input type='submit' value='".$lll["ok"].
        "' name='submit'>\n";
    $s.="</td></tr>";
    $s.=generBoxDown();
    $s.="</form>\n";
}

function editGroup()
{
    global $lll;
    global $gorumroll;
    global $fromListResult;
    global $toListResult;
    global $whatHappened, $infoText;
    global $applName, $userClassName;

    $s="";
    if( $gorumroll->submit==$lll["ok"] )
    {  
        $whatHappened = "form_submitted";
        $infoText = $lll["operation_cancelled"];
        return;
    } 
    $ret = load($this);
    if( $ret )
    {
        $txt = $lll["not_found_in_db"];
        handleError($txt);
    }
    $this->hasObjectRights($hasRight, Priv_modify, TRUE);
    if( $gorumroll->submit==$lll["addButtonText"] && 
        isset($fromListResult) && $fromListResult[0]!=0)
    {  
        $gm = new GroupMember;
        $gm->groupId = $this->id;
        foreach($fromListResult as $userId)
        {
            $gm->userId = $userId;
            create($gm);
        }
    } 
    elseif( $gorumroll->submit==$lll["removeButtonText"] && 
            isset($toListResult)&& $toListResult[0]!=0)
    {  
        $gm = new GroupMember;
        $gm->groupId = $this->id;
        foreach($toListResult as $userId)
        {
            $gm->userId = $userId;
            delete($gm);
        }
    } 
    elseif( $gorumroll->submit==$lll["addAllButtonText"] )
    {  
        $query = "SELECT id FROM $applName"."_$userClassName";
        $users = new $userClassName;
        loadObjectsSql($users, $query, $users);
        $userIds = $usersInGroupIds = array();
        foreach( $users as $user ) $userIds[]=$user->id;
        $query="SELECT userId AS id FROM $applName"."_groupmember ".
               "WHERE groupId='$this->id'";
        $usersInGroup = new $userClassName;
        loadObjectsSql($usersInGroup, $query, $usersInGroup);
        foreach($usersInGroup as $user)$usersInGroupIds[]=$user->id;
        $usersNotInGroupIds = array_diff($userIds,$usersInGroupIds);
        $gm = new GroupMember;
        $gm->groupId = $this->id;
        foreach( $usersNotInGroupIds as $id )
        {
            $gm->userId = $id;
            create($gm);
        }
    } 
    elseif( $gorumroll->submit==$lll["removeAllButtonText"] )
    {  
        $query = "DELETE FROM $applName".
                 "_groupmember WHERE groupId='$this->id'";
        executeQuery($query);
    }
    $whatHappened = "invalid_form"; 
}

function getListSelect()
{
    global $applName, $groupClassName;
    $groupTableName = $applName."_$groupClassName";
    // A group listaban nem szabad megmutatni az eredetileg beletett 
    // default elemeket - all, onlyadmin es onlyadminandmod. Ezek
    // pedig epp azok, ahol a moderator 0.
    $select = "SELECT * FROM $groupTableName WHERE moderator!='0'";
    return $select;
} 

}

?>
