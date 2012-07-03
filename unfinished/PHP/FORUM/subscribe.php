<?php
$subscribe_typ = 
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "type"=>array(
                "type"=>"INT",
                "form invisible"
            ),            
            "userid"=>array(
                "type"=>"INT",
                "form invisible"
            ),            
            "objid"=>array(
                "type"=>"INT",
                "form invisible"
            ),            
            "info"=>array(
                "type"=>"INT",
                "form invisible"
            ),
            "email"=>array(
                "type"=>"VARCHAR",
                "no column"
            ),            
        ),
        "primary_key"=>array("id"),
        "unique_keys"=>array("type,userid,objid"),
        );
        
class Subscribe extends Object
{
    function create()
    {
        global $gorumroll,$lll,$gorumuser;
        global $whatHappened, $infoText;
        global $subscription,$subsInfo, $applName;
        global $gorumrecognised, $gorumauthlevel;
        
        if ($this->type==poll_vote) 
        {
            $poll = new Poll;
            $poll->id=$this->objid;
            load($poll);
            $poll->hasObjectRights($hasRight, Priv_insert, TRUE);
        }    
        if ($this->type==poll_vote && !$this->info) {
            $whatHappened = "form_submitted";
            $infoText = $lll["selectAnOption"];
            return ok;            
        }
        $whatHappened = "form_submitted";
        $this->userid=$gorumuser->id;
        unset($this->id);
        //subscribe: only for real users!
        if ($this->type!=poll_vote && !$gorumrecognised) {
            handleError($lll["permission_denied"]);
        }
        //voting: for guests
        if ($this->type==poll_vote && !$gorumrecognised &&
           $gorumauthlevel!=Loginlib_GuestLevel)
        {
            handleError($lll["permission_denied"]);
        }
        create($this);
        if ($this->type==poll_vote) {
            $query="UPDATE $applName"."_poll SET voted=1".
                   " WHERE id=$this->objid";
            $result=executeQuery($query);
            $whatHappened = "form_submitted";
            if ($gorumroll->fromlist=="message") {
                $gorumroll->method = "showhtmllist";
                $gorumroll->list = "message";
                $gorumroll->class = "message";
                $gorumroll->rollid = $gorumroll->fromid;
            }
            else {
                $gorumroll->method = "showpoll";
                $gorumroll->list = "poll";
                $gorumroll->id = $this->objid;
            }
        }
        $subscription|=$this->type;    
        if ($this->type==subs_forum_new_mess) {
            $subsInfo["forummess"][$this->objid]=1;
        }
        elseif ($this->type==subs_forum_new_topic) {
            $subsInfo["topic"][$this->objid]=1;
        }
        elseif ($this->type==subs_topic_new_mess) {
            $subsInfo["topics"][$this->objid]=1;
        }
        elseif ($this->type==subs_user_new_mess) {
            $subsInfo["usertomess"][$this->objid]=1;
        }
        elseif ($this->type==subs_user_new_topic) {
            $subsInfo["usertotopic"][$this->objid]=1;
        }
        elseif ($this->type==poll_vote) {
            $subsInfo["poll_vote"][$this->objid]=1;
        }        
        /*
        if ($this->type!=poll_vote && $gorumroll->method=="create") {
            $gorumroll->method = "showsubs";
            $gorumroll->list = "user";
            $gorumroll->class = "user";
            $whatHappened = "invalid_form";
        }
        */
        //TODO: ez itt jot ir ki szavazaskor is?
        if ($this->type==poll_vote) $infoText = $lll["vote_ok"];
        else $infoText = $lll["subs_ok"];
        return ok;
    }
    function delete()
    {
        global $gorumroll,$lll,$gorumuser;
        global $whatHappened, $infoText;
        global $subscription,$subsInfo;
        global $gorumrecognised, $gorumauthlevel;
        
        $whatHappened = "form_submitted";
        $this->userid=$gorumuser->id;
        unset($this->id);
        //subscribe: only for real users!
        if ($this->type!=poll_vote && !$gorumrecognised) {
            handleError($lll["permission_denied"]);
        }
        //voting: for guests
        if ($this->type==poll_vote && !$gorumrecognised &&
           $gorumauthlevel!=Loginlib_GuestLevel)
        {
            handleError($lll["permission_denied"]);
        }
        delete($this,array("userid","type","objid"));
        $subscription-=$this->type;
        if ($this->type==subs_forum_new_mess) {
            $subsInfo["forummess"][$this->objid]=1;
        }
        elseif ($this->type==subs_forum_new_topic) {
            $subsInfo["topic"][$this->objid]=1;
        }
        elseif ($this->type==subs_topic_new_mess) {
            unset($subsInfo["topics"][$this->objid]);
        }
        elseif ($this->type==subs_user_new_mess) {
            unset($subsInfo["usertomess"][$this->objid]);
        }
        elseif ($this->type==subs_user_new_topic) {
            unset($subsInfo["usertotopic"][$this->objid]);
        }
        elseif ($this->type==poll_vote) {
            unset($subsInfo["poll_vote"][$this->objid]);
        }        
        $infoText = $lll["unsubs_ok"];
        return ok;
    }
    
