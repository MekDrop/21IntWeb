<?php
$topic_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "pid"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form hidden",
                "private"
            ),
            "subject"=>array(
                "type"=>"VARCHAR",
                "max" =>"250",
                "min" =>"1",
                "list",
                "sorta",
                "text",
                "mandatory",
                "details"
            ),
            "forumname"=>array(
                "type"=>"VARCHAR",
                "no column",
                "form invisible",
                "sorta"
            ),           
            "status"=>array(
                "type"  =>"TEXT",
                "list",
                "centered",
                "no column"
            ),            
            "postnum"=>array(
                "type"=>"INT",
                "default" => "0",
                "form invisible",
                "list",
                "centered",                
                "sorta",
                "private",
                "details"
            ),
            "viewnum"=>array(
                "type"=>"INT",
                "default" => "0",
                "form invisible",
                "list",
                "centered",                
                "sortd",
                "private",
                "details"
            ),            
            "txt"=>array(
                "type"=>"TEXT",
                "no column",
                "textarea",
                "rows" => 10,
                "cols" => 50,
                "mandatory",
                "min"=>"1",
                "modify_form: form invisible"
            ),           
            "prio"=>array(
                "type"=>"INT",
                "text",
                "default" => "100",
                "form hidden"
            ),
            "creatorName"=>array(
                "type"=>"VARCHAR",
                "list",
                "sorta",
                "no column",
                "form invisible",
                "details"
            ),            
            "creationtime"=>array(
                "type"=>"INT",
                "form invisible",
                "private",
                "details"
            ),
            "lastpost"=>array(
                "type"=>"INT",
                "form invisible",
                "list",
                "sorta",
                "private",
                "details"
            ),            
            "subs_topic"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"0",
                "form invisible",
                "no column"
            ),            
            "poll"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"0",
                "modify_form: form invisible"
            ),            
            "MAX_FILE_SIZE"=>array(
                "type"=>"INT",
                "form invisible",
                "no column"
            ),
            "att_file_upload"=>array(
                "type"=>"VARCHAR",
                "file",
                "form invisible",
                "max" =>"250",
                "no column"
            ),            
            "coding"=>array(
                "type"  =>"INT",
                "values"=>array(MessCode_no,MessCode_html,MessCode_ubb),
                "selection",
                "modify_form: form invisible"
            ),            
            "smiley"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"1",
                "modify_form: form invisible"                
            ),
            "ownerId"=>array(
                "type"=>"INT",
                "form invisible",
                "private"
            )
        ),    
        "primary_key"=>"id",
        "modify_form: submit"=>array("ok", "cancel"),
        "submit"=>array("ok", "cancel", "preview"),
        "delete_confirm"=>"subject",
        "sort_criteria_attr"=>"nedudd",
        "sort_criteria_dir"=>"d",
        "sort_criteria_sql"=>"prio ASC, lastpost DESC"
    );


class Topic extends Object
{
function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $gorumrecognised, $gorumauthlevel, $gorumuser,$gorumroll;
    global $generalRight,$lll;
    //TODO: ez meg nem ok, lasd pl. forum
    $hasRight = FALSE;
    $generalRight = FALSE;
    $this->cacheFatherObject();
    global $fatherForum;
    global $readAccessArr;
    if ($fatherForum->readgroup!=Group_All ) readAccessForums();
    hasAdminRights($isAdm);
    if (isset($readAccessArr[$fatherForum->id]) ||
        $fatherForum->readgroup==Group_All || $isAdm ||
        !$fatherForum) //ez a spec listaknal van
    {
        $readRight=TRUE;
    }
    else $readRight=FALSE;
    global $writeAccessArr;
    if ($fatherForum->writegroup!=Group_All) writeAccessForums();
    if (isset($writeAccessArr[$fatherForum->id]) ||
        $fatherForum->writegroup==Group_All || $isAdm)
    {
        $writeRight=TRUE;
    }
    else $writeRight=FALSE;
    global $topicAccessArr;
    if ($fatherForum->topicgroup!=Group_All ) topicAccessForums();
    if ((isset($topicAccessArr[$fatherForum->id]) ||
        $fatherForum->topicgroup==Group_All || $isAdm) && $writeRight)
    {
        $writeRight=TRUE;
    }
    else $writeRight=FALSE;
        
