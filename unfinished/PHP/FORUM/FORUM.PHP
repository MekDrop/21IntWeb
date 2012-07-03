<?php
//goForward csere
//return kodok -> handleError csere
//langTxt -> lll
//zorum -> gorum (zorumroll, zorumuser, ...)
//tablanevek atirasa : $applName"."_
//typeInfo -> typ
//infoText[] => infoText
// ->class helyett ->list : grep "\->class" *.php 

//vegen:
//nem lehet errTxt
$forum_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "up"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form hidden",
            ),            
            "name"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"120",
                "min" =>"1",
                "length"=>50,
                "mandatory",
                "list",
                "organiser",
                "details"
            ),            
            "treeidx"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form invisible",
                "organiser",
                "centered",
            ),            
            "treedepth"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form invisible",
                //"organiser",
                "centered",
            ),            
            "status"=>array(
                "type"  =>"TEXT",
                "list",
                "centered",
                "no column"
            ),             
            "description"=>array(
                "type"=>"TEXT",
                "textarea",
                "rows"=>5,
                "cols"=>50,
                "details"
            ),
            "iscat"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"0"
            ),            
            "readgroup"=>array(
                "type"=>"INT",
                "classselection",
                "class"=>"group",                
                "labelAttr"=>"name",
                "default"=>"1"
            ),
            "writegroup"=>array(
                "type"=>"INT",
                "classselection",
                "class"=>"group",                
                "labelAttr"=>"name",
                "default"=>"1"
            ),
            "topicgroup"=>array(
                "type"=>"INT",
                "classselection",
                "class"=>"group",                
                "labelAttr"=>"name",
                "default"=>"1"
            ),
            "hasChild"=>array(
                "type"  =>"INT",
                "form invisible",
                "default"=>"0"
            ),
            "topicnum"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form invisible",
                "list",
                "centered",
                "details"
            ),
            "postnum"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form invisible",
                "list",
                "centered",
                "details",
            ),
            "creationtime"=>array(
                "type"=>"INT",
                "form invisible",
                "details",
            ),
            "lastpost"=>array(
                "type"=>"INT",
                "form invisible",
                "list",
                "details",
            ),
            "moderator"=>array(
                "type"=>"TEXT",
                "multipleselection",
                "size"=>"3",
                "list",
                "details",
            ),
            "ownerId"=>array(
                "type"=>"INT",
                "form invisible",
            )
        ),    
        "primary_key"=>"id",
        "delete_confirm"=>"name",
        "sort_criteria_attr"=>"treeidx",
        "sort_criteria_dir"=>"a"
    );


