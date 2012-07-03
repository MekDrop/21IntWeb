<?php
$poll_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "min" =>"0",
                "auto increment",
                "form hidden"
            ),
            "pid"=>array(
                "type"=>"INT",
                "form hidden"
            ),
            "tid"=>array(
                "type"=>"INT",
                "form hidden"
            ),
            "question"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
                "min" =>"1",
                "mandatory",
                "list",
                "sorta"
            ),
            "q1"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
                "min" =>"1",
                "mandatory",
            ),
            "q2"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
                "min" =>"1",
                "mandatory",
            ),
            "q3"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q4"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q5"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q6"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q7"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q8"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q9"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "q10"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "creationtime"=>array(
                "type"=>"INT",
                "form invisible",
                "list",
                "sorta"
            ),            
            "voted"=>array(
                "type"=>"INT",
                "form invisible",
            ),           
            "ownerId"=>array(
                "type"=>"INT",
                "form invisible",
            )           
        ),
        "primary_key"=>array( "id"),
        "sort_criteria_attr"=>"question",
        "sort_criteria_dir"=>"a"        
    );
class Poll extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        //TODO: Ezt meg egyszer at lehetne nezni!
        global $gorumrecognised, $gorumauthlevel, $gorumuser,$gorumroll;
        global $allowAnonymPost;
        global $generalRight;
        $hasRight = FALSE;
        $generalRight = FALSE;
        $t = new Topic;
        $t->pid=$this->pid;
        $t->cacheFatherObject("forum");
        //TODO: ha notfound akkor die ? altalanosan megnezni.
        global $fatherForum;
        hasAdminRights($isAdm);
        if( $isAdm )
        {
            $readRight=TRUE;
            $writeRight=TRUE;
        }
        else
        {
            global $readAccessArr;
            if ($fatherForum && $fatherForum->readgroup ) {
                readAccessForums();
                $readRight = isset($readAccessArr[$fatherForum->id]);
            }
            else $readRight=TRUE;
            global $writeAccessArr;
            if ($fatherForum && $fatherForum->writegroup) {
                writeAccessForums();
                $writeRight = isset($writeAccessArr[$fatherForum->id]);
            }
            else $writeRight=TRUE;
        }
        if( $method==Priv_load ) 
        {
            $hasRight=$readRight;
            $generalRight = TRUE;
        }
        elseif( $gorumauthlevel==Loginlib_NewUser ) {
            $hasRight=FALSE;
            $generalRight = TRUE;
        }  
        elseif( $gorumauthlevel==Loginlib_GuestLevel && 
                $method==Priv_insert )
        {
            $hasRight=TRUE;
            $generalRight = FALSE;
        }        
        elseif( $gorumauthlevel>Loginlib_GuestLevel && 
                !$gorumrecognised )
        {
            $hasRight=FALSE;
            $generalRight = TRUE;
        }        
        else
        {
            //TODO: megiscsak kell ide a kavaras,
            //hogy ha mar van szavazat, nem lehet modositani,
            //vagy valami ilyesmi
            //csak moderator vagy admin.
            if( $method==Priv_create )
            {
                $hasRight=$writeRight;
                $generalRight = ($gorumroll->class=="topic");
            }
            elseif( $isAdm )
            {
                $hasRight=TRUE;
                $generalRight = ($gorumroll->class=="topic");
            }
            elseif( $gorumuser->isMod && $this->pid!=0)//TODO: megcsin.
            {
                if( $fatherForum )
                {
                    $moderators=split( ", *", $fatherForum->moderator );
                    if( in_array($gorumuser->id, $moderators) )
                    {
                      $hasRight=TRUE;
                      $generalRight=FALSE;
                    }    
                }
            }            
            if( !$hasRight && isset($this->ownerId) && 
                $this->ownerId==$gorumuser->id && $this->voted==0)
            {
                $generalRight = FALSE;
                $hasRight=$writeRight;
            }
            // Votolasi jog:
            elseif( $method==Priv_insert )
            {
                $generalRight = FALSE;
                $hasRight=$writeRight;
            }
        }
        if( !$hasRight && $giveError ) {
            handleError($lll["permission_denied"]);
        }        
        return ok;
    } 
    
    /*
    function loadHtmlList(&$list)
    {
        global $gorumroll,$fromforummessageview,$gorumuser;
        loadHtmlList($this,$list);
        global $readAccessArr;
        readAccessForums();
        hasAdminRights($isAdm); 
        if( !$isAdm )   
        {
            $newList = array();
            for($i=0;isset($list[$i]);$i++)
            {
                if( !$list[$i]->pid || 
                    isset($readAccessArr[$list[$i]->pid]) )
                {
                    $newList[] = $list[$i];
                }
            }
            $list=$newList;
        }    
        return ok;     
    }
    */

    /*
    function showListVal($attr)
    {
        global $lll,$gorumuser,$gorumroll;
        global $htmlListForum;

        $s="";
        if( $attr=="question" ) {
            global $subsInfo,$gorumrecognised, $gorumauthlevel;
            $tempRoll = $gorumroll;
            $tempRoll->class="poll";
            $tempRoll->rollid=0;
            if (isset($subsInfo["poll_vote"][$this->id]) ||
                !($gorumrecognised || 
                  $gorumauthlevel==Loginlib_GuestLevel)
               ) 
            {
                $tempRoll->method="showpoll";
            }
            else
            {
                $tempRoll->method="vote_form";
            }    
            $tempRoll->id = $this->id;
            $s.=$tempRoll->generAnchor(htmlspecialchars($this->{$attr}),
                                       "listItem");
            return $s;
        }        
        elseif( $attr=="creationtime" )
        {
            $s=showTimestamp($this->{$attr});
        }
        else
        {
            $s=htmlspecialchars($this->{$attr});
        }
        return $s;
    } 
    */
    function showOnlyPoll()
    {
        global $whatHappened,$infoText,$lll,$gorumroll,$xi, $applName;

        $s="";
        $vote = new Subscribe;
        $vote->type=poll_vote;
        $vote->objid=$this->id;
        $query="SELECT userid,info FROM $applName"."_subscribe".
               " WHERE type=".poll_vote." AND objid=$this->id";
        $result=executeQuery($query);
        $num=mysql_num_rows($result);
        for($i=1;$i<=10;$i++) {
            $votes[$i]=0;
        }
        for($i=1;$i<=$num;$i++) {
            $row=mysql_fetch_array($result);
            $votes[$row["info"]]++;
        }
        if( $gorumroll->class=="poll" ) $s.=$this->showNavBar();
        //$s.=vertSpacer()."\n";
        $s.=generBoxUp();        
        $s.="<tr><th colspan='4' class='header'>$this->question";
        global $subsInfo;
        if (isset($subsInfo["poll_vote"][$this->id])) {
            $s.="<br>".$lll["voted"];
        }
        $s.="</th></tr>\n";
        for($i=1;$i<=10;$i++) {
            $m="q".$i;
            if ($this->{$m}=="") break;
            $v = sprintf ("%01.2f",
                          $num ? ((float)($votes[$i]/$num)*100) : 0);
            $vi = (int)$v*3;
            $s.="<tr><td class='cell'>".$this->{$m}."</td>\n";
            $s.="<td class='cell' width='300'>".
                "<img src='$xi/vote$i.gif' height='10' width='$vi'>".
                "</td>\n";
            $s.="<td class='cell'>".$votes[$i]."</td>\n";
            $s.="<td class='cell'>$v%</td></tr>\n";
        }
        $s.="<tr><th colspan='4' class='header'>".
            "$num ".$lll["votes"]."</th></tr>\n";
        $s.=generBoxDown();        
        return $s;
    }
    function showPoll()
    {
        global $whatHappened,$infoText,$lll,$gorumroll;

        $s="";
        if (isset($this->id)&&$this->id==0&&isset($gorumroll->rollid)) {
            $this->id=$gorumroll->rollid;
        }
        $this->load();
        //TODO: lehet, hogy a not found itt nem baj! ?
        $s.=$this->showOnlyPoll();
        return $s;
    }
    function voteOnlyForm()
    {
        global $whatHappened,$infoText,$lll,$gorumroll,$scriptName;

        $s="";
        $tempRoll = $gorumroll;
        $tempRoll->list = "subscribe";   
        $tempRoll->method = "create";   
        $tempRoll->rollid = 0;
        $tempRoll->type = poll_vote;
        $tempRoll->objid = $this->id;
        saveInFrom($tempRoll);
        if( $gorumroll->class=="poll" ) $s.=$this->showNavBar();
        //$s.=vertSpacer()."\n";
        $s.=$tempRoll->generFormHeaderAndHiddenFields();            
        $s.=generBoxUp();        
        $s.="<tr><th colspan='2' class='header'>$this->question";
        $s.="</th></tr>\n";
        for($i=1;$i<=10;$i++) {
            $m="q".$i;
            if ($this->{$m}=="") break;
            $s.="<tr><td class='cell'>";
            $s.="<input type='radio' name='info' value='$i'></td>\n";
            $s.="<td class='cell'>".$this->{$m}."</td></tr>\n";
        }
        $s.="<tr><th class='header'><input type='submit' value='".
        $lll["vote"]."' name='submit' class='button'></th>\n";

        $tempRoll = $gorumroll;
        $tempRoll->list="message";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid="$this->pid,$this->tid";
        $tempRoll->viewpoll=1;
        $s.="<th class='header'>";
        $s.=$tempRoll->generAnchor($lll["viewres"],"header");
        $s.="</th></tr>\n";
        $s.=generBoxDown();        
        $s.="</form>\n";

        return $s;
    }
    function voteForm()
    {
        global $whatHappened,$infoText,$lll;

        $s="";
        $this->load();
        //TODO: lehet, hogy a not found itt nem baj! ?
        // lekerdezzuk a votolasi jogot:
        $this->hasObjectRights($hasRight, Priv_insert, FALSE);
        // Ha nioncs jog irni, akkor megmutatjuk az eredmenyeket
        if( !$hasRight ) $s.=$this->showOnlyPoll();
        else $s.=$this->voteOnlyForm();
        return $s;
    }
    
    function delete($whereFields="", $overrulePrivilege=FALSE )
    {
        global $whatHappened, $infoText, $lll;
        
        $whatHappened = "form_submitted";
        privilegeDelete( $this,$whereFields, $overrulePrivilege);
        if ($whatHappened=="invalid_form" ) {
            $infoText = $lll["no_such_object"];
            return;
        }        
        // A topichoz tobbe nem tartozik poll:
        if( $this->tid )
        {
            $fatherTopic = new Topic;
            $fatherTopic->id = $this->tid;
            load($fatherTopic);
            $fatherTopic->poll="0";
            modify($fatherTopic);
        }
        // A szavazatokat is ki kell torolni:
        $subscribe = new Subscribe;
        $subscribe->objid = $this->id;
        $subscribe->type = poll_vote;
        delete($subscribe, array("objid", "type"));
    }

}
?>
