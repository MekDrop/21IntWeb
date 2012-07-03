<?php

function insertSpanClass($s, $spanCl)
{
    $search = "'(^|>)(\s*[^\s<][^<]*)(<|$)'";  
    $replace = "\\1<span class='$spanCl'>\\2</span>\\3";
    $result = preg_replace ($search, $replace, $s);
    return $result;
}

function insertTdClass($s, $tdCl)
{
    $search = "<td";  
    $replace = "<td class='$tdCl'";
    $result = str_replace ($search, $replace, $s);
    return $result;
}
function readAccessForums()
{
    global $gorumuser,$readAccessArr,$readAccessCached, $applName;
    if (isset($readAccessCached)) {
        return ok;
    }
    $query="SELECT f.id FROM $applName"."_forum f,".
           " $applName"."_groupmember gm".
           " WHERE readgroup=groupId".
           " AND gm.userId=$gorumuser->id";
    $result=executeQuery($query);
    $num=mysql_num_rows($result);
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_row($result);
        $readAccessArr[$row[0]]=1;
    }
    $readAccessCached=TRUE;
    global $publicAccess;
    if (!isset($publicAccess)) {
        publicAccessForums();
    }
    return ok;
}
function writeAccessForums()
{
    global $gorumuser,$writeAccessArr,$writeAccessCached, $applName;
    if (isset($writeAccessCached)) {
        return ok;
    }
    $query="SELECT f.id FROM $applName"."_forum f,".
           " $applName"."_groupmember gm".
           " WHERE writegroup=groupId".
           " AND gm.userId=$gorumuser->id";
    $result=executeQuery($query);
    $num=mysql_num_rows($result);
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_row($result);
        $writeAccessArr[$row[0]]=1;
    }
    $writeAccessCached=TRUE;
    global $publicAccess;
    if (!isset($publicAccess)) {
        $ret=publicAccessForums();
    }
    return ok;
}
function topicAccessForums()
{
    global $gorumuser,$topicAccessArr,$topicAccessCached, $applName;
    if (isset($topicAccessCached)) {
        return ok;
    }
    $query="SELECT f.id FROM $applName"."_forum f,".
           " $applName"."_groupmember gm".
           " WHERE topicgroup=groupId".
           " AND gm.userId=$gorumuser->id";
    $result=executeQuery($query);
    $num=mysql_num_rows($result);
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_row($result);
        $topicAccessArr[$row[0]]=1;
    }
    $topicAccessCached=TRUE;
    global $publicAccess;
    if (!isset($publicAccess)) {
        $ret=publicAccessForums();
    }
    return ok;
}
function publicAccessForums()
{
    global $publicAccess,$writeAccessArr,$readAccessArr,$topicAccessArr, $applName;
    $query="SELECT id,readgroup,writegroup,topicgroup FROM $applName"."_forum".
           " WHERE writegroup=1 OR readgroup=1 OR topicgroup=1";
    $result=executeQuery($query);
    $num=mysql_num_rows($result);
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_array($result);
        if ($row["writegroup"]==1) $writeAccessArr[$row["id"]]=1;
        if ($row["readgroup"]==1) $readAccessArr[$row["id"]]=1;
        if ($row["topicgroup"]==1) $topicAccessArr[$row["id"]]=1;
    }
    $publicAccess=TRUE;
    return ok;
}
function hasModRights()
{
    global $isModByPid,$gorumuser, $applName;
    if (isset($isModByPid)) {
        return ok;
    }
    $isModByPid = array();
    $query="SELECT id,moderator FROM $applName"."_forum WHERE iscat=0".
           " AND moderator!=''";
    $forum = new Forum;
    loadObjectsSQL( $forum,$query,$forums);
    for($i=0;isset($forums[$i]);$i++) {
        $modArr=explode(",",$forums[$i]->moderator);
        if (in_array($gorumuser->id,$modArr)) {
            $isModByPid[$forums[$i]->id]=TRUE;
        }
    }
}
function loadMods()
{
    global $modNameById,$gorumuser, $applName, $userClassName;
    if (isset($modNameById)) {
        return ok;
    }
    $modNameById = array();
    $query="SELECT id,name FROM $applName"."_$userClassName WHERE isMod=1";
    $result=executeQuery($query);
    $num=mysql_num_rows($result);
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_array($result);
        $modNameById[$row["id"]]=$row["name"];
    }
}
function markRead()
{
    global $gorumuser,$whatHappened,$infoText,$lll;
    
    $gorumuser->logoutTime = $gorumuser->lastClickTime;
    modify($gorumuser);
    $whatHappened = "form_submitted";
    $infoText = $lll["markedread"];
}
?>
