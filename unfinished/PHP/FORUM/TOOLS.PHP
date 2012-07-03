<?php

function showForumJump( $currentPid)
{
    global $lll,$gorumroll, $applName;

    $s="";
    $forums = new Forum;
    $query="SELECT * FROM $applName"."_forum WHERE iscat=0".
           " ORDER BY treeidx";
    $ret=loadObjectsSQL( $forums,$query,$forums);
    if ($ret==not_found_in_db) {
        return $s;
    }
    global $readAccessArr;
    readAccessForums();
    hasAdminRights($isAdm);
    if( !$isAdm )   
    {
        $newList = array();
        foreach( $forums as $forum )
        {
            if (isset($readAccessArr[$forum->id]))
            {
                $newList[] = $forum;
            }
        }
        $forums=$newList;
    }
    $tempRoll = $gorumroll;
    $tempRoll->list="topic";
    $tempRoll->method="showhtmllist";
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    $s.="<table border='0'>\n";
    $len=40;
    foreach($forums as $forum) {
        if (strlen($forum->name)>$len) {
            $fn=substr($forum->name,0,$len)."...";
        }
        else $fn=$forum->name;
        $labels[$forum->id]=htmlspecialchars($fn);
        $vals[$forum->id]=$forum->id;
    }
    $s.=generSelectField("rollid",$lll["forumJump"]." : ","",$labels,
                        $vals,$currentPid,0,"jumpbg","jumperTool",FALSE,
                        TRUE,"jumper");
    $s.="</table>\n";
    $s.="</form>\n";
    return $s;
}

function showTopicJump( $currentPid, $currentTid)
{
    global $lll,$gorumroll, $applName;
    
    $s="";
    $topics = new Topic;
    $query="SELECT * FROM $applName"."_topic".
           " WHERE pid=$currentPid".
           " ORDER BY creationtime DESC LIMIT 15";//TODO: 15?
    $ret=loadObjectsSQL( $topics,$query,$topics);
    if ($ret==not_found_in_db) {
        return $s;
    }
    $tempRoll = $gorumroll;
    $tempRoll->class="message";
    $tempRoll->method="showhtmllist";
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    $s.="<table border='0'>\n";
    $len=40;
    foreach($topics as $topic) {
        if (strlen($topic->subject)>$len) {
            $ts=substr($topic->subject,0,$len)."...";
        }
        else $ts=$topic->subject;
        $labels[$topic->id]=htmlspecialchars($ts);
        $vals[$topic->id]="$currentPid,$topic->id";
    }
    $s.=generSelectField("rollid",$lll["topicJump"]." : ","",$labels,
                        $vals,"$currentPid,$currentTid",0,
                        "jumpbg","jumperTool",FALSE,TRUE,"jumper");
    $s.="</table>\n";
    $s.="</form>\n";
    return $s;
}   
?>
