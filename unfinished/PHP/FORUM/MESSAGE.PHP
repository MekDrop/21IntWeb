<?php
//TODO: az utolso uzenet legyen modosithato es torolheto!
$message_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "pid"=>array(
                "type"=>"INT",
                "form hidden",
            ),
            "tid"=>array(
                "type"=>"INT",
                "form hidden",
            ),
            "subject"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"250",
                "details"
            ),            
            "forumname"=>array(
                "type"=>"VARCHAR",
                "no column",
                "form invisible",
                "sorta"
            ),           
            "topicsubject"=>array(
                "type"=>"VARCHAR",
                "no column",
                "form invisible",
                "sorta"
            ),           
            "txt"=>array(
                "list",
                "type"=>"TEXT",
                "textarea",
                "rows"=>10,
                "cols"=>50,
                "mandatory",
                "min"=>"1",
                "sorta",
                "100%",
                "details"
            ),
            "iplog"=>array(
                "type"=>"VARCHAR",
                "max" =>"255",
                "form invisible",
            ),            
            "creatorName"=>array(
                "type"=>"VARCHAR",
                "list",
                "sorta",
                "no column"
            ),            
            "lastmess"=>array(
                "type"=>"INT",
                "form invisible",
                "create_form: form invisible",
            ),
            "creationtime"=>array(
                "type"=>"INT",
                "form invisible",
            ),            
            "subs_topic"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"0",
                "no column"
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
            ),
            "attsize"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form invisible",
            ),            
            "downloaded"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form invisible",
            ),
            "coding"=>array(
                "type"  =>"INT",
                "values"=>array(MessCode_no,MessCode_html,MessCode_ubb),
                "selection"
            ),            
            "smiley"=>array(
                "type"  =>"INT",
                "bool",
            ),
            "ownerId"=>array(
                "type"=>"INT",
                "form invisible",
                "create_form: form invisible",
            )            
        ),    
        "primary_key"=>"id",
        "delete_confirm"=>"subject",
        "sort_criteria_attr"=>"creationtime",
        "sort_criteria_dir"=>"a",
        "submit"=>array("ok", "cancel", "preview"),
        "notools"
    );


class Message extends Object
{
function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $gorumrecognised, $gorumauthlevel, $gorumuser,$gorumroll;
    global $generalRight, $lll;
    global $fatherForum, $fatherTopic;
    global $readAccessArr,$writeAccessArr;