    if( $method==Priv_load )
    {
        $hasRight=$readRight;
        $generalRight = TRUE;
    }
    elseif( $gorumauthlevel==Loginlib_NewUser )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }  
    elseif( $gorumauthlevel==Loginlib_GuestLevel && $fatherForum )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }        
    elseif( $gorumauthlevel>Loginlib_GuestLevel && 
            !$gorumrecognised )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }        
    else
    {
        if( $method==Priv_create )
        {
            $hasRight=$writeRight;
            $generalRight = ($gorumroll->list=="topic");
        }
        elseif( $isAdm )
        {
            $hasRight=TRUE;
            $generalRight = ($gorumroll->list=="topic");
        }
        elseif( $gorumuser->isMod )//TODO: ezt a reszt megcsin.
        {
            if( $fatherForum )
            {
                $moderators = split( ", *", $fatherForum->moderator );
                if( in_array($gorumuser->id, $moderators) )
                {
                    $hasRight=TRUE;
                    //ha spec list:
                    $generalRight=($gorumroll->list=="topic");
                }    
            }
        }
        if( !$hasRight && isset($this->ownerId) && 
            $this->ownerId==$gorumuser->id )
        {
            $generalRight = FALSE;
            if ( isset($this->postnum) && $this->postnum!="1")
            {
                $hasRight=FALSE;
            }
            else
            {
                $hasRight=$writeRight;
            }
        }
    }

    if( !$hasRight && $giveError ) {
        handleError($lll["permission_denied"]);
    }        
}    
function showNoNewmessHotTopicImage()
{
    global $lll,$xi;
    return "<img src='$xi/hot.gif' width='30'".
           " height='21' alt='".$lll["nonewmesshot"]."'".
           " title='".$lll["nonewmesshot"]."'>";
}
function showNoNewmessTopicImage()
{
    global $lll,$xi;
    return "<img src='$xi/no_new_mess.gif' width='24'".
           " height='17' alt='".$lll["nonewmess"]."'".
           " title='".$lll["nonewmess"]."'>";
}
function showNewmessTopicImage()
{
    global $lll,$xi;
    return "<img src='$xi/newmess.gif' width='24'".
           " height='22' alt='".$lll["newmess"]."'".
           " title='".$lll["newmess"]."'>";
}
function showNewmessHotTopicImage()
{
    global $lll,$xi;
    return "<img src='$xi/hotnewmess.gif' width='30'".
           " height='22' alt='".$lll["newmesshot"]."'".
           " title='".$lll["newmesshot"]."'>";
}    

function getListSelect()
{
    global $topic_typ,$forum_typ,$gorumroll,$gorumuser,$lll, $applName, $userClassName;
    if ($gorumroll->list=="topic") {
        $select = "SELECT ".getAllColumns($topic_typ, "t").
                  "u.name AS creatorName ".
                  "FROM $applName"."_topic AS t, $applName"."_$userClassName AS u ".
                  "WHERE t.ownerId=u.id AND pid=$this->pid";
    }
    elseif ($gorumroll->list=="topic_all") {
        $select = "SELECT ".getAllColumns($topic_typ, "t").
                  " f.name AS forumname, u.name AS creatorName".
                  " FROM".
                  " $applName"."_topic AS t, $applName"."_forum AS f,".
                  " $applName"."_$userClassName AS u".
                  " WHERE t.pid=f.id AND t.ownerId=u.id";
    }
    elseif ($gorumroll->list=="topic_newt") {
        $select = "SELECT ".getAllColumns($topic_typ, "t").
                  " f.name AS forumname, u.name AS creatorName".
                  " FROM".
                  " $applName"."_topic AS t, $applName"."_forum AS f,".
                  " $applName"."_$userClassName AS u".
                  " WHERE t.pid=f.id AND t.ownerId=u.id AND".
                  " t.creationtime>=$gorumuser->logoutTime";
    }
    elseif ($gorumroll->list=="topic_newm") {
        $select = "SELECT ".getAllColumns($topic_typ, "t").
                  " f.name AS forumname, u.name AS creatorName".
                  " FROM".
                  " $applName"."_topic AS t, $applName"."_forum AS f,".
                  " $applName"."_$userClassName AS u".
                  " WHERE t.pid=f.id AND t.ownerId=u.id AND".
                  " t.lastpost>=$gorumuser->logoutTime";
    }
    elseif ($gorumroll->list=="topic_user") {
        list($userId,$userName) = explode( ",", $gorumroll->rollid );
        $lll["topic_user_ttitle"]=sprintf($lll["topic_user_ttitle"],
            htmlspecialchars($userName));
        $select = "SELECT ".getAllColumns($topic_typ, "t").
                  " f.name AS forumname, u.name AS creatorName".
                  " FROM".
                  " $applName"."_topic AS t, $applName"."_forum AS f,".
                  " $applName"."_$userClassName AS u".
                  " WHERE t.pid=f.id AND t.ownerId=u.id AND".
                  " t.ownerId=$userId";
    }
    else {
        $select = "";
    }

    return $select;
} 
        