    /*
    TODO: kell ez?
    function vote()
    {
        return subscribe_vote($this);
    }
    */
}

function sendMailToSubscribed($forum,$topic,$mess,$user,$which="mess")
{
    global $errTxt,$gorumroll,$gorumuser;
    
    
    if ($which=="topic") {
        $order[0]=subs_all_new_topic;
        $order[1]=subs_forum_new_topic;
        $order[2]=subs_user_new_topic;
    }
    else {
        $order[0]=subs_all_new_mess;
        $order[1]=subs_forum_new_mess;
        $order[2]=subs_topic_new_mess;
        $order[3]=subs_user_new_mess;
    }
    for($i=0;isset($order[$i]);$i++) {
        if ($order[$i]==subs_user_new_mess ||
            $order[$i]==subs_user_new_topic)
        {
            $whichclass=$user->id;
        }
        elseif ($order[$i]==subs_forum_new_topic||
                $order[$i]==subs_forum_new_mess)
        {
            $whichclass=$forum->id;
        }
        elseif ($order[$i]==subs_topic_new_mess) {
            $whichclass=$topic->id;
        }
        else $whichclass=0;
        $ret=sendToSubsCategory($order[$i],$whichclass,$forum,$topic,
                                $mess,$user,$info);
        if ($info) {
             break;   
        }
    }
    return ok;
}
function sendToSubsCategory($whichtype,$whichclass,$forum,$topic,$mess,$user,&$info)
{
    global $gorumuser, $applName, $userClassName;
    $sub = new Subscribe;
    $query="SELECT type,objid,userid,info,email".
           " FROM $applName"."_subscribe s, $applName"."_$userClassName u".
           " WHERE type=$whichtype".
           " AND objid=$whichclass".
           " AND userid=u.id".
           " AND info=0";
    $ret = $sub->loadObjectsSQL($query,$list, TRUE);
    if ($ret==not_found_in_db) {
        return ok;
    }
    makeMailText($whichtype,$forum,$topic,$mess,$user,$subject,
                      $text);
    global $adminEmail;
    if ($adminEmail!="") $from="From: $adminEmail";
    else $from="";
    //TODO: ret - valamit csinalni
    //send emails 
    for($i=0;isset($list[$i]);$i++) {
        if ($list[$i]->type==$whichtype &&
            $list[$i]->info==0&&$gorumuser->id!=$list[$i]->userid)
        {
            $ret=mail($list[$i]->email,$subject,$text,$from);
        }
    }
    //bejelolni, hogy mar kuldtunk ebben a kategoriaban
    $query="UPDATE $applName"."_subscribe SET info=1 WHERE type=$whichtype";
    $result=executeQuery($query);
    //TODO: egy ido utan nagyon sok subscribe osszgyulhet -micsinalni?
    return ok;
}
function makeMailText($whichtype,$forum,$topic,$mess,$user,&$subject,&$text)
{
    global $lll,$HTTP_SERVER_VARS;
    $url="http:/"."/$HTTP_SERVER_VARS[SERVER_NAME]".
         $HTTP_SERVER_VARS["SCRIPT_NAME"];
    $tempRoll = new Roll;
    //$tempRoll->method = "showhtmllist";
    $text="";
    $text.=$lll["mail_greet"]."\n\n";
    $subject=$lll["mail_subj_subs"];       
    $text.=sprintf($lll["mail_text_subs_first"],$url,$forum->name,
                   $topic->subject,$mess->subject,$user->name);
    switch($whichtype) {
        case subs_all_new_mess :
            $tempRoll->list="message_new";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        case subs_all_new_topic :
            $tempRoll->list="topic_newt";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        case subs_forum_new_mess :
            $tempRoll->list="message_new";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        case subs_forum_new_topic :
            $tempRoll->list="topic_newt";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        case subs_topic_new_mess :
            $tempRoll->list="message";
            $tempRoll->rollid="$forum->id,$topic->id";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        case subs_user_new_mess :
            $tempRoll->list="message_user";
            $tempRoll->rollid="$user->id,$user->name";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        case subs_user_new_topic :
            $tempRoll->list="topic_user";
            $tempRoll->rollid="$user->id,$user->name";
            $url2=$tempRoll->makeUrl("mixed");
            break;
        default:
            $txt="illegal value in switch in makeMailText";
            handleError($txt);
    }
    $text.=sprintf($lll["mail_text_subs_second"],$url2);
    $text.="\n".$lll["mail_subs_all_new_mess_nomore"];
    //$text.="\n".$lll["mail_end"]="Best Regards!";
    //$text.="\nZorum Team";//TODO: alairas

    return ok;
}
function showUserSubscriptions(&$s)
{
    global $whatHappened,$infoText,$lll,$xi;
    global $gorumroll; 
    
    $s="";
    $s.="<table border='0' width='100%'><tr><td class='cell'>";//keret
    $s.=showSubscribe();
    
    $s.="<tr><td>";
    $s.="<table width='100%' border='0' cellpadding='0'".
        " cellspacing='0'>";
    $s.="<tr><td colspan='3'><img src='$xi/b.gif' height='15'>".
        "</td></tr>\n";
    $s.="<tr><td valign='top'>";
    $sU = new SubsUserList;
    $sU->showHtmlList($s1);
    $s.=$s1;
    $s.="</td>\n";
    $s.="<td><img src='$xi/b.gif' width='15'></td>\n";
    $s.="<td valign='top'>";
    $sU = new SubsForumList;
    $sU->showHtmlList($s1);
    $s.=$s1;
    $s.="</tr></table>";
    $s.="<table width='100%' border='0' cellpadding='0'".
        " cellspacing='0'>";
    $s.="<tr><td colspan='2'><img src='$xi/b.gif' height='15'>".
        "</td></tr>\n";
    $s.="<tr><td valign='top'>";
    $sU = new SubsTopicList;
    $sU->showHtmlList($s1);
    $s.=$s1;
    $s.="</td>";
    $s.="</tr></table>";

    $s.="</td></tr></table>";//keret
}
function showSubscribe()
{
    global $lll,$subscription,$subsInfo,$gorumuser,$gorumroll;
    $s="";
    $s.=generBoxUp();
    $s.="<tr><th align='center' class='cell'>";
    if ($gorumuser->email="") return $s;
    if ($subscription&subs_all_new_mess) {
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->method = "delete";   
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);
        $txt=$lll["unsubs_all_new_mess"];
    }
    else {
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->method = "create";   
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);
        $txt=$lll["subs_all_new_mess"];
    }
    $tempRoll->type=subs_all_new_mess;
    //$tempRoll->frommethod="showsubs";
    if ($subscription&subs_all_new_mess) {
        $txt=$lll["unsubs_all_new_mess"];
    }
    else $txt=$lll["subs_all_new_mess"];
    $s.=$tempRoll->generAnchor($txt,"listItem");
    $s.="</th><th align='center' class='cell'>";
    if ($subscription&subs_all_new_topic) {
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->method = "delete";   
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);
        $txt=$lll["unsubs_all_new_topic"];
    }
    else {
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->method = "create";   
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);
        $txt=$lll["subs_all_new_topic"];
    }
    $tempRoll->type=subs_all_new_topic;
    $s.=$tempRoll->generAnchor($txt,"listItem");
    $s.="</th></tr>";
    $s.=generBoxDown();
    return $s;
}
function canRate($base)
{
    global $gorumuser,$gorumrecognised,$gorumauthlevel,$regOnlyRate;

    if($gorumuser->id==$base->ownerId) return FALSE;
    if($regOnlyRate=="yes" && $gorumrecognised) return TRUE;
    if( ($gorumrecognised || $gorumauthlevel==Loginlib_GuestLevel) &&
          $regOnlyRate=="no") return TRUE;
    return FALSE;
}
//subslist
class SubsList extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        $hasRight=TRUE;
        return ok;
    }    
    function showTools($rights)
    {
        global $lll,$gorumroll,$xi;
        $s="";
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->method = "delete";   
        $tempRoll->rollid = 0;
        $tempRoll->type=$this->type;
        $tempRoll->objid=$this->id;
        saveInFrom($tempRoll);
        $s.=$tempRoll->generImageAnchor("$xi/delete.gif",
                                   $lll["icon_unsubs"],17,22);
        return $s;
    }    
    function showNewTool($rights)
    {
        return "";
    }    
}
$subsuserlist_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"TEXT",
                "no column"
            ),
            "name"=>array(
                "type"=>"TEXT",
                "no column",
                "list"
            ),
            "type"=>array(
                "type"=>"TEXT",
                "no column",
                "list"
            )
        ),
        "sort_criteria_attr"=>"id",
        "sort_criteria_dir"=>"a"        
    );