    $generalRight = FALSE;
    $hasRight = FALSE;
    $this->cacheFatherObjects("forum, topic");
    if ($fatherForum->readgroup!=Group_All ) readAccessForums();
    hasAdminRights($isAdm);
    if (isset($readAccessArr[$fatherForum->id]) ||
        $fatherForum->readgroup==Group_All || $isAdm ||
        !$fatherForum) //ez a spec listaknal van    
    {
        $readRight=TRUE;
    }
    else $readRight=FALSE;
    if ($fatherForum->writegroup!=Group_All) writeAccessForums();
    if (isset($writeAccessArr[$fatherForum->id]) ||
        $fatherForum->writegroup==Group_All || $isAdm)
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
    elseif( !$gorumrecognised )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }        
    else
    {
        if( $method==Priv_create )
        {
            $hasRight=$writeRight;
            $generalRight = TRUE;
        }
        elseif( $isAdm )
        {
            $hasRight=TRUE;
            $generalRight = TRUE;
        }
        elseif( $gorumuser->isMod )
        {
            if( $fatherForum )
            {
                global $isModByPid;

                if (!isset($isModByPid)) hasModRights();
                if( isset($isModByPid[$fatherForum->id]) )
                {
                    $hasRight=TRUE;
                    //ha spec list:
                    $generalRight=($gorumroll->list=="message");
                }    
            }
        }
        if( !$hasRight && isset($this->ownerId) && 
            $this->ownerId==$gorumuser->id )
        {
            $generalRight = FALSE;
            if( !$this->lastmess )
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

function cacheFatherObjects( $whichObjects="forum, topic" )
{
    $whichObjects=split( ", *", $whichObjects );
    if( in_array( "forum", $whichObjects ) )
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
                $txt="Can not load father forum for message";
                handleError($txt);
            }
        }
        else if( !isset($this->pid) || !$this->pid ) $fatherForum=0;
    }    
    if( in_array( "topic", $whichObjects ) )
    {
        global $fatherTopic;
        if( isset($this->tid) && $this->tid &&
            (!isset($fatherTopic) || !$fatherTopic || 
             $fatherTopic->id!=$this->tid) ) 
        {
            $fatherTopic=new Topic;
            $fatherTopic->id=$this->tid;
            $ret=load($fatherTopic);
            if( $ret==not_found_in_db ) {
                $txt="Can not load father topic for message";
                handleError($txt);
            }
        }
        else if( !isset($this->tid) || !$this->tid ) $fatherTopic=0;
    }    
    if( in_array( "message", $whichObjects ) )
    {
        global $fatherMessage, $applName;
        if( (!isset($fatherMessage) || !$fatherMessage || 
            $fatherMessage->tid!=$this->tid )
            && $this->tid!=0 ) 
        {
            $fatherMessage=new Message;
            $query="SELECT * FROM $applName"."_message".
                   " WHERE tid=$this->tid ORDER BY id DESC LIMIT 1";
            $ret=loadObjectsSQL( $fatherMessage,$query,$mess);
            if( $ret==not_found_in_db ) $fatherMessage=0;
            else $fatherMessage=$mess[0];
        }
        else if( $this->tid==0 ) $fatherMessage=0;
    }
}
function create($overrulePrivilege=FALSE)
{
    global $gorumuser, $gorumauthlevel, $gorumroll, $lll;
    global $HTTP_SERVER_VARS;
    global $whatHappened, $infoText;
    global $attFileSize;
    
    $whatHappened = "form_submitted";
    if( $gorumroll->submit==$lll["preview"] ) {   
        $whatHappened = "invalid_form";
        return;
    }
    $this->iplog=$HTTP_SERVER_VARS["REMOTE_ADDR"];
    if ($attFileSize>0) {
        global $HTTP_POST_FILES;
        if (isset($HTTP_POST_FILES["att_file_upload"]["name"]) && 
            !checkAttachExtension(
                $HTTP_POST_FILES["att_file_upload"]["name"],$ext))
        {
            $whatHappened = "invalid_form";
            $infoText = sprintf($lll["wrong_att_ext"],$ext);
            return;
        }
        $this->att_file_upload="";//will be modified in storeAttach
    }
    $this->cacheFatherObjects("forum, topic, message");
    global $fatherForum, $fatherTopic, $fatherMessage;
    $this->lastmess=TRUE;
    privilegeCreate($this, $overrulePrivilege);
    if ($whatHappened=="invalid_form" ) {
        return;
    }
    if( $fatherMessage ) 
    {
        $fatherMessage->lastmess=FALSE;
        modify($fatherMessage);
    }
    //updating postnum num in fatherForum
    $fatherForum->postnum++;
    $fatherForum->lastpost=time();
    modify($fatherForum);
    //updating postnum num in fatherTopic
    $fatherTopic->lastpost=time();
    $fatherTopic->postnum++;
    modify($fatherTopic);
    //updating postnum num in zorumglobalstat
    global $zorumglobalstat;
    $zorumglobalstat->entrynum++;
    modify($zorumglobalstat);
    //updating postnum num in gorumuser
    $gorumuser->postnum++;
    modify($gorumuser);
    if ($this->subs_topic) {
        $subs = new Subscribe();
        $subs->type=subs_topic_new_mess;
        $subs->userid=$gorumuser->id;
        $subs->objid=$fatherTopic->id;
        $subs->create(TRUE);
    }        
    sendMailToSubscribed($fatherForum,$fatherTopic,$this,
                              $gorumuser);
    //nem jo, ha a vegen van, az attach belebuheral
    $infoText = sprintf($lll["created"],
                          $lll["message"]);
    //handle attachement
    if ($attFileSize>0) storeAttachment($this->id);
    
    global $attErrTxt;
    if ($attErrTxt) $infoText.="<br>$attErrTxt";
    
} 

   
function modify($whereFields="", $overrulePrivilege=FALSE )
{
    global $lll, $gorumroll;
    global $whatHappened, $infoText;
    global $attFileSize;
    global $HTTP_POST_FILES;
    
    $whatHappened = "form_submitted";
    if( $gorumroll->submit==$lll["preview"] ) {   
        $whatHappened = "invalid_form";
        return;
    }
    if ($attFileSize>0) {
        if (isset($HTTP_POST_FILES["att_file_upload"]["name"]) && 
            !checkAttachExtension(
                $HTTP_POST_FILES["att_file_upload"]["name"],$ext))
        {
            $whatHappened = "invalid_form";
            $infoText = sprintf($lll["wrong_att_ext"],$ext);
            return;
        }
    }
    unset($this->downloaded);//will be modified in storeAttach
    unset($this->attsize);
    unset($this->att_file_upload);
    privilegeModify( $this, $whereFields,$overrulePrivilege);
    if ($whatHappened=="invalid_form" ) {
        return;
    }
    $this->cacheFatherObjects("forum","topic");
    global $fatherTopic, $fatherForum;
    //handle attachement
    if ($attFileSize>0) {
        $ret=modAttachment($this->id);
        if ($ret!=ok) {
            $this->att_file_upload="";
            modify($this);//TODO: kell ez itt?
            return $ret;
        }        
        global $attErrTxt;
        if ($attErrTxt) $infoText.="<br>$attErrTxt";
    }
}
   