class Forum extends Object
{
    function Forum()
    {
        $this->up=0;
        $this->moderator="";
    }  
      
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $lll,$generalRight;
        hasAdminRights($isAdm);
        $generalRight = TRUE;
        if( $method==Priv_load ) {
            $hasRight=TRUE;
        }    
        elseif( $isAdm ) {
            $hasRight=TRUE;
        }
        else {
            $hasRight=FALSE;
        }
        if( !$hasRight && $giveError ) {
            handleError($lll["permission_denied"]);
        }        
    }    
    
    function create($overrulePrivilege=FALSE)
    {
        global $whatHappened, $zorumglobalstat, $userClassName;
        
        $whatHappened = "form_submitted";
        treeListPreCreate($this,"");
        privilegeCreate($this,$overrulePrivilege);
        if( $whatHappened=="invalid_form") {
            return;
        }
        if ($this->moderator) {
            foreach( $this->moderator as $uid ) {
                $user = new $userClassName;
                $user->id=$uid;
                $user->isMod=TRUE;
                modify($user);
            }
        }
        //updating forum num in globalstat
        $zorumglobalstat->forumnum++;
        modify($zorumglobalstat);
    }
         
    function modify($overrulePrivilege=FALSE)
    {
        global $whatHappened, $userClassName;
        
        $whatHappened = "form_submitted";
        privilegeModify($this,$overrulePrivilege);
        if( $whatHappened=="invalid_form") {
            return;
        }
        if ($this->moderator) {
            foreach( $this->moderator as $uid ) {
                $user = new $userClassName;
                $user->id=$uid;
                $user->isMod=TRUE;
                modify($user);
            }
        }
    }
    
    function delete($whereFields="",$overrulePrivilege=FALSE)
    {
        global $whatHappened, $zorumglobalstat, $topic_typ, $applName;

        $whatHappened = "form_submitted";
        privilegeDelete($this,$whereFields="",$overrulePrivilege);
        //delete topics
        $query="SELECT * FROM $applName"."_topic".
               " WHERE pid=$this->id";
        $topics = new Topic;
        loadObjectsSQL( $topics, $query,$topics);
        foreach($topics as $topic) {
            $topic->delete("",TRUE);
        }
        $zorumglobalstat->forumnum--;
        modify($zorumglobalstat);
    }   
     
    function showNewToolPlusUrl(&$roll)
    {
        global $gorumroll;
        
        //TODO: check : rollid-be jo ertek jon?
        if ($gorumroll->rollid) $roll->up = $gorumroll->rollid;
    }
     
    function showNoNewmessForumImage()
    {
        global $lll,$xi;
        return "<img src='$xi/no_new_mess.gif' width='24'".
               " height='17' alt='".$lll["nonewmess"]."'".
               " title='".$lll["nonewmess"]."'>";
    }
    
    function showNewmessForumImage()
    {
        global $lll,$xi;
        return "<img src='$xi/newmess.gif' width='24'".
               " height='22' alt='".$lll["newmess"]."'".
               " title='".$lll["newmess"]."'>";
    }
    
    function showOneRow($rights,&$s)
    {
        if (!$this->iscat) return showOneRow($this,$rights,$s);
        else return showOneRow($this,$rights,$s,"category");
    }
    
    function showListEntry($rights)
    {
        global $maxdepth, $gorumroll;
        
        if( !$this->iscat ) 
        {
            return showListEntry($this,$rights);
        }    
        $f = 6+$maxdepth;
        $s="<td class='category' colspan='$f' align='left'>";
        global $forumView;
        if( $forumView=="flat" && $this->hasChild ) 
        {
            $tempRoll = $gorumroll;
            $tempRoll->list="forum";
            $tempRoll->method="showhtmllist";
            $tempRoll->rollid=$this->id;
            $s.=$tempRoll->generAnchor(htmlspecialchars($this->name));
        }        
        else 
        {
            $s.=htmlspecialchars($this->name);
        }    
        if ($this->description!="") {
            $s.="<br><span class='forumDescription'>";
            $s.=htmlspecialchars($this->description);
            $s.="</span>";
        }
        $s.="</td>";
        return $s;
    }

    function generForm(&$s)
    {
        global $lll,$gorumuser,$forum_typ, $userClassName, $applName;
        
        //TODO: group tema
        //  felvenni spec groupokat
        //  a group listaban ezeket nem szabad latni
        $query = "SELECT id,name FROM $applName"."_$userClassName ".
                 "WHERE id!=name ORDER BY name";
        $objects = new $userClassName;
        loadObjectsSQL($objects, $query, $objects);
        foreach( $objects as $obj ) {
            $forum_typ["attributes"]["moderator"]["values"][]=$obj->id;
            $lll["forum_moderator_".$obj->id]=$obj->name;
        }
        $this->moderator=explode(",",$this->moderator);
        generForm($this,$s);
    } 
    
    function showNavBar($withLink=FALSE)
    {
        global $gorumroll, $lll, $forumView;

        $s="";
        if( $gorumroll->method=="showhtmllist" )
        {
            if( !$gorumroll->rollid || $forumView=="tree" ) {
                $s.="";
                /*
                $tempRoll = $gorumroll;
                $tempRoll->list="forum";
                $tempRoll->method="showhtmllist";
                $tempRoll->rollid=0;
                $s.=$tempRoll->generAnchor($lll["forums"]);
                */
            }
            else
            {
                $tempRoll = $gorumroll;
                $tempRoll->list="forum";
                $tempRoll->method="showhtmllist";
                $tempRoll->rollid=0;
                $s.=$tempRoll->generAnchor($lll["forums"]);
                $s.="&nbsp;>&nbsp;";
                $this->cacheFatherObjects();
                global $fatherForumList;
                if( $withLink )
                {
                    $this->pid = $this->id;
                    $tempRoll = $gorumroll;
                    $tempRoll->list="topic";
                    $tempRoll->method="showhtmllist";
                    $tempRoll->rollid=$this->pid;
                    $s1=$tempRoll->generAnchor($this->name);
                }
                else $s1=$this->name;
                foreach( $fatherForumList as $fatherForum )
                {
                    $tempRoll = $gorumroll;
                    $tempRoll->list="forum";
                    $tempRoll->method="showhtmllist";
                    $tempRoll->rollid=$this->id;//TODO: ez itt nemok ?
                    $s1 = $tempRoll->generAnchor($fatherForum->name)."&nbsp;>&nbsp;$s1";
                }
                $s.=$s1;
            }
        }
        elseif( $gorumroll->method=="treeorganiser_form"  )
        {
            $tempRoll = $gorumroll;
            $tempRoll->list="forum";
            $tempRoll->method="showhtmllist";
            $tempRoll->rollid=0;
            $s.=$tempRoll->generAnchor($lll["forums"]);
            $s.="&nbsp;>&nbsp;";
            $s.=$lll["organiseForum"];
        }
        else
        {
            $tempRoll = $gorumroll;
            $tempRoll->list="forum";
            $tempRoll->method="showhtmllist";
            $tempRoll->rollid=0;
            $s.=$tempRoll->generAnchor($lll["forums"]);
        }
        return $s;
         
    }
    function showListVal($attr, $index="", $filledFromSavedValues=TRUE)
    {
        global $lll,$gorumuser,$gorumroll;

        //TODO: regen a readgroup=0 (stb) jelentette, hogy mindenkinek
        //joga van, ez megvaltozott 1-re. Mindenhol ellenorizni.

        $s="";
        global $readAccessArr;
        readAccessForums();
        if ($attr=="creationtime") {
            $s=showTimestamp($this->{$attr});
        }
        else if ($attr=="lastpost") {
            $s = $this->{$attr}=="0" ? 
                 $lll["never"] : showTimestamp($this->{$attr});
        }
        elseif($attr=="iscat") {
            $s = $this->{$attr} ? $lll["yes"] : $lll["no"];
        }    
        else if ($attr=="moderator") {
            if (!$this->moderator) {
                return $s=$lll["notmoderated"];
            }
            global $modNameById;
            global $userClassName;
            loadMods();
            $mods=explode(",",$this->moderator);
            $tempRoll = $gorumroll;
            $tempRoll->list=$userClassName;
            $tempRoll->method="showdetails";
            saveInFromFrom($tempRoll);
            foreach( $mods as $mod )
            {
                if ($s!="") $s.=",";
                $tempRoll->rollid=$mod;
                $s.=$tempRoll->generAnchor($modNameById[$mod]);
            }
            return $s;
        }
        else if( $attr=="name" )
        {
            $s=htmlspecialchars($this->{$attr});
            hasAdminRights($isAdm);
            if (isset($readAccessArr[$this->id]) ||
                $this->readgroup==Group_All || $isAdm)
            {
                $tempRoll = $gorumroll;
                $tempRoll->list="topic";
                $tempRoll->method="showhtmllist";
                $tempRoll->rollid=$this->id;  // pid
                $s=$tempRoll->generAnchor($s);
            }
            global $forumView;
            if( $forumView=="flat" && $this->hasChild &&
                $gorumroll->method!="treeorganiser_form" )
            {
                $tempRoll = $gorumroll;
                $tempRoll->list="forum";
                $tempRoll->method="showhtmllist";
                $tempRoll->rollid=$this->id;
                $s.="&nbsp;&nbsp;";
                $s.=$tempRoll->generAnchor($lll["subForum"]);
            }
            if ($this->description!="") {
                $s.="<br><span class='forumDescription'>";
                $s.=htmlspecialchars($this->description);
                $s.="</span>";
            }
            $first=TRUE;
            if ($this->readgroup!=Group_All) {
                $s.="<br><span class='forumAccess'>";
                $s.=$lll["private_read"];
                $s.="</span>";
                $first=FALSE;
            }
            if ($this->writegroup!=Group_All) {
                if ($first) $s.="<br>";
                else $s.="&nbsp;-&nbsp;";
                $s.="<span class='forumAccess'>";
                $s.=$lll["private_write"];
                $s.="</span>";
                $first=FALSE;
            }
            if ($this->topicgroup!=Group_All) {
                if ($first) $s.="<br>";
                else $s.="&nbsp;-&nbsp;";
                $s.="<span class='forumAccess'>";
                $s.=$lll["private_topic"];
                $s.="</span>";
                $first=FALSE;
            }
            return $s;
        }
        else if( $attr=="treeidx" )
        {
            if( $filledFromSavedValues ) $value = $this->treeidx;
            else
            {
                $treeidx_name = "treeidx_$index";
                global $$treeidx_name;
                $value = $$treeidx_name;
            }
            return "<input type='text' name='treeidx_$index' value='$value' class='cell' maxlength='4' size='4'>";
        }
        else if( $attr=="treedepth" )
        {
            if( $filledFromSavedValues ) $value = $this->treedepth;
            else
            {
                $treedepth_name = "treedepth_$index";
                global $$treedepth_name;
                $value = $$treedepth_name;
            }
            if( $value=="" ) $value=0;
            return "<input type='text' name='treedepth_$index' value='$value' class='cell' maxlength='4' size='4'>";
        }
        elseif ($attr=="status") {
            $cookieName="readforum_".$this->id;
            global $HTTP_COOKIE_VARS;
            //global $$cookieName;
            if ($this->lastpost<=$gorumuser->logoutTime ||
               (isset($HTTP_COOKIE_VARS[$cookieName]) &&
                $this->lastpost<=$HTTP_COOKIE_VARS[$cookieName]))
            {
                $s=$this->showNoNewmessForumImage();
            }
            else $s=$this->showNewmessForumImage();
        }
        else $s=htmlspecialchars($this->{$attr});
        return $s;
         
    }
    
    function showBelowList()
    {
        global $lll;
        $s="";
        /*
        $s.="<table border='0' cellspacing='0'><tr class='legend'>";
        $s.="<td valign='middle'>".$this->showNoNewmessForumImage().
            "</td>\n";
        $s.="<td valign='middle'>:".  $lll["nonewmess"].
            " </td>\n";
        $s.="<td valign='middle'>".$this->showNewmessForumImage().
            "</td>\n";
        $s.="<td valign='middle'>:".$lll["newmess"].
            " </td>\n";
        $s.="</tr></table>\n";
        */
        return $s;
    }
    
    function showDetailsMethods()
    {
        global $subscription,$subsInfo,$lll,$allowSubscriptions;
        global $gorumrecognised, $gorumroll;

        $s="";
        if ($gorumrecognised && $allowSubscriptions) {//subscription
            $s.="<tr><td class='cell' colspan='2'>";
            $tempRoll = $gorumroll;
            $tempRoll->list = "subscribe";   
            $tempRoll->rollid = 0;
            $tempRoll->objid=$this->id;
            $tempRoll->type=subs_forum_new_mess;
            if ($subscription&subs_forum_new_mess &&
                isset($subsInfo["forummess"][$this->id]))
            {
                $tempRoll->method = "delete";   
                $txt=$lll["unsubs_forum_new_mess"];
            }
            else 
            {
                $tempRoll->method = "create";   
                $txt=$lll["subs_forum_new_mess"];
            }    
            saveInFrom($tempRoll);
            $s.=$tempRoll->generAnchor($txt);
            $s.="</td></tr>\n";
            $s.="<tr><td class='cell' colspan='2'>";
            $tempRoll->type=subs_forum_new_topic;
            if ($subscription&subs_forum_new_topic &&
                isset($subsInfo["topic"][$this->id]))
            {
                $tempRoll->method = "delete";   
                $txt=$lll["unsubs_forum_new_topic"];
            }
            else 
            {
                $tempRoll->method = "create";   
                $txt=$lll["subs_forum_new_topic"];
            }    
            $s.=$tempRoll->generAnchor($txt);
            $s.="</td></tr>\n";
        }
        return $s;     
    }
    
    function cacheFatherObjects()
    {
        // A rollid alapjan betolti this-be az aktualis forumot, vagyis
        // a subforumok apjat, ha meg nincs betoltve. A fatherForumList-
        // be betolti az aktualis forum felmenoit, ha meg nincsenek 
        // betoltve
        global $fatherForumList;
        global $gorumroll;
        if( (!isset($this->id) || !$this->id) && $gorumroll->rollid ) 
        {
            $this->id=$gorumroll->rollid;
            $ret = load( $this );
            if( $ret ) {
                $txt="can not load forum object";
                handleError($txt);
            } 
        }    
        if( !isset($fatherForumList) || !$fatherForumList)
        {
            $fatherForumList = array();
            if( isset($this->id) && $this->id )
            {
                $childForum = $this;
                while( $childForum->up )
                {
                    $childForum->id = $childForum->up;
                    $childForum->up = 0;
                    $ret = load( $childForum );
                    if( $ret ) {
                        $txt="can not load child object";
                        handleError($txt);
                    } 
                    $fatherForumList[] = $childForum;
                }
                $fatherForumList = array_reverse($fatherForumList);
            }    
        }
    }
    
    function loadHtmlList(&$list)
    {
        global $maxdepth,$forumView;

        loadHtmlList($this,$list);
        $maxdepth=0;
        if ($forumView=="tree") {
            // Maximumkereses:
            foreach($list as $obj) {
                if( $obj->treedepth > $maxdepth ) {
                    $maxdepth=$obj->treedepth;
                }
            }
        }
    }    

    function getListSelect()
    {
        global $gorumroll,$forumView, $applName;

        $select = "SELECT * FROM $applName"."_forum";
        if ($forumView=="flat") {
            $select.=" WHERE up=".(int)$gorumroll->rollid;
        }
        return $select;
    }
    function treeOrganiserForm(&$s, $filledFromSavedValues=TRUE)
    {
        return treeOrganiserForm($this, $s, $filledFromSavedValues);
    }
    function treeOrganiserServ(&$s)
    {
        return treeOrganiserServ($this, $s);
    } 
}

?>
