<?php
//TODO:

mysql_connect("localhost","root","");
$zDbName="migzorum";
$gDbName="nagydorum";

$ret=mysql_select_db($gDbName);
$q="DELETE FROM zorum_forum";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_topic";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_zorumuser";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_message";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_attach";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_globalstat";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_poll";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_reflog";
$gresult=mysql_query($q);
$q="DELETE FROM zorum_subscribe";
$gresult=mysql_query($q);

//globalstat
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM forumglobalstat_zorum";
$zresult=mysql_query($q);

$ret=mysql_select_db($gDbName);

$row=mysql_fetch_array($zresult);

$q="INSERT INTO zorum_globalstat SET id=$row[id],".
    "instver='$row[instver]',forumnum='$row[forumnum]',".
    "topicnum=$row[topicnum],entrynum='$row[entrynum]',".
    "usernum=$row[usernum]";
$gresult=mysql_query($q);
if (!$gresult) {
    die(mysql_error());
}
//polls
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM poll_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);
    $row["question"] = addcslashes($row["question"],"'\\");
    $row["q1"] = addcslashes($row["q1"],"'\\");
    $row["q2"] = addcslashes($row["q2"],"'\\");
    $row["q3"] = addcslashes($row["q3"],"'\\");
    $row["q4"] = addcslashes($row["q4"],"'\\");
    $row["q5"] = addcslashes($row["q5"],"'\\");
    $row["q6"] = addcslashes($row["q6"],"'\\");
    $row["q7"] = addcslashes($row["q7"],"'\\");
    $row["q8"] = addcslashes($row["q8"],"'\\");
    $row["q9"] = addcslashes($row["q9"],"'\\");
    $row["q10"] = addcslashes($row["q10"],"'\\");

    $q="INSERT INTO zorum_poll SET id=$row[id],".
        "pid=$row[pid],question='$row[question]',tid=$row[tid],".
        "q1='$row[q1]',q2='$row[q2]',".
        "q3='$row[q3]',q4='$row[q4]',".
        "q5='$row[q5]',q6='$row[q6]',".
        "q7='$row[q7]',q8='$row[q8]',".
        "q9='$row[q9]',".
        "q10='$row[q10]',creationtime='$row[creationtime]',".
        "voted=$row[voted],ownerId='$row[ownerId]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error());
    }
}
//poll results in subscribe
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM subscribe_zorum WHERE type=65536";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    $q="INSERT INTO zorum_subscribe SET id=$row[id],".
        "type=$row[type],userid='$row[userid]',objid=$row[classid],".
        "info='$row[sent]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error());
    }
}
//forums
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM forum_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    if ($row["hasChild"]=="yes") $row["hasChild"]=1;
    else $row["hasChild"]=0;
    $q="INSERT INTO zorum_forum SET id=$row[id],".
        "up=$row[up],name='$row[name]',treeidx=$row[treeidx],".
        "treedepth=$row[treedepth],description='$row[description]',".
        "hasChild=$row[hasChild],topicnum='$row[topicnum]',".
        "postnum=$row[postnum],creationtime='$row[creationtime]',".
        "lastpost=$row[lastpost],ownerId='$row[ownerId]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error());
    }
}
//topics
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM forumtopic_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    if ($row["poll"]=="yes") $row["poll"]=1;
    else $row["poll"]=0;
    if ($row["smiley"]=="yes") $row["smiley"]=1;
    else $row["smiley"]=0;
    if ($row["coding"]=="no") $row["coding"]=0;
    elseif ($row["coding"]=="HTML") $row["coding"]=1;
    else $row["coding"]=2;
    $row["subject"] = addcslashes($row["subject"],"'\\");
    $q="INSERT INTO zorum_topic SET id=$row[id],".
        "pid=$row[pid],subject='$row[subject]',postnum=$row[postnum],".
        "viewnum=$row[viewnum],creationtime='$row[creationtime]',".
        "lastpost=$row[lastpost],prio='$row[prio]',".
        "poll=$row[poll],smiley='$row[smiley]',".
        "coding=$row[coding],ownerId='$row[ownerId]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error()." $q");
    }
}
//messages
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM forummessage_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    if ($row["smiley"]=="yes") $row["smiley"]=1;
    else $row["smiley"]=0;
    if ($row["coding"]=="no") $row["coding"]=0;
    elseif ($row["coding"]=="HTML") $row["coding"]=1;
    else $row["coding"]=2;
    $row["subject"] = addcslashes($row["subject"],"'\\");
    $row["txt"] = addcslashes($row["txt"],"'\\");
    $q="INSERT INTO zorum_message SET id=$row[id],".
        "pid=$row[pid],tid='$row[tid]',subject='$row[subject]',".
        "txt='$row[txt]',iplog='$row[iplog]',".
        "creationtime=$row[creationtime],attsize='$row[attsize]',".
        "att_file_upload='$row[att_file_upload]',".
        "smiley=$row[smiley],downloaded='$row[downloaded]',".
        "coding=$row[coding],ownerId='$row[ownerId]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error()." $q");
    }
}
//attach
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM attach_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    $row["file"] = addcslashes($row["file"],"'\\");
    $q="INSERT INTO zorum_attach SET id=$row[id],".
        "file='$row[file]',mime='$row[mime]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error()." $q");
    }
}
//users
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM user_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    if ($row["isAdm"]=="yes") $row["isAdm"]=1;
    else $row["isAdm"]=0;
    if ($row["showEmail"]=="yes") $row["showEmail"]=1;
    else $row["showEmail"]=0;
    $row["name"] = addcslashes($row["name"],"'\\");
    $row["notes"] = addcslashes($row["notes"],"'\\");
    $row["signature"] = addcslashes($row["signature"],"'\\");
    if (!isset($row["lastClickTime"])) $row["lastClickTime"]=0;
    if (!isset($row["logoutTime"])) $row["logoutTime"]=0;
    $q="INSERT INTO zorum_zorumuser SET id=$row[id],".
        "name='$row[name]',email='$row[email]',".
        "password='$row[password]',notes='$row[notes]',".
        "isAdm=$row[isAdm],creationtime='$row[creationtime]',".
        "lastClickTime=$row[lastClickTime],".
        "postnum=$row[postnum],".
        "showEmail=$row[showEmail],".
        "logoutTime=$row[logoutTime],signature='$row[signature]'";
    $gresult=mysql_query($q);
    /*
    if (!$gresult) {
        die(mysql_error()." $q");
    }
    */
}
//reflog
$ret=mysql_select_db($zDbName);
$q="SELECT * FROM reflog_zorum";
$zresult=mysql_query($q);
$num=mysql_num_rows($zresult);

$ret=mysql_select_db($gDbName);


for($i=0;$i<$num;$i++) {
    $row=mysql_fetch_array($zresult);

    $q="INSERT INTO zorum_reflog SET id=$row[id],".
        "http_referer='$row[http_referer]',".
        "referer_site='$row[referer_site]',userid='$row[userid]',".
        "creationtime='$row[creationtime]'";
    $gresult=mysql_query($q);
    if (!$gresult) {
        die(mysql_error()." $q");
    }
}

echo "<br>END";

?>