function delete($whereFields="", $overrulePrivilege=FALSE)
{
    global $whatHappened, $gorumuser;
    
    $whatHappened = "form_submitted";
    $ret = privilegeDelete( $this,$whereFields, $overrulePrivilege);
    if ($ret==not_found_in_db) {
        $txt="Object not found in Message::delete";
        handleError($txt);
    }        
    //delete attachment
    $a = new Attach;
    $a->id=$this->id;
    delete($a);
    global $fatherForum, $fatherTopic, $fatherMessage;
    // Ez azert kell, hogy a cacheFatherObject megprobalja 
    // ujratolteni a father objektumokat, igy azok 0 maradnak, 
    // ha a father objektum mar nem letezik:
    $fatherForum = $fatherTopic = $fatherMessage = 0;
    $this->cacheFatherObjects("forum, topic, message");
    //mark hasanswer in fatherMessage
    if( $fatherMessage ) 
    {
        $fatherMessage->lastmess=TRUE;
        modify($fatherMessage);
    }
    //updating entry num in fatherForum
    if( $fatherForum )  // Ha meg nincs kitorolve
    {         
        $fatherForum->postnum--;
        modify($fatherForum);
    }
    //updating entry num in fatherTopic
    if( $fatherTopic )  // Ha meg nincs kitorolve
    {
        $fatherTopic->postnum--;
        modify($fatherTopic);
    }
    //updating entry num in globalstat
    $stat = new GlobalStat;
    $stat->id=1;
    load($stat);
    //updating entry num in zorumglobalstat
    global $zorumglobalstat;
    $zorumglobalstat->entrynum--;
    modify($zorumglobalstat);
}
    