function getSelect()
{
    global $topic_typ, $applName, $userClassName;
    $select = "SELECT ".getAllColumns($topic_typ, "t").
              "u.name AS creatorName ".
              "FROM $applName"."_topic AS t, $applName"."_$userClassName AS u ".
              "WHERE t.ownerId=u.id AND t.id='$this->id'";
    return $select;
} 
        
function showNewToolPlusUrl(&$tempRoll)
{
    $tempRoll->pid = $this->pid;
}   
      
function cacheFatherObject()
{
    global $fatherForum;
    if( isset($this->pid) && $this->pid &&
        (!isset($fatherForum) || !$fatherForum ||
        $fatherForum->id!=$this->pid) ) 
    {
        $fatherForum=new Forum;
        $fatherForum->id=$this->pid;
        $ret=load($fatherForum);
        if( $ret==not_found_in_db ) {
            $fatherForum=0;
            $txt="Can not load father for topic, fatherForum set to 0";
        }
    }
    elseif( !isset($this->pid) || !$this->pid ) $fatherForum=0;
}    

function createForm(&$s,$valid=TRUE)
{
    global $lll,$gorumuser, $gorumroll;
    global $attFileSize,$attAllowExt,$attForbidExt;
    global $subsInfo,$subscription,$allowSubscriptions;
    

    $this->hasObjectRights($hasRight, Priv_create, TRUE);
    $s="";
    if ($attFileSize>0) {        
        $this->removeFromTypeInfo("MAX_FILE_SIZE", "no column");
        $this->removeFromTypeInfo("att_file_upload", "no column");
        $this->removeFromTypeInfo("att_file_upload", "form invisible");
        $this->MAX_FILE_SIZE=$attFileSize;
    }
    $this->cacheFatherObject();
    global $fatherForum;
    if (!( $subscription&subs_all_new_mess ||
           $subscription&subs_all_new_topic ||
           isset($subsInfo["forummess"][$this->pid]) ||
           isset($subsInfo["topic"][$this->pid]) 
           ))
    {
        if ( $allowSubscriptions ) {
          $this->removeFromTypeInfo("subs_topic", "no column");
          $this->removeFromTypeInfo("subs_topic", "form invisible");
        }
    }
    if (!$gorumroll->invalid) {
        $this->txt = "";
    }
    if($gorumuser->signature && !$gorumroll->invalid) {
        $this->txt.="\n\n$gorumuser->signature";
    }  
    if( $gorumroll->invalid ) $gorumroll->invalid=FALSE;    
    if ($attAllowExt!="")
    {
        $lll["att_file_upload_expl"]=sprintf(
            $lll["att_file_upload_expl_allow"],$attAllowExt);
    }
    elseif ($attForbidExt!="")
    {
        $lll["att_file_upload_expl"]=sprintf(
            $lll["att_file_upload_expl_forbid"],$attForbidExt);
    }
    $lll["att_file_upload_expl"].=" ".
        sprintf($lll["att_file_upload_expl_maxsiz"],
                $attFileSize);
    global $allowHtmlInPost, $allowUbbInPost, $allowSmileyInPost;
    $this->smiley = $allowSmileyInPost;
    if( !$allowHtmlInPost && !$allowUbbInPost ) {
        $this->coding=MessCode_no;
    }
    elseif( $allowHtmlInPost && !$allowUbbInPost ) {
        $this->coding=MessCode_no;
    }
    elseif( !$allowHtmlInPost && $allowUbbInPost ) {
        $this->coding=MessCode_ubb;
    }
    else $this->coding=MessCode_ubb;
    $this->generForm($s1);
    $s.=$s1;
}

