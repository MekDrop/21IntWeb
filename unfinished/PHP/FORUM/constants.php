<?php
$userClassName = "zorumuser";
$initClassName = "zoruminit";

$mainBoxPadding="6";
$showIcon = TRUE;
$fixCss="style.css";

$applName = "zorum";
$scriptName = "index.php";

$defaultMethod = "showhtmllist";
$defaultList = "forum";
$defaultRollId = 0;
$headerHeight="29";
$boxWithFrame=TRUE;
$list2Colors=0;
$vertSpacer=14;
$pgIndent="8";
$chAdmAct=TRUE;
$dontSetLastClickTime=1;//a gorum az init.TimeoutServ. elott atirna

$blockSortDirection="lastBlockLast";
$treeIdxBase=1073741824;//2^30

$blockSize = 50;

$dbClasses[]="forum";
$dbClasses[]="topic";
$dbClasses[]="message";
$dbClasses[]="group";
$dbClasses[]="groupmember";
$dbClasses[]="globalstat";
$dbClasses[]="ubb";
$dbClasses[]="smiley";
$dbClasses[]="subscribe";
$dbClasses[]="blacklist";
$dbClasses[]="poll";
$dbClasses[]="attach";
$dbClasses[]="settings";
$dbClasses[]="globalsettings";

$allowedMethods["treeorganiser_form"]='$base->treeOrganiserForm($s);';
$allowedMethods["treeorganiser_form_invalid"]='$base->treeOrganiserForm($s,FALSE);';
$allowedMethods["treeorganiser"]='$base->treeOrganiserServ($s);';
$allowedMethods["movetopic_form"]='$base->moveForm($s);';
$allowedMethods["movetopic"]='$base->move($s);';
$allowedMethods["search_form"]='$base->searchForm($s);';
$allowedMethods["showsubs"]='showUserSubscriptions($s);';
$allowedMethods["showsubs_form"]='showUserSubscriptions($s);';
$allowedMethods["showattach"]='showAttach();';
$allowedMethods["userfunctions"]='$s=$base->showUserFunctions();';
$allowedMethods["lists"]='$s=$base->showListsMenu();';
$allowedMethods["adminfunc"]='$s=$base->showAdminFunctions();';
$allowedMethods["markread"]='markRead();';

// Az applikaciospecifikus menupontok:
define("Init_userfunc",100);
define("Init_lists",101);
define("Init_dsearch",102);
define("Init_adminfunc",103);

define("ForumView_tree", 0);
define("ForumView_flat", 1);

define("Group_None", 0);
define("Group_All", 1);
define("Group_OnlyAdmin", 2);
define("Group_OnlyAdminAndMod", 3);

define("MessCode_no", 0);
define("MessCode_html", 1);
define("MessCode_ubb", 2);

define("subs_all_new_mess",0x1);//1
define("subs_all_new_topic",0x2);//2
define("subs_forum_new_mess",0x4);//4
define("subs_forum_new_topic",0x8);//8
define("subs_topic_new_mess",0x10);//16
define("subs_user_new_mess",0x20);//32
define("subs_user_new_topic",0x40);//64
define("poll_vote",0x10000);//65536

define("user_online_time",600);//ten minutes

?>