function loadHtmlList(&$list)
{
    global $gorumroll,$gorumuser;
    loadHtmlList($this,$list);
    global $readAccessArr;
    readAccessForums();
    hasAdminRights($isAdm); 
    if( !$isAdm )   
    {
        $newList = array();
        for($i=0;isset($list[$i]);$i++)
        {
            if (isset($readAccessArr[$list[$i]->pid]))
            {
                $newList[] = $list[$i];
            }
        }
        $list=$newList;
    }    
    $this->cacheFatherObjects("topic");
    global $fatherTopic;
    if( $fatherTopic )  // Ha nem spec list
    {
        if( $fatherTopic->ownerId!=$gorumuser->id ) // ha nem a tulaj
        {
            $fatherTopic->viewnum++;
            modify($fatherTopic,"",TRUE);
        }
        //mark it read
        $cookieName="readtopic_".$this->tid;
        setcookie($cookieName,time(),time()+ 28800);
    }
}
function showHtmlList(&$s)
{
    global $gorumroll,$message_typ,$HTTP_GET_VARS;

    $s="";
    // Az igazi showHtmlList elott elvegzunk nehany dolgot:
    //if ($gorumroll->messages) 
    //{
        $message_typ["attributes"]["txt"][]="list";
        $this->removeFromTypeInfo("creationtime","list");
        $this->removeFromTypeInfo("subject","list");
        $this->removeFromTypeInfo("creatorName","list");
    //}
    // Ha nem spec listarol van szo, a rollidben levo id-ket 
    // betoltjuk a pid-be es tid-be, hogy mostantol a showHtmlList
    // vegeig ne a rollidbol kelljen minden alkalommal kiszedegetni:
    //TODO: 
    if( $gorumroll->list=="message" ) {
        list( $this->pid, $this->tid )=explode(",",
                                               $gorumroll->rollid);
    }    
    $this->cacheFatherObjects("topic");
    global $fatherForum, $fatherTopic;
    if ($fatherTopic->poll)
    {
        $po = new Poll;
        $po->tid=$fatherTopic->id;
        $ret=load($po,array("tid"));
        if ($ret==ok) {
            global $subsInfo,$gorumrecognised, $gorumauthlevel;
            if (isset($subsInfo["poll_vote"][$po->id]) ||
                !($gorumrecognised || 
                  $gorumauthlevel==Loginlib_GuestLevel) ||
                  isset($HTTP_GET_VARS["viewpoll"])
               )
            {
                $s.=$po->showOnlyPoll();
            }
            else $s.=$po->voteOnlyForm();
            $s.=vertSpacer();
        }
    }
    showHtmlList($this,$s1);
    $s.=$s1;
    $this->showJumperTool($s1);
    if( $s1 ) $s.=vertSpacer()."$s1";
}

function showNavBar()
{
    global $gorumroll; 
    global $lll;
    global $messageView; 
    global $forumView; 

    $s="";
    $tempRoll = $gorumroll;
    $tempRoll->list="forum";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $s.=$tempRoll->generAnchor($lll["forums"]);
    $s.="&nbsp;>&nbsp;";
    if ($gorumroll->list=="message") 
    {
        $this->cacheFatherObjects("forum, topic");
        global $fatherForum, $fatherTopic;
        $tempRoll = $gorumroll;
        $tempRoll->list="topic";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid=$this->pid;
        $s.=$tempRoll->generAnchor($fatherForum->name);
        $s.="&nbsp;>&nbsp;";
        if( $gorumroll->method=="showhtmllist" )
        {
            if( !$fatherForum->up || $forumView=="tree" ) 
            {
                $s.=htmlspecialchars(replaceBadWords(
                    $fatherTopic->subject));
            }
            else
            {
                global $rollid;
                $rollid = $fatherForum->id;
                $fatherTopic->showNavBar($s, TRUE);
                $s.="&nbsp;>&nbsp;";
                $s.=htmlspecialchars(replaceBadWords(
                    $fatherTopic->subject));
           }    
        }
        else
        {
            $tempRoll = $gorumroll;
            $tempRoll->list="message";
            $tempRoll->method="showhtmllist";
            $tempRoll->rollid="$this->pid,$this->tid";
            $s.=$tempRoll->generAnchor(replaceBadWords(
                $fatherTopic->subject));
        }    
    }
    else $s="";
    return $s;
} 
function showReplyTool(&$s,$noquote=TRUE)
{
    global $lll,$gorumroll,$gorumuser,$xi;
    
    $s="";
    $tempRoll = $gorumroll;
    $tempRoll->list = "message";
    $tempRoll->method = "create_form";   
    $tempRoll->rollid = 0;        
    $tempRoll->pid = $this->pid;
    $tempRoll->tid = $this->tid;
    saveInFrom($tempRoll);
    if ($noquote) {
        $s.=$tempRoll->generImageAnchor(
            "$xi/reply.gif",$lll["icon_reply_noq"],17,22);
        $s.="&nbsp;";
        $s.=$tempRoll->generAnchor($lll["icon_reply_noq"],
                                   "listItem");
    }
    else {
        $tempRoll->rollid=$this->id;
        $s.=$tempRoll->generImageAnchor(
            "$xi/replywithquote.gif",$lll["icon_reply"],17,22);
        $s.="&nbsp;";
        $s.=$tempRoll->generAnchor($lll["icon_reply"],
                                   "listItem");
    }
}