function generForm(&$s)
{
    global $lll;
    global $gorumuser, $gorumroll;
    global $topic_typ;
    global $attFileSize,$attAllowExt,$attForbidExt;
    

    $s="";
    $typ = & $topic_typ;
    if ($gorumroll->submit==$lll["preview"]) {
        $s.=$this->showPreview();
    }
    $this->cacheFatherObject();
    global $fatherForum;
    $this->removeFromTypeInfo("txt", "no column");
    hasAdminRights($isAdm);
    if( $isAdm) {
        $this->removeFromTypeInfo("prio", "form hidden");
    }    
    global $allowHtmlInPost, $allowUbbInPost, $allowSmileyInPost;
    if( !$allowSmileyInPost ) {
        $typ["attributes"]["smiley"][] = "form invisible";
    }
    if( !$allowHtmlInPost && !$allowUbbInPost ) {
        $typ["attributes"]["coding"][] = "form invisible";        
    }
    elseif( $allowHtmlInPost && !$allowUbbInPost ) {
        unset($typ["attributes"]["coding"]["values"][2]);//UBB
        $lll["topic_coding"]=$lll["enableHtmlInThis"];
        $lll["coding_".MessCode_html]=$lll["yes"];
    }
    elseif( !$allowHtmlInPost && $allowUbbInPost ) {
        unset($typ["attributes"]["coding"]["values"][1]);
        $lll["topic_coding"]=$lll["enableUbbInThis"];
        $lll["coding_".MessCode_ubb]=$lll["yes"];
    }
    generForm($this, $s1);
    $s.=$s1;
}

function showPreview()
{
    global $lll;
    $s="";
    $s.=generBoxUp();
    $s.="<tr class='header'><th align='center'>";
    $s.=$lll["preview"];
    $s.="</th></tr>\n<tr class='cell'><td>";
    $tempTxt = $this->txt;
    applyUbbDecoding($this, $tempTxt);
    $s.="<span class='cell'>".$tempTxt."</span>";
    $s.=generBoxDown();
    return $s;
}
  
function moveForm(&$s)
{
    global $usedInPortal;
    global $topic_typ;
    global $lll, $infoText;
    
    $s="";
    $this->removeFromTypeInfo("pid", "private");
    $this->removeFromTypeInfo("pid", "form hidden");
    $topic_typ["attributes"]["pid"][] = "classselection";
    $topic_typ["attributes"]["pid"]["class"]="forum";
    $topic_typ["attributes"]["pid"]["labelAttr"]="name";

    $topic_typ["attributes"]["txt"][] = "form invisible";
    $topic_typ["attributes"]["subject"][]="form invisible";
    $topic_typ["attributes"]["prio"][]="form hidden";
    $topic_typ["attributes"]["poll"][]="form invisible";
    $topic_typ["attributes"]["subs_topic"][]="form invisible";
    $topic_typ["attributes"]["coding"][]="form invisible";
    $topic_typ["attributes"]["smiley"][]="form invisible";
    $topic_typ["submit"]=array("ok", "cancel");

    Object::modifyForm($s1);
    $s.=$s1;
}
/*
    //TODO
function moveForm(&$s)
{
    global $usedInPortal;
    global $topic_typ, $applName;
    global $lll, $infoText;
    
    $s="";
    $this->load();
    $this->hasObjectRights($hasRight, Priv_delete, TRUE);
    $this->cacheFatherObject();
    global $fatherForum;
    $s="";
    goForward( $tempRoll, "formService" );
    $s.="<FORM METHOD='POST' ".
        " ACTION='$tempRoll->scriptname'>\n";
    $s.=$tempRoll->generHiddenFields();
    $s.=generHiddenField("id",$this->id);

    $s.=generBoxUp();
    $s.="<tr><th class='headerbg' colspan='2'>";
    $s.="<span class='header'>";
    $s.=$lll["moveTopic"];
    $s.="</span></th></tr>";
    
    $forums = new Forum;
    $query="SELECT id, name FROM $applName"."_forum ".
           "WHERE id!=$fatherForum->id AND isCat='no' ORDER BY treeidx";
    $ret=loadObjectsSQL( $forums,$query,$forums);
    if ($ret==not_found_in_db) {
        $infoText = $lll["noforumToMove"];
        return ok;
    }
    global $writeAccessArr;
    writeAccessForums();
    hasAdminRights($isAdm);
    if( !$isAdm )   
    {
        $newList = array();
        foreach( $forums as $forum )
        {
            if (isset($writeAccessArr[$forum->id]))
            {
                $newList[] = $forum;
            }
        }
        $forums=$newList;
    }
    if (count($forums)==0) {
        $infoText = $lll["noforumToMove"];
        return ok;
    }
    foreach( $forums as $forum )
    {
        $vals[$forum->id] = $forum->id;
        $labels[$forum->id] = $forum->name;   
    }  
    $selected=0;
    $s.=generSelectField("targetForum",$lll["targetForum"],"", 
                         $labels, $vals, 0,
                         FALSE,0,"cell", "listItem");
    $s.="<tr><td align='center' colspan='2' class='cell'>";
    $s.="<input type='submit' value='".$lll["ok"].
        "' name='submit'>\n";
    $s.="<input type='submit' value='".$lll["cancel"].
        "' name='submit'>\n";
    $s.="</td></tr>\n";
    $s.=generBoxDown();
}
*/

