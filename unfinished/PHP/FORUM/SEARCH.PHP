<?php

define("MaxSearchResultNum", 100);

$search_typ =  
    array(
        "attributes"=>array(   
            "words"=>array(
                "type"=>"VARCHAR",
                "text",
                "min"=>"3",
                "max" =>"255",
            ),
            "operator"=>array(
                "type"  =>"INT",
                "values"=>array( "AND", "OR" ),
                "default"=>"AND",
                "selection",
            ),
            "where"=>array(
                "type"  =>"INT",
                "values"=>array( "message","subject"  ),
                "default"=>"message",
                "selection",
            ),
            "forumId"=>array(
                "type"=>"INT",
                "selection",
                //"classselection",
                //"class"=>"forum",
                "labelAttr"=>"name"
            ),
            "userName"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"32",
            ),
            "sortCriteria"=>array(
                "type"  =>"INT",
                "values"=>array( "creationtime_DESC","creationtime_ASC", 
                                 "creatorName_ASC", "creatorName_DESC",
                                 "subject_ASC", "subject_DESC",                                  
                                 "topicsubject_ASC","topicsubject_DESC",
                                 "forumname_ASC", "forumname_DESC"),
                "selection",
            ),
            "resultNum"=>array(
                "type"  =>"INT",
                "values"=>array( "50", "25", "100" ),
                "selection",
            ),
            "matches"=>array(
                "type"  =>"INT",
                "values"=>array( "all", "onePerTopic" ),
                "selection",
            )
        ),
        "submit"=>array("back", "ok")
    );

class Search extends Object
{

    function searchForm(&$s)
    {
        global $lll,$infoText;
        global $search_typ;
        global $gorumroll, $applName;
        
        $s="";
        $forums = new Forum;
        $query="SELECT id, name FROM $applName"."_forum ORDER BY treeidx";
        $ret=loadObjectsSQL( $forums,$query,$forums);
        if ($ret==not_found_in_db) {
            $infoText = $lll["noforumToSearch"];
            return ok;
        }
        global $readAccessArr;
        $ret=readAccessForums();
        if( $ret ) {
            return $ret;
        } 
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
        $search_typ["attributes"]["forumId"]["values"][0] = 0;
        foreach( $forums as $forum )
        {
            $search_typ["attributes"]["forumId"]["values"][$forum->id]= 
                $forum->id;
            $lll["search_forumId_$forum->id"]=$forum->name;
        }  
        $gorumroll->method = "showhtmllist_form";
        generForm($this, $s1);
        $s.=$s1;
    }

    function loadHtmlList(&$list)
    {   
        global $message_typ, $userClassName;
        global $whatHappened,$infoText,$lll,$gorumroll,$gorumuser;
        global $search, $applName;
        
        if( !$search->words )
        {
            $gorumroll->method = "search";
            $whatHappened = "invalid_form";
            $infoText=$lll["invalidWordLength"];
            return ok;
        }
        $words = split(" ",$search->words);
        for( $i=0; isset($words[$i]); $i++ )
        {
            $words[$i] = trim($words[$i]);
            if( !$words[$i] ) unset($words[$i]);
            elseif( strlen($words[$i])<3 )
            {
                $gorumroll->method = "search";
                $whatHappened = "invalid_form";
                $infoText=$lll["invalidWordLength"];
                return ok;
            }
        }
        $message_typ["attributes"]["forumname"][]="list";
        $message_typ["attributes"]["topicsubject"][]="list";
        $message_typ["attributes"]["subject"][]="list";
        $message_typ["attributes"]["creationtime"][]="list";
        unset($message_typ["attributes"]["txt"][0]);
        $message_typ[]="notools";
        $query = "SELECT m.id AS id, m.pid AS pid, m.tid AS tid,".
                 "m.subject AS subject,".
                 " m.creationtime AS creationtime,u.name AS creatorName".
                 ",f.name AS forumname, t.subject AS topicsubject,".
                 " m.ownerId AS ownerId".
                 " FROM $applName"."_message m, $applName"."_forum f,".
                 " $applName"."_topic AS t, $applName"."_$userClassName AS u".
                 " WHERE m.pid=f.id AND m.tid=t.id AND u.id=m.ownerId AND";
        $query.=" (";
        $first=TRUE;
        foreach( $words as $word ) {
            if( !$first ) $query.=" ".$search->operator." ";
            $first = FALSE;
            $word = addcslashes($word,"'\\");
            $query.="(m.subject LIKE '%".$word."%'";
            if( $search->where=="message" )
            {
                $query.=" OR txt LIKE '%".$word."%'";
            }
            $query.=")";
        }
        $query.=")";
        if ($search->userName!="") {
            $query.=" AND m.creatorName='".addslashes($search->userName)."'";
        }
        if ($search->forumId) {
            $query.=" AND m.pid=$search->forumId";
        }
        $slices = explode("_", $search->sortCriteria);
        $attrName = $slices[0];
        $direction = $slices[1];
        $query.=" ORDER BY $attrName $direction";
        if( $attrName!="creationtime" )
        {
            $query.=", creationtime DESC";
        }
        $rn = min($search->resultNum, MaxSearchResultNum);
        $query .= " LIMIT $rn";
        $message = new Message;
        $ret = loadObjectsSQL($message, $query, $list);
        if ($ret!=ok) 
        {
            return ok;
        }
        if( $search->matches=="onePerTopic" )   
        {
            $topicList = array();
            $newList = array();
            for($i=0;isset($list[$i]);$i++)
            {
                //TODO: a nyilvanos forumokat (0 group) is !!!
                if( !in_array($list[$i]->tid, $topicList) )
                {
                    $topicList[] = $list[$i]->tid;
                    $newList[] = $list[$i];
                }
                //else unset($list[$i]);
            }
            $list=$newList;
        }
        global $readAccessArr;
        $ret=readAccessForums();
        if( $ret ) {
            return $ret;
        } 
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
        return ok;
    }

    function showHtmlList(&$s)
    {
        global $whatHappened, $infoText;
        global $lll;
        global $search_typ;
        global $gorumroll;
        global $search;
        if( !isset($search) )
        {
            $search = new Search;
            $gorumroll->method = "valami mas";
            initClassVars($search, TRUE);
            $gorumroll->method = "showhtmllist";
        }    
        if( $gorumroll->submit==$lll["back"] )
        {   
            $whatHappened = "form_submitted";
            $infoText = $lll["operation_cancelled"];
            if( $gorumroll->fromlist=="search" ) $gorumroll->submit=$lll["ok"];
            return ok;
        }
        $s="";
        $this->loadHtmlList($list, $search);
        if( $whatHappened=="invalid_form" )
        {
            return ok;
        }
        $s.=generBoxUp();
        global $colspNum;
        $colspNum=5;
        $s.= $this->showListHeader();  
        foreach( $list as $listItem ) {
            $listItem->showOneRow(0,$s1);
            $s.=$s1;
        }
        $s.=generBoxDown();
        //$s.="<br>";
        
        if (!isset($list[0])) {
            $s.="<center><span class='cell'>$lll[emptylist]</span>".
                "</center>\n";
        }
        return ok;
    }

    function & getTypeInfo()
    {
        global $gorumroll;
        
        if( $gorumroll->method=="showhtmllist" ) $class="message";
        else $class="search";
        $typ = $class."_typ";
        global ${$typ};
        return ${$typ};
    }
    
    function showSortTool(&$s)
    {
        $s = "";
    }
}
?>