function showListVal($attr, $rights=0)
{
    global $gorumroll, $lll, $gorumuser, $search,$showIcon,$xi;

    $s="";
    if ($attr=="creationtime") $s=showTimestamp($this->{$attr});
    else if ($attr=="txt" ){
        $this->txt=replaceBadWords($this->txt);
        $tempTxt=$this->txt;
        $dist="&nbsp;&nbsp;&nbsp;&nbsp;";
        $s1=$this->showModTool( $rights );
        if ($s1) {
            $saveI=$showIcon;
            $showIcon=FALSE;
            $s2=$this->showModTool( $rights );
            $s.="$s1 $s2";
            $showIcon=$saveI;
            $s.=$dist;
        }
        $s1=$this->showDelTool( $rights );
        if ($s1) {
            $saveI=$showIcon;
            $showIcon=FALSE;
            $s2=$this->showDelTool( $rights );
            $s.="$s1 $s2";
            $s.=$dist;
            $showIcon=$saveI;
        }
        if( !isset($rights[Priv_create]) )
        {
            $this->hasObjectRights($hasRight,Priv_create);
        }
        else $hasRight = $rights[Priv_create];
        if( $hasRight )
        {
            $this->showReplyTool($s1);//TODO error handling
            $s.=$s1;
            $s.=$dist;
            $this->showReplyTool($s1,FALSE);
            $s.=$s1;
        }
        else
        {    
        }
        if( $s!="" ) $s.="<br><br>\n";
        // A kovetkezo showListValoknak nem szabad 
        // span classokat beszurni:
        $hS="";
        $hS.=$lll["from"].": ";
        $hS.="&nbsp;";
        $hS.=$this->showListVal("creatorName", $rights);
        $hS.="&nbsp;";
        $hS.=$lll["ms_at"].": ";
        $hS.=$this->showListVal("creationtime", $rights);
        hasAdminRights($isAdm);
        if ($isAdm) {
            $hS.=" - $lll[ip_logged]:$this->iplog";
        }
        $hS.="<br>";
        $hS.=$lll["ms_subj"].": ";
        global $HTTP_GET_VARS;
        $word="";
        if( isset($HTTP_GET_VARS["words"]) && $HTTP_GET_VARS["words"] ) {
            $word = split(" ",$HTTP_GET_VARS["words"]);
        }    
        $this->subject=replaceBadWords($this->subject);
        if( $word && $this->subject ) {
            $tempRoll = $gorumroll;
            $tempRoll->list="message";
            $tempRoll->method="showdetails";
            $tempRoll->rollid = $this->id;                
            $tempSubject=htmlspecialchars($this->subject);
            for($i=0;isset($word[$i]);$i++) {
                if ($word[$i]=="") continue;
                $tempSubject=eregi_replace($word[$i],
                    "<span style='background-color:#ffffcc'>".
                    $word[$i]."</span>",$tempSubject);
            }
            $hS.=$tempSubject;
        }        
        elseif( $this->subject ) $hS.=htmlspecialchars($this->subject);
        else $hS.=$lll["noSubject"];
        $hS.="<br><br>\n"; 
        $cookieName="readtopic_".$this->tid;
        global $HTTP_COOKIE_VARS;
        if ($this->creationtime<=$gorumuser->logoutTime ||
            (isset($HTTP_COOKIE_VARS[$cookieName])&&
             $this->creationtime<=$HTTP_COOKIE_VARS[$cookieName] ))
        {
            //TODO: stylesheet update
            $hS=insertSpanClass($hS, "messageHeader");
        }
        else 
        {
            $hS=insertSpanClass($hS, "newMessageHeader");
        }
        $s.=$hS;
        applyUbbDecoding($this, $tempTxt);
        if ($this->att_file_upload!="") {
            $tempTxt.="<br><br>$lll[attachment]: ";
            $tempRoll = new Roll;
            $tempRoll->method = "showattach";   
            $tempRoll->id = $this->id;   
            $tempTxt.=$tempRoll->generImageAnchor("$xi/attach.gif",
                    $lll["icon_attach"],17,20);
            $tempTxt.=" ";
            $tempTxt.=$tempRoll->generAnchor($this->att_file_upload,
                                             "listItem");
            $tempTxt.=" ".$this->attsize." ".$lll["att_siz"];
            $tempTxt.=", ".sprintf($lll["downloaded_times"],
                              $this->downloaded);
            //$this->att_file_upload";
        }
        if( $word ) {
            for($i=0;isset($word[$i]);$i++) {
                if ($word[$i]=="") continue;
                $tempTxt=eregi_replace($word[$i],
                    "<span style='background-color:#ffffcc'>".
                    $word[$i]."</span>",$tempTxt);
            }
        }
        $s.="<span class='messageText'>".$tempTxt."</span>";
        return $s;
    }
    else if( $attr=="subject" )
    {
        $cookieName="readtopic_".$this->tid;
        global $HTTP_COOKIE_VARS;
        if ($this->creationtime<=$gorumuser->logoutTime ||
            (isset($HTTP_COOKIE_VARS[$cookieName])&&
             $this->creationtime<=$HTTP_COOKIE_VARS[$cookieName]))
        {
            $cl="messageHeader";
        }
        else $cl="newMessageHeader";
        if( isset($search->words) && $search->words!="" ) {
            $tempRoll = $gorumroll;
            $tempRoll->list="message";
            $tempRoll->method="showdetails";
            $tempRoll->rollid = $this->id;                
            $tempRoll->words=$search->words;            
            $word = split(" ",$search->words);
            if( $this->subject )
            {
                $tempSubject=htmlspecialchars(replaceBadWords(
                    $this->subject));
                for($i=0;isset($word[$i]);$i++) {
                    if ($word[$i]=="") continue;
                    $tempSubject=eregi_replace($word[$i],
                        "<span style='background-color:#ffffcc'>".
                        $word[$i]."</span>",$tempSubject);
                }
                $s=$tempSubject;
            }
            else $s=$lll["noSubject"];
            $s=$tempRoll->generAnchor($s, "listItemTitle");
        }        
        else
        {
            if( $this->subject ) {
                $s=htmlspecialchars(replaceBadWords($this->subject));
            }
            else $s = $lll["noSubject"];
            $s="<span class='listItem'>".$s."</span>";
        }
        return $s;
    }
    else if( $attr=="forumname" )
    {
        $tempRoll = $gorumroll;
        $tempRoll->list="topic";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid=$this->pid;
        $s=htmlspecialchars($this->{$attr});
        $s=$tempRoll->generAnchor($s, "listItemTitle");
        return $s;
    }
    else if( $attr=="topicsubject" )
    {
        $tempRoll = $gorumroll;
        $tempRoll->list="message";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid="$this->pid,$this->tid";
        $this->topicsubject=htmlspecialchars(replaceBadWords(
            $this->topicsubject));
        if( isset($search->words) && $search->words!="" ) {
            $tempRoll->words=$search->words;            
            $word = split(" ",$search->words);
            for($i=0;isset($word[$i]);$i++) {
                if ($word[$i]=="") continue;
                $this->topicsubject=eregi_replace($word[$i],
                    "<span style='background-color:#ffffcc'>".
                    $word[$i]."</span>",$this->topicsubject);
            }
            $s=$this->topicsubject;
        }
        else $s=$this->topicsubject;        
        $s=$tempRoll->generAnchor($s, "listItemTitle");
        return $s;
    }
    else if ($attr=="creatorName") 
    {
        $tempRoll = $gorumroll;
        global $userClassName;
        $tempRoll->list=$userClassName;
        $tempRoll->method="showdetails";
        $tempRoll->rollid=$this->ownerId;
        saveInFromFrom($tempRoll);
        if( !isset($this->{$attr}) ) // a showDetails eseten lehet
        { // akkor betoltjuk a userbol
            $creator = new $userClassName;
            $creator->id = $this->ownerId;
            $creator->load(); // TODO: mi van, ha a usert toroltek
            $this->{$attr} = $creator->name;
        }
        $s=$tempRoll->generAnchor(htmlspecialchars($this->{$attr}));
        return $s;
    }
    else
    {
        $s = htmlspecialchars($this->{$attr});
    }  
    return $s;
}