function loadHtmlList(&$list)
{
    global $gorumuser;

    $ret=loadHtmlList($this,$list);
    if( $ret==not_found_in_db ) {
        return;
    }
    global $readAccessArr;
    readAccessForums();
    hasAdminRights($isAdm); 
    if( !$isAdm )   {
        $newList = array();
        for($i=0;isset($list[$i]);$i++) {
            if (isset($readAccessArr[$list[$i]->pid])) {
                $newList[] = $list[$i];
            }
        }
        $list=$newList;
    }        
}

function showHtmlList(&$s)
{
    global $gorumroll;

    // Ha nem spec listarol van szo, a rollidben levo id-t 
    // betoltjuk a pid-be, hogy mostantol a showHtmlList
    // vegeig ne a rollidbol kelljen minden alkalommal kiszedegetni:
    if( $gorumroll->list=="topic" )
    {
        $this->pid = $gorumroll->rollid;
    }    
    global $thereIsUnreadMess;
    $thereIsUnreadMess=FALSE;
    showHtmlList($this,$s);
    if (!$thereIsUnreadMess && $gorumroll->list=="topic") {
        $cookieName="readforum_".$this->pid;
        setcookie($cookieName,time(),time()+ 28800); //8 hours
        $s1 = showForumJump($this->pid);
    }
    else {
        $s1 = showForumJump(1);
    }   
    if( $s1 ) {
        $s.=vertSpacer()."$s1";
    }
}

function showNavBar($withLink=FALSE)
{
    global $lll,$gorumroll,$forumView;

    $s="";
    $tempRoll = $gorumroll;
    $tempRoll->list="forum";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $s.=$tempRoll->generAnchor($lll["forums"]);        
    $s.="&nbsp;>&nbsp;";
    if ($gorumroll->list=="topic" || $gorumroll->list=="message") 
    {
        $this->cacheFatherObject();
        global $fatherForum;
        if( $gorumroll->method=="showhtmllist" )
        {
            if( !$fatherForum->up || $forumView=="tree" ) 
            {
                $s.=htmlspecialchars($fatherForum->name);
            }
            else
            {
                global $rollid;
                $rollid = $fatherForum->id;
                $s=$fatherForum->showNavBar($withLink);
           }    
        }
        else
        {
            $tempRoll = $gorumroll;
            $tempRoll->list="topic";
            $tempRoll->method="showhtmllist";
            $tempRoll->rollid=$this->pid;
            $s.=$tempRoll->generAnchor($fatherForum->name);
        }    
    }
    else $s="";
    return $s;
}