class SubsUserList extends SubsList
{
    function showHtmlList(&$s)
    {
        global $gorumroll;

        global $helpType1,$helpType2;
        $helpType1=subs_user_new_mess;
        $helpType2=subs_user_new_topic;
        $savelist=$gorumroll->list;
        $gorumroll->list="subsuserlist";
        $gorumroll->subsuserlistsorta="name";
        Object::showHtmlList($s);
        $gorumroll->list=$savelist;
    }
    function showListVal($attr)
    {
        global $lll,$gorumroll, $userClassName;
        $s="";
        if( $attr=="name" ) {
            $tempRoll = $gorumroll;
            global $userClassName;
            $tempRoll->list=$userClassName;
            $tempRoll->method="showdetails";
            $tempRoll->rollid=$this->id;
            saveInFromFrom($tempRoll);
            $s=$tempRoll->generAnchor($this->{$attr});
        } 
        elseif ($attr=="type") {
            if ($this->{$attr}==subs_user_new_mess) {
                $s=$lll["message"];
            }
            elseif ($this->{$attr}==subs_user_new_topic) {
                $s=$lll["topic"];
            }
        }
        else $s=htmlspecialchars($this->{$attr});
        return $s;
    }
    function getListSelect()
    {
        global $gorumuser,$helpType1,$helpType2, $applName, $userClassName;
        $select = "SELECT u.id AS id,name, type".
                  " FROM $applName"."_subscribe s, $applName"."_$userClassName u".
                  " WHERE (type=$helpType1".
                  " OR type=$helpType2)".
                  " AND userid=$gorumuser->id".
                  " AND objid=u.id";
        return $select;
    } 
}
$subsforumlist_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"TEXT",
                "no column"
            ),
            "name"=>array(
                "type"=>"TEXT",
                "no column",
                "list"
            ),
            "type"=>array(
                "type"=>"TEXT",
                "no column",
                "list"
            )
        ),
        "sort_criteria_attr"=>"id",
        "sort_criteria_dir"=>"a"        
    );