function showNewTool($rights)
{
    global $lll,$gorumroll,$gorumrecognised;
    
    $s="";
    if ($gorumroll->list=="message") {// nem spec list
        if ($gorumrecognised) {
            $s.=showNewTool($this,$rights);
        }
        else {
            $s.=$lll["regtopost"];
        }
    }        
    return $s;
}  
   
function showNewToolPlusUrl(&$roll)
{
    $roll->pid = $this->pid;
    $roll->tid = $this->tid;
    // The father of this message is the first message in the list
    // which represents the topic itself:
    $firstMessage = new Message;
    $firstMessage->pid = $this->pid;
    $firstMessage->tid = $this->tid;
    load($firstMessage, array("pid", "tid"));
}    

function showJumperTool(&$s)
{
    global $gorumroll;
    global $lll;
    global $messageView;
    $s="";
    $s.="<table border='0'><tr>";
    if (isset($this->pid)&&$this->pid!=0) {
        $s1=showForumJump($this->pid);
        $s.="<td>".$s1."</td>\n";
        $fJ=1;
    }
    if (isset($this->pid)&&$this->pid!=0) {
        $s1=showTopicJump($this->pid,$this->tid);
        $s.="<td>".$s1."</td>\n";
        $tJ=1;
    }
    if (!isset($tJ)&&!isset($fJ)) {
        $s="";
        return;
    }
    $s.="</tr></td></table>\n";
}  
      