function showListVal($attr)
{
    global $lll,$gorumuser,$gorumroll;
    global $htmlListForum, $userClassName;

    $s="";
    if ($attr=="lastpost") {
        $s = $this->{$attr}=="0" ? 
             $lll["never"] : showTimestamp($this->{$attr});
    }        
    elseif ($attr=="status") {
        $this->cacheFatherObject();
        global $fatherForum;
        global $globalHotTopicNum;
        $cookieName="readtopic_".$this->id;
        global $HTTP_COOKIE_VARS;
        global $thereIsUnreadMess;
        if ($this->lastpost<=$gorumuser->logoutTime||
           (isset($HTTP_COOKIE_VARS[$cookieName]) &&
            $this->lastpost<=$HTTP_COOKIE_VARS[$cookieName]))
        {
            if ($this->postnum>$globalHotTopicNum) {
                $s=$this->showNoNewmessHotTopicImage();
            }
            else $s=$this->showNoNewmessTopicImage();
        }
        else {
            $thereIsUnreadMess=TRUE;
            if ($this->postnum>$globalHotTopicNum) {
                 $s=$this->showNewmessHotTopicImage();
            }
            else $s=$this->showNewmessTopicImage();
        }
    }        
    elseif ($attr=="creationtime")
        
    {
        $s=showTimestamp($this->{$attr});
    }    
    elseif ($attr=="creatorName") 
    {
        $tempRoll = $gorumroll;
        global $userClassName;
        $tempRoll->list=$userClassName;
        $tempRoll->method="showdetails";
        $tempRoll->rollid=$this->ownerId;
        saveInFromFrom($tempRoll);
        $s=$tempRoll->generAnchor(htmlspecialchars($this->{$attr}));
        return $s;
    }    
    elseif( $attr=="subject" )
    {
        $this->subject=replaceBadWords($this->subject);
        $tempRoll = $gorumroll;
        $tempRoll->list="message";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid="$this->pid,$this->id";
        $s=htmlspecialchars($this->{$attr});
        $s=$tempRoll->generAnchor($s, "listItemTitle");
        if (isset($this->prio) && $this->prio<100) {
            $s.="<br><span class='forumAccess'>".
                $lll["highprio"]."</span>";
        }
        return $s;
    }
    elseif( $attr=="forumname" )
    {
        $tempRoll = $gorumroll;
        $tempRoll->list="topic";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid=$this->pid;
        $s=htmlspecialchars($this->{$attr});
        $s=$tempRoll->generAnchor($s, "listItemTitle");
        return $s;
    }
    elseif($attr=="poll") {
        $s=$lll[$this->{$attr}];
    }    
    else
    {
        $s=htmlspecialchars($this->{$attr});
    }
    return $s;
}

function showNewTool($rights)
{
    global $lll,$gorumroll,$gorumrecognised,$userClassName;
    $s="";
    if ($gorumroll->list=="topic") {
        if ($gorumrecognised) {
            $s.=showNewTool($this,$rights);
        }
        else {
            /*
            $tempRoll = $gorumroll;
            $tempRoll->list = $userClassName;
            $tempRoll->method = "create_form";
            saveInFrom($tempRoll);
            $s.=$tempRoll->generAnchor($lll["regtopost"]);
            */
            $s.=$lll["regtopost"];
        }
    }    
    return $s;
} 
 
function showBelowList()
{
    global $lll;
    $s="";
    /*
    $s.="<table border='0' cellspacing='0'><tr class='cell'>";
    $s.="<td valign='middle'>".$this->showNoNewmessTopicImage().
        "</td>\n";
    $s.="<td valign='middle'>:".  $lll["nonewmess"].
        " </td>\n";
    $s.="<td valign='middle'>".$this->showNoNewmessHotTopicImage().
        "</td>\n";
    $s.="<td valign='middle'>:".  $lll["nonewmesshot"].
        " </td>\n";
    $s.="<td valign='middle'>".$this->showNewmessTopicImage().
        "</td>\n";
    $s.="<td valign='middle'>:".$lll["newmess"].
        " </td>\n";
    $s.="<td valign='middle'>".$this->showNewmessHotTopicImage().
        "</td>\n";
    $s.="<td valign='middle'>:".$lll["newmesshot"].
        " </td>\n";
    $s.="</tr></table>\n";
    */
    return $s;
}

