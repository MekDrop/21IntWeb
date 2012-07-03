<?php
$secmenu_typ =  array("attributes"=>array());
class SecMenu extends Object
{
function showUserFunctions()
{
    global $lll;
    $menu = $this->makeUserFunctions();
    $this->showSecMenu($lll["userfunc"],$menu,$s);
    return $s;
}
function showListsMenu()
{
    global $lll;
    $menu = $this->makeListsMenu();
    $this->showSecMenu($lll["difflists"],$menu,$s);
    return $s;
}
function showAdminFunctions()
{
    global $lll;
    $menu = $this->makeAdminFunctions();
    $this->showSecMenu($lll["adminfunc"],$menu,$s);
    return $s;
}
function makeUserFunctions()
{
    global $gorumuser,$lll,$gorumrecognised;
    global $gorumroll, $userClassName;
    
    $menu = array();
    $i=0;
    $tempRoll = $gorumroll;
    $tempRoll->list = $userClassName;
    $tempRoll->method = "markread";   
    $tempRoll->rollid = 0;
    saveInFrom($tempRoll);
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["markread"]);
    $menu[$i]["expl"]=$lll["markread_menuexpl"];
    $i++;

    if( $gorumrecognised) {   
       $tempRoll = $gorumroll;
       $tempRoll->list = "settings";   
       $tempRoll->method = "modify_form";   
       $tempRoll->rollid = 0;
       saveInFrom($tempRoll);
       $menu[$i]["menu"]=$tempRoll->generAnchor($lll["customizeSettings"]);
       $menu[$i]["expl"]=$lll["customizeSettings_menuexpl"];
       $i++;
       
       $tempRoll = $gorumroll;
       $tempRoll->list = $userClassName;
       $tempRoll->method = "showsubs";
       $tempRoll->rollid = 0;        
       $menu[$i]["menu"]=$tempRoll->generAnchor($lll["subscriptions"]);
       $menu[$i]["expl"]=$lll["subscriptions_menuexpl"];
       $i++;

       $tempRoll = $gorumroll;
       $tempRoll->list = $userClassName;
       $tempRoll->method = "change_password_form";   
       $tempRoll->id = $gorumuser->id;
       $tempRoll->rollid = 0;
       saveInFrom($tempRoll);
       $menu[$i]["menu"]=$tempRoll->generAnchor($lll["changePassword"]);
       $menu[$i]["expl"]=$lll["changePassword_menuexpl"];
       $i++;
       
       $tempRoll = $gorumroll;
       $tempRoll->list = $userClassName;
       $tempRoll->method = "modify_form";   
       $tempRoll->id = $gorumuser->id;
       $tempRoll->rollid = 0;
       saveInFrom($tempRoll);
       $menu[$i]["menu"]=$tempRoll->generAnchor($lll["userProfile"]);
       $menu[$i]["expl"]=$lll["userProfile_menuexpl"];
       $i++;

    }    