function showSortTool($attr)
{
    //TODO: zorum belit megnezni. (kitoroltem az egeszet)
    $s="";
    return $s;
}
 
function showDetailsTool()
{
    return "";
}

function createForm(&$s)
{
    //global $HTTP_GET_VARS,$HTTP_POST_VARS;
    global $lll,$message_typ,$gorumuser,$gorumroll;
    global $subsInfo,$subscription,$allowSubscriptions;
    global $attFileSize;
    
    $s="";
    $typ = & $message_typ;
    $this->hasObjectRights($hasRight, Priv_create, TRUE);
    if ($gorumroll->rollid) {
        $quoteMess = new Message;
        $quoteMess->id = $gorumroll->rollid;
        load($quoteMess);
    }
    $this->cacheFatherObjects("forum, topic, message");
    if ($attFileSize>0) {        
        $this->removeFromTypeInfo("MAX_FILE_SIZE", "no column");
        $this->removeFromTypeInfo("att_file_upload", "no column");
        $this->removeFromTypeInfo("att_file_upload", "form invisible");
        $this->MAX_FILE_SIZE=$attFileSize;
    }        
    global $fatherForum,$fatherTopic,$fatherMessage;
    if (!( $subscription&subs_topic_new_mess ||
           isset($subsInfo["topic"][$this->tid]) 
           ))
    {
        if ( $allowSubscriptions ) {
          $this->removeFromTypeInfo("subs_mess", "form invisible");
        }
    }        
    if( !$gorumroll->invalid && $gorumroll->rollid ) 
    {
        //TODO: ubb vagy plain vagy html, eszerint quote
        //TODO: dos, unix newline
        //$this->txt=ereg_replace("^",">",$fatherMessage->txt);
        //$this->txt=ereg_replace("\n","\n>",$this->txt);
        $this->txt="[quote]\n$quoteMess->txt\n[/quote]\n";
    }
    if($gorumuser->signature && !$gorumroll->invalid) {
        if (!isset($this->txt)) $this->txt="";
        $this->txt.="\n\n$gorumuser->signature";
    }
    if( !$gorumroll->invalid )
    {
        global $allowHtmlInPost,$allowUbbInPost,$allowSmileyInPost;
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
    }
    if( $gorumroll->invalid ) $gorumroll->invalid=FALSE;
    $ret=$this->generForm($s1);
    $s.=$s1;
} 