function showDetailsMethods()
{
    global $subscription,$subsInfo,$lll,$gorumuser,$gorumroll;
    global $gorumrecognised,$allowSubscriptions;
    $s="";
    if ($gorumrecognised && $allowSubscriptions) { //subscription
        $s.="<tr><td class='cell' colspan='2'>";
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->rollid = 0;
        $tempRoll->objid=$this->id;
        $tempRoll->userid = $gorumuser->id;
        $tempRoll->type=subs_topic_new_mess;
        if ($subscription&subs_topic_new_mess &&
            isset($subsInfo["topics"][$this->id]))
        {
            $tempRoll->method = "delete";   
            $txt=$lll["unsubs_topic_new_mess"];
        }
        else 
        {
            $tempRoll->method = "create";   
            $txt=$lll["subs_topic_new_mess"];
        }    
        saveInFrom($tempRoll);
        $s.=$tempRoll->generAnchor($txt,"listItem");
        $s.="</td></tr>\n";
    }    
    $this->hasObjectRights( $hasRight, Priv_delete );
    if( $hasRight )  // move topic
    {
        $s.="<tr><td class='cell' colspan='2'>";
        $tempRoll = $gorumroll;
        $tempRoll->list="topic";
        $tempRoll->method="movetopic_form";
        $tempRoll->rollid = 0;
        $tempRoll->id = $this->id;
        $s.=$tempRoll->generAnchor($lll["moveTopic"],"listItem");
        $s.="</td></tr>\n";
    }
    return $s;
}

function create($overrulePrivilege=FALSE)
{
    global $gorumuser, $gorumauthlevel;
    global $whatHappened, $infoText;
    global $gorumroll;
    global $lll;
    global $specBlockSize;
    global $usedInPortal;
    global $HTTP_POST_VARS;
    
    $whatHappened = "form_submitted";
    if( $gorumroll->submit==$lll["preview"] ) {   
        $whatHappened = "invalid_form";
        return;
    }        
    $this->cacheFatherObject();
    global $fatherForum;
    $fatherForum->topicnum++;
    privilegeCreate($this, $overrulePrivilege);
    if ($whatHappened=="invalid_form" ) {//TODO: kell ez itt?
        return;
    }
    // creating the first message under this topic:
    $message = new Message;
    $message->pid = $this->pid;
    $message->tid = $this->id;
    $message->subject = $this->subject;// ="";
    $message->coding = $this->coding;
    $message->smiley = $this->smiley;
    $message->subs_topic = FALSE;
    if( isset($this->txt) ) $message->txt = $this->txt;
    $message->create($overrulePrivilege);
    if ($whatHappened=="invalid_form" ) {//TODO: kell ez itt?
        return;
    }
    //updating topic num in fatherForum
    modify($fatherForum);
    //updating topic num in zorumglobalstat
    global $zorumglobalstat;
    $zorumglobalstat->topicnum++;
    modify($zorumglobalstat);
    if (isset($this->subs_topic) && $this->subs_topic) {
        $subs = new Subscribe();
        $subs->type=subs_topic_new_mess;
        $subs->userid=$gorumuser->id;
        $subs->objid=$this->id;
        create($subs);
    }
    sendMailToSubscribed($fatherForum,$this,$message,$gorumuser,
                              "topic");
    if( isset($this->poll) && $this->poll )
    {
        $whatHappened = "invalid_form";
        $gorumroll->method = "create";
        $gorumroll->list = "poll";
        $gorumroll->class = "poll";
        $HTTP_POST_VARS["pid"] = $this->pid;
        $HTTP_POST_VARS["tid"] = $this->id;
    }    
    $infoText = sprintf($lll["created"],$lll["topic"]);
    global $attErrTxt;
    if ($attErrTxt) $infoText.="<br>$attErrTxt";
} 
   
