<?php
/*
Kerdeses szin headermethod a ???
colheader magassaga nem (konnyen) beallithato

function focusField(name) {
  for(i=0; i < document.forms.length; ++i) {
    var obj = document.forms[i].elements[name];
    if (obj && obj.focus) {obj.focus();}
  }
}

textarea:focus, input:focus {
    background-color: #ffffee;
}

TODO:
A moderator nincs kijelezve a fooldalon. 
A moderatorok joga sincs rendberakva.
A moderatorok ideiglenesen ki vannak szedve a rendszerbol,
a features listan is ki lehetne venni.


*/

class ZorumInit extends Init
{
function initializeSystemSettings()
{
    global $allowSubscriptions;
    
    $this->detectBrowser();
    Init::initializeSystemSettings();
    $this->initializeTypeInfos();
    $this->initializeUbbRules();
    $this->initializeGlobalStat();
    if ($allowSubscriptions) $this->initializeSubscriptions();
}

function initializeUserSettings()
{
    global $allowSubscriptions;
    Init::initializeUserSettings(); 
    global $blockThat;
    blockBlacklist($blockThat);
    $this->initializeTimeoutServices();
    if ($allowSubscriptions) $this->resetSentMails();
    
    // Beallitjuk az allowHtmlInPostot a Setting alapjan eloallott
    // allowHtmlInPost es az aktualis user alapjan:
    global $allowHtmlInPost, $gorumuser, $gorumrecognised;
    if( !$gorumrecognised ) $allowHtmlInPost=FALSE;
    elseif( $allowHtmlInPost==Group_None ) $allowHtmlInPost=FALSE;
    elseif( $allowHtmlInPost==Group_All ) $allowHtmlInPost=TRUE;
    elseif( $allowHtmlInPost==Group_OnlyAdmin )
    {
        $allowHtmlInPost = $gorumuser->isAdm;
    }
    elseif( $allowHtmlInPost==Group_OnlyAdminAndMod )
    {
        $allowHtmlInPost = $gorumuser->isAdm || $gorumuser->isMod; 
    }
    else
    {
        $gm = new GroupMember;
        $gm->groupId = $allowHtmlInPost;
        $gm->userId = $gorumuser->id;
        $allowHtmlInPost = (load($gm)==ok);
    }
}
    
function resetSentMails()
{
    global $gorumuser, $applName;

    //TODO: egy bitet lehetne mashol is tartani, hogy ezt ne kelljen
    //mindig felhivni.
    $query="UPDATE $applName"."_subscribe SET info=0 WHERE userid=".
           $gorumuser->id." AND type<=".subs_user_new_topic;
    executeQuery($query);
}

function initializeTimeoutServices()
{
    global $gorumroll;
    global $timeOut;
    global $gorumuser;
    global $userClassName;
    global $HTTP_COOKIE_VARS;
    
    if( !isset($HTTP_COOKIE_VARS["zorumTimeOut"]) )
    {
        if( isset($gorumuser->lastClickTime) )
        {
            $gorumuser->logoutTime = $gorumuser->lastClickTime;
        }
        else $gorumuser->logoutTime = 0;    
    }
    else if( !isset($gorumuser->logoutTime) ) $gorumuser->logoutTime =0;    
    $gorumuser->lastClickTime = time();
    $user = new $userClassName;
    $user->init( array("id"=>$gorumuser->id, 
                       "logoutTime"=>$gorumuser->logoutTime, 
                       "lastClickTime"=>$gorumuser->lastClickTime) );
    modify($user);
    setcookie("zorumTimeOut", 1, time()+ $timeOut * 60);
}

function initializeTypeInfos()
{
    global $textAreaRows, $textAreaCols;
    global $message_typ;
    global $topic_typ;
    
    $message_typ["attributes"]["txt"]["rows"] = $textAreaRows;
    $message_typ["attributes"]["txt"]["cols"] = $textAreaCols;
    $message_typ["attributes"]["subject"]["length"] = $textAreaCols;
    $topic_typ["attributes"]["txt"]["rows"] = $textAreaRows;
    $topic_typ["attributes"]["txt"]["cols"] = $textAreaCols;
    $topic_typ["attributes"]["subject"]["length"] = $textAreaCols;
    return ok;
}

function initializeUbbRules()
{
    global $gorumreplacementrules;
    global $allowUbbInPost;
    
    if( $allowUbbInPost )
    {
        $ubbrules = new Ubb;
        $ubbrules->loadObjectsSql( "", $ubbrules );
    }
    else $ubbrules=array();
    $smilies = new Smiley;
    $smilies->loadObjectsSql( "", $smilies );
    
    $gorumreplacementrules=array_merge($ubbrules,$smilies);
}


function initializeGlobalStat()
{
    global $zorumglobalstat;
    $zorumglobalstat = new GlobalStat;
    $zorumglobalstat->id=1;
    $ret=load($zorumglobalstat);
    if ($ret!=ok) handleError("can not load globalstat");
}

function initializeSubscriptions()
{
    global $gorumuser,$subscription,$subsInfo, $applName;

    $subsForumIds = array();
    $subsInfo = array();
    $subsInfo["forummess"] = array();
    $subsInfo["topic"] = array();
    $subsInfo["topics"] = array();
    $subsInfo["usertomess"] = array();
    $subsInfo["usertotopic"] = array();
    $subsInfo["reply"] = array();
    $subsInfo["favuser"] = array();
    $subsInfo["favforum"] = array();
    $subsInfo["favtopic"] = array();
    $subsInfo["poll_vote"] = array();
    $sub = new Subscribe;
    $query="SELECT * FROM $applName"."_subscribe WHERE userid=$gorumuser->id";
    $ret = $sub->loadObjectsSQL($query,$list, TRUE);
    if ($ret==not_found_in_db) {
        return ok;
    }
    $subscription=0x0;
    for($i=0;isset($list[$i]);$i++) {
        $subscription|=$list[$i]->type;//build subscription mask
        if ($list[$i]->type==subs_forum_new_mess) {
            $subsInfo["forummess"][$list[$i]->objid]=1;
        }
        elseif ($list[$i]->type==subs_forum_new_topic) {
            $subsInfo["topic"][$list[$i]->objid]=1;
        }
        elseif ($list[$i]->type==subs_topic_new_mess) {
            $subsInfo["topics"][$list[$i]->objid]=1;
        }
        elseif ($list[$i]->type==subs_user_new_mess) {
            $subsInfo["usertomess"][$list[$i]->objid]=1;
        }
        elseif ($list[$i]->type==subs_user_new_topic) {
            $subsInfo["usertotopic"][$list[$i]->objid]=1;
        }
        elseif ($list[$i]->type==poll_vote) {
            $subsInfo["poll_vote"][$list[$i]->objid]=1;
        }
    }
}

function showApp()
{
    global $gorumcontent,$navBarText,$infoText;
    global $upperTemplate,$lowerTemplate,$lll,$xi,$NS4;

    
    $s="";
    if (!$NS4) $s.=$this->bgPageBegin();

    $this->makeLoginMenu($loginMenu);    
    $this->makeUserMenu($userMenu);    
    $this->makeZorumMenu($zorumMenu,$loginMenu,$userMenu);

    $userStatus=$this->showUserStatus();
    $menuStr=$this->showZorumMenu2($zorumMenu);
    $s.=$this->zorumHeader($userStatus,$menuStr);
    //$s.=$this->showZorumMenu($zorumMenu);
    global $pgIndent;
    if (!$NS4) {
        $s.="<table border='0' width='100%' cellpadding='0'".
            " cellspacing='0'><tr><td><img src='$xi/b.gif'".
            " width='$pgIndent'></td>\n<td>";//bekezdes
    }
    $s1=showBlocked();
    if ($s1) $s.=vertSpacer().$s1;
    if ($navBarText) {
        $s.=vertSpacer();
        $s.="<table border='0' cellpadding='0' cellspacing='0'>";
        $s.="<tr>";
        $s.="<td><img src='$xi/b.gif' width='15'></td>\n";
        $s.="<td class='navBar'>$navBarText";
        $s.="</td></tr></table>\n";
    }
    if( $infoText ) {
        $s.=vertSpacer();
        $s.=$this->showAppInfoText($infoText)."\n";
    }
    if ($navBarText) {
        $s.=vertSpacer();
    }
    else {
        $s.=vertSpacer(42);
    }
    $s.=$gorumcontent;  
    $s.=vertSpacer();
    $s.=vertSpacer();
    $s.=$this->showStatLine();
    if (!$NS4) {
        $s.="</td>\n<td><img src='$xi/b.gif' width='$pgIndent'></td></tr>".
            "</table>\n";//bekezdes vege
    }
    $s.=vertSpacer();
    if (!$NS4) $s.=$this->bgPageEnd();
    $s.=$this->showVersionFooter();
    return $s;
}
function zorumHeader($userStatus,$menu)
{
    global $xi,$gorumrecognised,$gorumuser,$lll,$zorumImUrl;
    $s="";
    if (!isset($zorumImUrl)) {
        $zorumImUrl="http://zorum.phpoutsourcing.com";
    }
    $s.="<table border='0' cellpadding='0' cellspacing='0'".
        " width='100%'>\n";
    //vertical spacer
    $s.="<tr>\n";
    $s.="<td colspan='4' background='$xi/b.gif' width='100%'>";
    $s.="<img src='$xi/b.gif' height='13'>";
    $s.="</td>\n";
    $s.="</tr>";
    //header
    $s.="<tr>\n";
    $s.="<td width='20%'><a href='$zorumImUrl'>".
        "<img src='$xi/header_left.gif' width='542' border='0'".
        " height='44'></a></td>\n";
    $s.="<td width='1%' background='$xi/header_left_back.gif'>".
        "<img src='$xi/b.gif' width='1' height='1'></td>\n";
    $s.="<td width='1%'><img src='$xi/header_middle.gif' width='43'".
        " height='44'></td>\n";
    $s.="<td width='77%' background='$xi/header_right_back.gif'>".
        "<img src='$xi/b.gif'></td>\n";
    $s.="</tr>\n";
    //status line
    $s.="<tr>\n";
    $s.="<td colspan='4' background='$xi/menu_back.gif'".
        " height='17' valign='middle' align='left'>";
    $s.="<table border='0' cellpadding='0' cellspacing='0'".
        " background='$xi/b.gif'>\n<tr>".
        "<td valign='middle' class='userstatus'>";
    $s.="<img src='$xi/b.gif' width='25' height='1'>";
    $s.=$userStatus;
    //$s.="iii";
    $s.="</td></tr></table>";
    $s.="</td>\n";
    $s.="</tr>";
    //menu
    $s.="<tr>\n";
    $s.="<td width='542' height='23' valign='middle' colspan='2'>";
    $s.=$menu;
    $s.="</td>\n";
    $s.="<td width='1%'><img src='$xi/menu_middle.gif' width='43'".
        " height='23'></td>\n";
    $s.="<td width='98%' background='$xi/menu_right_back.gif'>".
        "<img src='$xi/b.gif'></td>\n";
    $s.="</tr>\n";
    //menu alatt
    $s.="<tr>\n";
    $s.="<td width='542' bgcolor='#6699CC' colspan='2'>".
        "<img src='$xi/b.gif' width='542' height='10'></td>\n";
    $s.="<td width='1%'><img src='$xi/menu_bottom.gif' width='43'".
        " height='10'></td>\n";
    $s.="<td width='98%' bgcolor='white'>".
        "<img src='$xi/b.gif' height='10'></td>\n";
    $s.="</tr>\n";

    $s.="</tr>\n";
    $s.="</table>\n";
    return $s;
}
function showUserStatus()
{
    global $gorumrecognised,$gorumuser,$lll;
    $s="";
    if ($gorumrecognised) {
       $s.=sprintf($lll["loggedas"],htmlspecialchars($gorumuser->name));
    }
    else $s.=$lll["logorreg"];
    return $s;
}
function bgPageBegin()
{
    $s="";
    //frame
    $s.="<table border='0' class='pageframe' width='100%'".
        " cellpadding='0' cellspacing='0'><tr><td>\n";
    $s.="<table border='0' width='100%' cellpadding='0'".
        " cellspacing='1'><tr><td>\n";
    //feher 1
    $s.="<table border='0' class='pagespacer' width='100%'".
        " cellpadding='0' cellspacing='0'><tr><td>\n";
    $s.="<table border='0' width='100%' cellpadding='0'".
        " cellspacing='1'>\n";
    $s.="<tr><td>";
    return $s;
}
function bgPageEnd()
{
    $s="";
    $s.="</td></tr>";
    $s.="\n</table>\n</td></tr></table>\n";
    $s.="\n</td></tr></table>\n</td></tr></table>\n";
    return $s;
}

function showAppInfoText($txt)
{
    global $xi;
    $s="";
    //shadow
    $s.="<table border='0' cellpadding='0' cellspacing='0'".
        " width='100%'><tr>\n<td>";
    
    $s.="<tr class='infoText'><td align='center'>";
    
    $s.="<table border='0' cellpadding='2'><tr><td class='infoText'>";
    $s.=$txt;
    $s.="</td></tr></table>\n";
    
    $s.="</td></tr>\n";

    //shadow
    $s.="</td></tr>";
    $s.="<tr><td background='$xi/menushadow.gif'><img src='$xi/b.gif' ".
        "height='3'>";
    $s.="</td></tr>";
    $s.="</table>\n";
    return $s;
}
function showZorumMenu2($menu)
{
    global $xi;

    $s="";
    
    $s.="<table border='0' cellpadding='0' cellspacing='0'>";
    $s.="<tr valign='middle'>";
    $s.="<td>\n";
    $s.="<img src='$xi/b.gif' width='25' height='1'>";
    $s.="</td>";

    foreach($menu as $id=>$menuPoint) {
        $s.="<td class='mainmenu' nowrap>\n";
        $s.=$menuPoint;
        $s.="</td>\n";
        $s.="<td>\n";
        $s.="&nbsp;";
        $s.="</td>";
        $s.="<td align='center'>";
        $s.="<img src='$xi/menupoint.gif' width='6' height='6'>";
        $s.="</td>";
        $s.="<td>\n";
        $s.="&nbsp;&nbsp;&nbsp;\n";
        $s.="</td>";
    }

    $s.="</td></tr>";
    $s.="</table>";

    return $s;
}
function showZorumMenu($menu)
{
    global $gorumroll,$lll,$allowSubscriptions;
    global $gorumauthlevel,$gorumrecognised,$gorumuser,$xi;

    $s="";
    //shadow
    $s.="<table border='0' cellpadding='0' cellspacing='0'".
        " width='100%'><tr><td>";
    
    $s.=generBoxUp();
    $s.="<tr class='header'><td>";
    $s.="<table border='0' cellpadding='0' cellspacing='0'>";
    $s.="<tr valign='middle'>";
    $s.="<td>\n";
    $s.="&nbsp;&nbsp;\n";
    $s.="</td>";

    foreach($menu as $id=>$menuPoint) {
        $s.="<td align='center'>";
        $s.="<img src='$xi/menubullet.gif' width='3' height='3'>";
        $s.="</td>";
        $s.="<td>\n";
        $s.="&nbsp;";
        $s.="</td>";
        $s.="<td>\n";
        $s.=$menuPoint;
        $s.="</td>\n";
        $s.="<td>\n";
        $s.="&nbsp;&nbsp;\n";
        $s.="</td>";
    }

    $s.="<td>\n";
    $s.="</tr></table>";
    $s.="</td></tr>";
    $s.="<tr><td class='header'>";
    $s.="<span class='userstatus'>&nbsp;";
    if ($gorumrecognised) {
       $s.=sprintf($lll["loggedas"],htmlspecialchars($gorumuser->name));
    }
    else $s.=$lll["logorreg"];
    $s.="</span>";
    $s.="</td></tr>";
    $s.=generBoxDown();


    //shadow
    $s.="</td></tr>";
    $s.="<tr><td background='$xi/menushadow.gif'><img src='$xi/b.gif' ".
        "height='3'>";
    $s.="</td></tr>";
    $s.="</table>\n";
    return $s;
}
function makeZorumMenu(&$menu,$loginMenu,$userMenu)
{
    global $gorumuser, $gorumroll, $userClassName;
    global $gorumauthlevel, $gorumrecognised;
    global $necessaryAuthLevel;
    global $lll;

    $menu=$loginMenu;

    $tempRoll = $gorumroll;
    $tempRoll->list = "secmenu";
    $tempRoll->method = "userfunctions";   
    $tempRoll->rollid = 0;
    if( $this->display( Init_userfunc ))
    {
        $menu[Init_userfunc]=$tempRoll->generAnchor($lll["userfunc"]);
    }

    $tempRoll=$gorumroll;
    $tempRoll->list = "secmenu";
    $tempRoll->method = "lists";
    $tempRoll->rollid = 0;    
    if( $this->display( Init_lists ))
    {
        $menu[Init_lists]=$tempRoll->generAnchor($lll["lists"]);
    }
    
    $tempRoll = $gorumroll;
    $tempRoll->list = "search";
    $tempRoll->method = "search_form";
    global $search;
    if( isset($search) )
    {
        $tempRoll->words = $search->words;
        $tempRoll->where = $search->where;
        $tempRoll->operator = $search->operator;
        $tempRoll->userName = $search->userName;
        $tempRoll->sortCriteria = $search->sortCriteria;
        $tempRoll->resultNum = $search->resultNum;
        $tempRoll->forumId = $search->forumId;
        $tempRoll->matches = $search->matches;
    }    
    saveInFrom($tempRoll);
    if( $this->display( Init_dsearch ))
    {
        $menu[Init_dsearch]=$tempRoll->generAnchor($lll["search"]);
    }
    
    $tempRoll = $gorumroll;
    $tempRoll->list = "secmenu";
    $tempRoll->method = "adminfunc";
    $tempRoll->rollid = 0;
    if( $this->display( Init_adminfunc ))
    {
        $menu[Init_adminfunc]=$tempRoll->generAnchor($lll["adminfunc"]);
    }
    
    $menu[Init_home] = $userMenu[Init_home];
}
function display( $what )
{    
    // Itt meg kell adni az applikaciospecifikus menupontok
    // kirakasi felteteleit:
    hasAdminRights($isAdm);
    switch( $what )
    {
    case Init_userfunc:
        return TRUE;
    case Init_lists:
        return TRUE;
    case Init_dsearch:
        return TRUE;
    case Init_cangePwd:
        return FALSE;    
    case Init_adminfunc:
        return $isAdm;
    default:
        return Init::display($what);           
    }
}
function showVersionFooter()
{
    global $zorumglobalstat,$lll;
    $s="";
    $s.=vertSpacer();
    $s.="<center><span class='powfooter'>$lll[powby] ".
        "<a href='http://zorum.phpoutsourcing.com' class='powfooter'>".
        "Zorum</a> ";
    $s.=str_replace("_", ".", $zorumglobalstat->instver);
    $s.="</span></center>";
    return $s;
}
function showStatLine()
{
    global $zorumglobalstat,$lll,$pgIndent,$xi;
    $s="";
    $s.="<table border='0' cellpadding='0' cellspacing='0'".
        " width='100%'><tr>";
    $s.="<td align='left' class='footstat' valign='top' >";
    //$s.="<img src='$xi/b.gif' width='4' height='1'>";
    $s.="$lll[timenow]: ".showTimestamp(time())."<br>\n";
    $s.=showUsersOnlineRow()."<br>\n";
    $s.="</td>\n";
    $s.="<td align='center' class='footstat' valign='top' >";
    $s.=$this->showLegend()."</td>\n";
    $s.="<td align='right' class='footstat' valign='top' >";
    $s.="$lll[forums]: $zorumglobalstat->forumnum<br>\n";
    $s.="$lll[topics]: $zorumglobalstat->topicnum<br>\n";
    $s.="$lll[messages]: $zorumglobalstat->entrynum<br>\n";
    $s.="$lll[regusers]: $zorumglobalstat->usernum<br>\n";
    $s.="</td>\n";
    $s.="</tr></table>\n";
    return $s;
}
function showLegend()
{
    global $gorumroll,$lll,$xi;
    $s="";
    if ($gorumroll->list=="forum" &&
        $gorumroll->method=="showhtmllist")
    {
        $f = new Forum;
        $s.="<table border='0' cellspacing='3' cellpadding='0'>";
        $s.="<tr class='legend'>";
        $s.="<td valign='middle'>".$f->showNoNewmessForumImage().
            "</td>\n";
        $s.="<td valign='middle'>".$lll["nonewmess"].
            " </td></tr>\n";
        $s.="<tr class='legend'>";
        $s.="<td valign='middle'>".$f->showNewmessForumImage().
            "</td>\n";
        $s.="<td valign='middle'>".$lll["newmess"].
            " </td>\n";
        $s.="</tr></table>\n";
    }
    elseif ($gorumroll->list=="topic" &&
        $gorumroll->method=="showhtmllist")
    {
        $sp="10";
        $t = new Topic;
        $s.="<table border='0' cellspacing='3' cellpadding='0'>";
        $s.="<tr class='legend'>";
        $s.="<td valign='middle'>".$t->showNoNewmessTopicImage().
            "</td>\n";
        $s.="<td valign='middle'>".$lll["nonewmess"].
            "</td>\n";
        $s.="<td><img src='$xi/b.gif' width='$sp'></td>";
        $s.="<td valign='middle'>".$t->showNoNewmessHotTopicImage().
            "</td>\n";
        $s.="<td valign='middle'>".$lll["nonewmesshot"].
            "</td></tr>\n";
        $s.="<tr class='legend'>";
        $s.="<td valign='middle'>".$t->showNewmessTopicImage().
            "</td>\n";
        $s.="<td valign='middle'>".$lll["newmess"].
            "</td>\n";
        $s.="<td><img src='$xi/b.gif' width='$sp'></td>";
        $s.="<td valign='middle'>".$t->showNewmessHotTopicImage().
            "</td>\n";
        $s.="<td valign='middle'>".$lll["newmesshot"].
            "</td>\n";
        $s.="</tr></table>\n";
        
    }
    return $s;
}
function detectBrowser()
{
    global $NS4,$boxWithFrame,$fixCss,$HTTP_SERVER_VARS;
    
    $ua=$HTTP_SERVER_VARS["HTTP_USER_AGENT"];
    $s1=explode(" ",$ua);
    $s2=explode("/",$s1[0]);
    $typ=$s2[0];
    $s3=explode(".",$s2[1]);
    $ver=(int)$s3[0];
    $subVer=(int)substr($s3[1],0,1);
    if ($typ=="Mozilla" && $ver==4 && $subVer!=0) {
        $NS4=TRUE;
        $boxWithFrame=FALSE;
        $fixCss="style4.css";
    }
    else $NS4=FALSE;
}
/*
function showNavBar()
{
    global $gorumroll,$lll;

    $s="";
    $tempRoll = $gorumroll;
    $tempRoll->list="forum";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $s.=$tempRoll->generAnchor($lll["forums"]);
    return $s;
}
*/

}//End class    
function showAppPopupHelp($helpTitle, $helpText)
{
    echo <<< EOF
<head>
<style TYPE="text/css">
body {background: #f0f6fd; color: #546753; font-size: 12px; margin: 2em;}
h1 {color: ##333366; font-size: 14px; margin: 3em;}
</style>
</head>
<h1>$helpTitle</h1>
$helpText
EOF;
die();
}
?>
