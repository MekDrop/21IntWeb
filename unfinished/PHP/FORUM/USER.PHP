<?php
$user_typ["attributes"]["isMod"]=  
            array(
                "type"  =>"INT",
                "default"=>"0",
                "form invisible",
            );
$user_typ["attributes"]["signature"]=  
            array(
                "type"=>"TEXT",
                "rows" => 10,
                "cols" => 50,
                "textarea",
                "remind_password_form: form invisible",
                "login_form: form invisible",
                "change_password_form: form invisible",
            );            
$user_typ["attributes"]["logoutTime"]=  
            array(
                "type"=>"INT",
                "form invisible",
            );
$user_typ["attributes"]["lastClickTime"]=  
            array(
                "type"=>"INT",
                "form invisible",
                "list"
            );
$user_typ["attributes"]["postnum"]=  
            array(
                "type"=>"INT",
                "default"=>"0",                
                "form invisible",
                "list",
                "sorta",
            );
$user_typ["attributes"]["showEmail"]=  
            array(
                "type"=>"INT",
                "default"=>"0",                
                "bool",
                "remind_password_form: form invisible",
                "login_form: form invisible",
                "change_password_form: form invisible"
            );

class ZorumUser extends User
{
    function showDetailsMethods()
    {
        global $subscription,$subsInfo,$lll,$gorumuser;
        global $gorumrecognised, $gorumauthlevel,$allowSubscriptions;
        
        // Ez azert kell, mert lehet, hogy a method itt showuserlink:
        global $gorumroll;
        $gorumroll->method="showdetails";
        $gorumroll->rollid = $this->id;
        
        $s="";
        //----- topics by user BEGIN
        $s.="<tr><td class='cell' colspan='2'>";
        $tempRoll = $gorumroll;
        $tempRoll->list="topic_user";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid="$this->id,$this->name";        
        $s1=sprintf($lll["topics_by_user"],
                    htmlspecialchars($this->name));
        $s.=$tempRoll->generAnchor($s1,"listItem");
        $s.="</td></tr>\n";
        //----- messages by user BEGIN
        $s.="<tr><td class='cell' colspan='2'>";
        $tempRoll->list="message_user";        
        $s1=sprintf($lll["messages_by_user"],
                    htmlspecialchars($this->name));
        $s.=$tempRoll->generAnchor($s1,"listItem");
        $s.="</td></tr>\n";
        if($gorumrecognised && $allowSubscriptions) {//subscription
            //----- subscribe to messages from this user BEGIN
            $s.="<tr><td class='cell' colspan='2'>";
            $tempRoll = $gorumroll;
            $tempRoll->list = "subscribe";   
            $tempRoll->rollid = 0;
            $tempRoll->objid=$this->id;
            $tempRoll->userid = $gorumuser->id;            
            $tempRoll->type=subs_user_new_mess;
            if ($subscription&subs_user_new_mess &&
                isset($subsInfo["usertomess"][$this->id]))
            {
                $tempRoll->method = "delete";
                $s1=sprintf($lll["unsubs_mess_by_user"],
                        htmlspecialchars($this->name));
            }
            else 
            {   
                $tempRoll->method = "create";
                $s1=sprintf($lll["subs_mess_by_user"],
                        htmlspecialchars($this->name));
            }
            saveInFrom($tempRoll);
            $s.=$tempRoll->generAnchor($s1,"listItem");
            $s.="</td></tr>\n";
            //----- subscribe to topics from this user BEGIN
            $s.="<tr><td class='cell' colspan='2'>";
            $tempRoll->type=subs_user_new_topic;
            if ($subscription&subs_user_new_topic &&
                isset($subsInfo["usertotopic"][$this->id]))
            {
                $tempRoll->method = "delete";
                $s1=sprintf($lll["unsubs_topic_by_user"],
                        htmlspecialchars($this->name));
            }
            else 
            {
                $tempRoll->method = "create";
                $s1=sprintf($lll["subs_topic_by_user"],
                        htmlspecialchars($this->name));
            }    
            $s.=$tempRoll->generAnchor($s1,"listItem");
            $s.="</td></tr>\n";
        }
        $s.=User::showDetailsMethods();
        return $s;
    } 
    function showListVal($attr)
    {
        global $lll,$gorumuser,$gorumrecognised;
        $s="";
        if ($attr=="creationtime" || $attr=="lastVisited" || 
            $attr=="currentVisit" || $attr=="lastClickTime") 
        {    
            if (isset($this->{$attr})) $s=showTimestamp($this->{$attr});
            else $s=$lll["never"];
        } 
        elseif( $attr=="email" )
        {
            hasAdminRights($isAdm);
            if( $isAdm || $this->showEmail || 
                ($gorumrecognised && $gorumuser->id==$this->id)) {

                $m=htmlspecialchars($this->{$attr});
                $s="<a href='mailto:$m'>$m</a>";
            }
            else
            {   
                global $lll; 
                $s=$lll["privateEmail"];
            }            
        }        
        else $s=User::showListVal($attr);
        return $s;        
    }
    function create($overrulePrivilege, &$s)
    {
         global $whatHappened, $infoText,$zorumglobalstat;
         User::create($overrulePrivilege,$s);
         if( $whatHappened=="invalid_form") {
             return;
         }
         $zorumglobalstat->usernum++;
         modify($zorumglobalstat);
         
    }
 
}
?>