function modifyForm(&$s)
{
    global $lll,$gorumroll,$attFileSize;
    
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
    if ($this->att_file_upload!="") {
        $lll["att_file_upload"].=" ($this->att_file_upload)";
    }
    if ($attFileSize>0) {        
        $this->removeFromTypeInfo("MAX_FILE_SIZE", "no column");
        $this->removeFromTypeInfo("att_file_upload", "no column");
        $this->removeFromTypeInfo("att_file_upload", "form invisible");
        $this->MAX_FILE_SIZE=$attFileSize;
    }        
    $this->generForm($s);
} 

function generForm(&$s)
{
    global $lll, $gorumroll;
    global $attFileSize,$attAllowExt,$attForbidExt;
    global $subsInfo,$subscription,$allowSubscriptions;
    global $message_typ;
    
    $s="";
    if ($gorumroll->submit==$lll["preview"]) {
        $s.=$this->showPreview();
    }
    $typ = & $message_typ;
    if (!( $subscription&subs_all_new_mess ||
           isset($subsInfo["forummess"][$this->pid]) ||
           isset($subsInfo["topics"][$this->tid]) 
           ))
    {
        if ( $allowSubscriptions ) {
            $this->removeFromTypeInfo("subs_topic", "no column");
            $this->removeFromTypeInfo("subs_mess", "no column");
        }
    }
    if ($attFileSize>0) {
        $this->removeFromTypeInfo("MAX_FILE_SIZE", "no column");
    }
    else {
        $typ["attributes"]["att_file_upload"][]="no column";
    }
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
    $this->MAX_FILE_SIZE=$attFileSize;
    
    global $allowHtmlInPost, $allowUbbInPost, $allowSmileyInPost;
    if( !$allowSmileyInPost )
    {
        $typ["attributes"]["smiley"][] = "form invisible";
    }
    if( !$allowHtmlInPost && !$allowUbbInPost )
    {
        $typ["attributes"]["coding"][] = "form invisible";        
    }
    elseif( $allowHtmlInPost && !$allowUbbInPost )
    {
        unset($typ["attributes"]["coding"]["values"][1]);
        $lll["coding"]=$lll["enableHtmlInThis"];
        $lll["coding_".MessCode_html]=$lll["yes"];
    }
    elseif( !$allowHtmlInPost && $allowUbbInPost )
    {
        unset($typ["attributes"]["coding"]["values"][0]);
        $lll["coding"]=$lll["enableUbbInThis"];
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

function getListSelect()
{
    global $message_typ,$gorumroll,$gorumuser,$lll, $applName, $userClassName;
    
    if ($gorumroll->list=="message") {        
        $select = "SELECT ".getAllColumns($message_typ, "m").
                  "u.name AS creatorName ".
                  "FROM $applName"."_message AS m, $applName"."_$userClassName AS u ".
                  "WHERE m.ownerId=u.id AND pid=$this->pid".
                  " AND tid=$this->tid";
    }
    elseif ($gorumroll->list=="message_all") {
        $select = "SELECT ".getAllColumns($message_typ, "m").
                  "u.name AS creatorName ".
                  "FROM $applName"."_message AS m, $applName"."_$userClassName AS u ".
                  "WHERE m.ownerId=u.id";
    }
    elseif ($gorumroll->list=="message_new") {
        $select = "SELECT ".getAllColumns($message_typ, "m").
                  "u.name AS creatorName ".
                  "FROM $applName"."_message AS m, $applName"."_$userClassName AS u ".
                  "WHERE m.ownerId=u.id AND ".
                  "m.creationtime>=$gorumuser->logoutTime";
    }
    elseif ($gorumroll->list=="message_user") {
        list($userId,$userName) = explode( ",", $gorumroll->rollid );
        $lll["message_user_ttitle"]=sprintf($lll["message_user_ttitle"],
            htmlspecialchars($userName));    
        $select = "SELECT ".getAllColumns($message_typ, "m").
                  "u.name AS creatorName ".
                  "FROM $applName"."_message AS m, $applName"."_$userClassName AS u ".
                  "WHERE m.ownerId=u.id".
                  " AND m.ownerId=$userId";
    }
    return $select;
}  

}//END Class
?>