    return $menu;
}
function makeListsMenu()
{
    global $gorumuser,$lll,$gorumrecognised;
    global $gorumroll, $userClassName;
    
    $menu = array();
    $i=0;

    $tempRoll = $gorumroll;
    $tempRoll->list="topic_all";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["all_topics"]);
    $menu[$i]["expl"]=$lll["all_topics_menuexpl"];
    $i++;
    
    $tempRoll = $gorumroll;
    $tempRoll->list="message_all";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["all_messages"]);
    $menu[$i]["expl"]=$lll["all_messages_menuexpl"];
    $i++;
    
    $tempRoll = $gorumroll;
    $tempRoll->list="topic_newt";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["new_topics"]);
    $menu[$i]["expl"]=$lll["new_topics_menuexpl"];
    $i++;
    
    $tempRoll = $gorumroll;
    $tempRoll->list="topic_newm";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor(
                                        $lll["topics_with_newpost"]);
    $menu[$i]["expl"]=$lll["topics_with_newpost_menuexpl"];
    $i++;
    
    $tempRoll = $gorumroll;
    $tempRoll->list="message_new";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["new_messages"]);
    $menu[$i]["expl"]=$lll["new_messages_menuexpl"];
    $i++;
    
    $tempRoll = $gorumroll;
    $tempRoll->list=$userClassName;
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["users"]);
    $menu[$i]["expl"]=$lll["users_menuexpl"];
    $i++;
    
    $tempRoll = $gorumroll;
    $tempRoll->list="smiley";
    $tempRoll->method="showhtmllist";
    $tempRoll->rollid=0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["smilies"]);
    $menu[$i]["expl"]=$lll["smilies_menuexpl"];
    $i++;

    global $allowUbbInPost;
    if( $allowUbbInPost ) {
        $tempRoll = $gorumroll;
        $tempRoll->list="ubb";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid=0;
        $menu[$i]["menu"]=$tempRoll->generAnchor($lll["ubbs"]);
        $menu[$i]["expl"]=$lll["ubbs_menuexpl"];
        $i++;
    }
    if( $gorumrecognised && ($gorumuser->isAdm || $gorumuser->isMod) ) {
        $tempRoll = $gorumroll;
        $tempRoll->list="group";
        $tempRoll->method="showhtmllist";
        $tempRoll->rollid=0;
        $menu[$i]["menu"]=$tempRoll->generAnchor($lll["groups"]);
        $menu[$i]["expl"]=$lll["groups_menuexpl"];
        $i++;    
    }
    
    
    return $menu;
}
function makeAdminFunctions()
{
    global $gorumuser,$lll,$gorumrecognised;
    global $gorumroll, $userClassName;
    
    $menu = array();
    if (!$gorumrecognised || !$gorumuser->isAdm) {
        return $menu;
    }
    $i=0;
    $tempRoll = $gorumroll;
    $tempRoll->list = "globalsettings";
    $tempRoll->method = "modify_form";
    $tempRoll->rollid = 0;
    saveInFrom($tempRoll);
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["modGlobalSettings"]);
    $menu[$i]["expl"]=$lll["modGlobalSettings_menuexpl"];
    $i++;

    //TODO: statistics

    $tempRoll = $gorumroll;
    $tempRoll->list = "forum";
    $tempRoll->method = "treeorganiser_form";
    $tempRoll->rollid = 0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["organiseForum"]);
    $menu[$i]["expl"]=$lll["organiseForum_menuexpl"];
    $i++;

    $tempRoll = $gorumroll;
    $tempRoll->list = "blacklist";
    $tempRoll->method = "showhtmllist";
    $tempRoll->rollid = 0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["blacklist_button"]);
    $menu[$i]["expl"]=$lll["blacklist_button_menuexpl"];
    $i++;

    $tempRoll = $gorumroll;
    $tempRoll->list = "badwords";
    $tempRoll->method = "showhtmllist";
    $tempRoll->rollid = 0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["badword_button"]);
    $menu[$i]["expl"]=$lll["badword_button_menuexpl"];
    $i++;

    $tempRoll = $gorumroll;
    $tempRoll->list = $userClassName;
    $tempRoll->method = "stat";
    $tempRoll->rollid = 0;
    $menu[$i]["menu"]=$tempRoll->generAnchor($lll["stati"]);
    $menu[$i]["expl"]=$lll["stati_menuexpl"];
    $i++;

    return $menu;
}

function showSecMenu($title,$menu,&$s)
{
    $s="";
    $s.=generBoxUp("100%","5");
    $s.="<tr class='header'><th colspan='2'>$title</th></tr>\n";
    foreach($menu as $item) {
        $s.="<tr class='cell'>";
        $s.="<td>".$item["menu"]."</td>\n";
        $s.="<td>".$item["expl"]."</td>\n";
        $s.="</tr>\n";
    }
    $s.="</td></tr>";
    $s.=generBoxDown();
}
}//END Class
?>