class SubsForumList extends SubsList
{
    function showHtmlList(&$s)
    {
        global $gorumroll;

        global $helpType1,$helpType2;
        $helpType1=subs_forum_new_mess;
        $helpType2=subs_forum_new_topic;
        $savelist=$gorumroll->list;
        $gorumroll->list="subsforumlist";
        $gorumroll->subsforumlistsorta="name";
        Object::showHtmlList($s);
        $gorumroll->list=$savelist;
    }
    function showListVal($attr)
    {
        global $lll;
        $s="";
        if( $attr=="name" ) {
            $f = new Forum;
            $f->id=$this->id;
            $f->description="";
            $f->name=$this->name;
            $f->readgroup=0;
            $f->writegroup=0;
            $f->topicgroup=0;
            $f->hasChild="no";
            $f->allowAnonym="no";
            return $f->showListVal("name");
        } 
        elseif ($attr=="type") {
            if ($this->{$attr}==subs_forum_new_mess) {
                $s=$lll["message"];
            }
            elseif ($this->{$attr}==subs_forum_new_topic) {
                $s=$lll["topic"];
            }
        }
        else $s=htmlspecialchars($this->{$attr});
        return $s;
    }
    function getListSelect()
    {
        global $gorumuser,$helpType1,$helpType2, $applName;
        $select = "SELECT f.id AS id,name, type".
                  " FROM $applName"."_subscribe s, $applName"."_forum f".
                  " WHERE (type=$helpType1".
                  " OR type=$helpType2)".
                  " AND userid=$gorumuser->id".
                  " AND objid=f.id";
        return $select;
    } 
}
$substopiclist_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"TEXT",
                "no column"
            ),
            "pid"=>array(
                "type"=>"TEXT",
                "no column"
            ),
            "subject"=>array(
                "type"=>"TEXT",
                "no column",
                "list"
            ),
            "type"=>array(
                "type"=>"TEXT",
                "no column"
            )
        ),
        "sort_criteria_attr"=>"id",
        "sort_criteria_dir"=>"a"        
    );
class SubsTopicList extends SubsList
{
    function showHtmlList(&$s)
    {
        global $gorumroll;

        global $helpType;
        $helpType=subs_topic_new_mess;
        $savelist=$gorumroll->list;
        $gorumroll->list="substopiclist";
        $gorumroll->substopiclistsorta="subject";
        Object::showHtmlList($s);
        $gorumroll->list=$savelist;
    }
    function showListVal($attr)
    {
        $s="";
        if( $attr=="subject" ) {
            $t = new Topic;
            $t->id=$this->id;
            $t->pid=$this->pid;
            $t->subject=$this->subject;
            return $t->showListVal("subject");
        } 
        else $s=htmlspecialchars($this->{$attr});
        return $s;
    }
    function getListSelect()
    {
        global $gorumuser,$helpType, $applName;
        $select = "SELECT t.id AS id,pid,subject, type".
                  " FROM $applName"."_subscribe s, $applName"."_topic t".
                  " WHERE type=$helpType".
                  " AND userid=$gorumuser->id".
                  " AND objid=t.id";
        return $select;
    } 
}
?>