function modify( $whereFields="", $overrulePrivilege=FALSE )
{
    global $whatHappened, $infoText;
    global $lll, $gorumroll;
    global $specBlockSize;
    global $usedInPortal;
    
    $whatHappened = "form_submitted";
    if( $gorumroll->submit==$lll["preview"] ) {   
        $whatHappened = "invalid_form";
        return;
    }        
    $origTopic = new Topic;
    $origTopic->id=$this->id;
    $origTopic->load();
    $this->cacheFatherObject();
    global $fatherForum;
    privilegeModify( $this, $whereFields,$overrulePrivilege);
    if ($whatHappened=="invalid_form" ) {//TODO: kell ez?
        return;
    }
    $firstMessage = new Message;
    $firstMessage->tid = $this->id;    
    $firstMessage->pid = $this->pid;    
    $ret=load( $firstMessage, array("tid", "pid") );
    if( $ret==ok && $firstMessage->subject!=$this->subject )
    {
        $firstMessage->subject = $this->subject;
        modify( $firstMessage );
    }    
}

function delete( $whereFields="", $overrulePrivilege=FALSE )
{
    global $lll;
    global $whatHappened, $infoText;
    global $gorumroll, $applName;
    $whatHappened = "form_submitted";
    //TODO: ez alabbi?
    $ret = privilegeDelete( $this,$whereFields, $overrulePrivilege);
    if ($ret==not_found_in_db) {
        $txt="Object not found in Topic::delete";
        handleError($txt);
    }
    if ($whatHappened=="invalid_form" ) {
        return;
    }        
    // Ki kell a pollt is torolni, ha van:
    if( $this->poll ) {
        $poll = new Poll;
        $poll->tid = $this->id;
        load($poll, array("tid"));
        $poll->delete("",TRUE);
    }      
    global $fatherForum;
    $fatherForum=0; // Ez azert kell, hogy a cacheFatherObject
                    // megprobalja ujratolteni a fatherForumot,
                    // Igy az 0 marad, ha a fatherForum mar nem
                    // letezik
    $this->cacheFatherObject();
    //updating entry num in forum
    if ($fatherForum) {        
        $fatherForum->topicnum--;
        if ($fatherForum->topicnum==0) $fatherForum->lastpost=0;
        modify($fatherForum);
    }
    //updating entry num in globalstat
    global $zorumglobalstat;
    $zorumglobalstat->topicnum--;
    modify($zorumglobalstat);
    //delete all messages
    $query="DELETE FROM $applName"."_message WHERE tid='$this->id'";
    executeQuery($query);
}

function move()
{
    global $whatHappened, $infoText;
    global $lll, $applName;
    
    $whatHappened = "form_submitted";
    $this->load();
    $this->hasObjectRights( $hasRight, Priv_delete, TRUE );
    $this->cacheFatherObject();
    global $fatherForum;
    $oldFatherForum = $fatherForum;
    global $targetForum;
    if( !isset($targetForum) || !$targetForum ) {
        $infoText = $lll["operation_cancelled, noTargetForum"];
        return;
    }
    $this->pid = $targetForum;       
    $this->hasObjectRights( $hasRight, Priv_create, TRUE );
    $this->cacheFatherObject();
    // Ezutan a $fatherForum mar az uj fatherForum   
    
    // Elmentjuk a topicot:
    modify($this);    
    
    // Lekerdezzuk a message-ek szamat:
    $query="SELECT COUNT(*) FROM $applName"."_message WHERE tid=$this->id";
    $result=executeQuery($query);
    $row=mysql_fetch_row($result);
    $messageNum = $row[0];
    
    $oldFatherForum->topicnum--;
    $oldFatherForum->postnum = 
                    max($oldFatherForum->postnum - $messageNum, 0 );
    // Elmentjuk a regi forumot:
    modify($oldFatherForum);    
    $fatherForum->topicnum++;
    $fatherForum->postnum += $messageNum;
    // Elmentjuk az uj forumot:
    modify($fatherForum);    

    // Modositjuk a message-eket
    $query = "UPDATE $applName"."_message SET pid='$targetForum' ".
             "WHERE tid='$this->id'";   
    $result=executeQuery($query);
    
    // Modositjuk a pollt, ha van
    $query = "UPDATE $applName"."_poll SET pid='$targetForum' ".
             "WHERE tid=$this->id";   
    $result=executeQuery($query);
    
}
}//END Class

?>
