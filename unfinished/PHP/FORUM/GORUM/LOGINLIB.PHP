<?php

function authenticate( $fromLogin=FALSE )
{
    global $gorumuser, $gorumroll, $userClassName, $infoText, $lll;
    global $gorumauthlevel, $gorumrecognised, $necessaryAuthLevel;
    global $autoLogout, $autoLogoutTime;
    global $HTTP_COOKIE_VARS, $cookiePath;
    global $dontSetLastClickTime;
    
    

    if( $gorumroll->sessionUserId )
    {
        $gorumuser = new $userClassName;
        $gorumuser->id = $gorumroll->sessionUserId;
        $ret = load($gorumuser);
        if( $ret==ok )
        {
            if( $gorumroll->usrPassword && 
                $gorumuser->password == $gorumroll->usrPassword)
            {   
                if( !$fromLogin ) $expired=timeoutExpired();
                else $expired = FALSE;
                if( !$expired )
                {
                          "$gorumroll->usrPassword, gorumuser->password: ".
                          "$gorumuser->password";
                    $gorumauthlevel = Loginlib_LowLevel;
                    $gorumrecognised = TRUE;          
                    if(!isset($dontSetLastClickTime)) {
                        $gorumuser->lastClickTime = time();
                        modify( $gorumuser );
                    }
                }
                return;
            }
            else
            {
                      "$gorumroll->usrPassword, gorumuser->password: ".
                      "$gorumuser->password";
            }
        }
    }
    if( $gorumroll->globalUserId )
    {
        $gorumuser = new $userClassName;
        $gorumuser->id = $gorumroll->globalUserId;
        $ret = load($gorumuser);
        if( $ret==ok )
        {
            if( $gorumuser->id==$gorumuser->name || 
                isset($gorumroll->usrPassword) && 
                $gorumuser->password == $gorumroll->usrPassword)
            {    
                if( !$fromLogin ) $expired=timeoutExpired();
                else $expired = FALSE;
                if( $expired )
                {
                    return;
                }
                elseif( $gorumuser->id==$gorumuser->name )
                {
                    $gorumauthlevel = Loginlib_GuestLevel;
                    $gorumrecognised = FALSE;  
                }
                else  
                {
                    $gorumauthlevel = Loginlib_BasicLevel;
                    $gorumrecognised = $gorumuser->rememberPassword;  
                    // Ez a feltetel csak akkor aktualizalja a 
                    // lastClickTime-ot, ha a user recognised. Ez igy jo 
                    // is, csak az a problema, hogy akkor a 
                    // currentlyOnline sorban nem tudnak meg guest-kent
                    // se szerepelni azok a juzerek, akik azonositottak,
                    // de nem recognised-ok:
                    if( $gorumrecognised &&
                        !isset($dontSetLastClickTime))
                    {
                        $gorumuser->lastClickTime = time();
                        modify( $gorumuser );
                    }        
                }
                return;
            }
        }
        if( $ret==not_found_in_db )  
        {
            $gorumauthlevel = Loginlib_GuestLevel;
            //than create without name
            $gorumuser->init(array("id"=>$gorumuser->id, 
                                   "name"=>$gorumuser->id));
            if (isset($HTTP_COOKIE_VARS["affiliate"])) {
                $u = new $userClassName;
                $u->name=$HTTP_COOKIE_VARS["affiliate"];
                $ret=load($u,array("name"));
                if ($ret!=not_found_in_db) {
                    $gorumuser->affId=$u->id;
                }
            }
            
            create($gorumuser);
            // azert hogy az isAdm es hasonlok is ki legyenek toltve:
            load($gorumuser);
            if( isset($gorumroll->usrPassword) ) {
                setcookie("usrPassword","",Loginlib_ExpirationDate, $cookiePath);
            }
            $gorumrecognised = FALSE;          
            return;
        }
    }
    $gorumauthlevel = Loginlib_NewUser;
    $gorumrecognised = FALSE;          
    generateRandomId( $randomId );
    $gorumuser = new $userClassName;
    $gorumuser->isAdm = FALSE;
    $gorumuser->isMod = FALSE;
    $gorumuser->id = $randomId;
    $gorumuser->name = $randomId;
    //: Note: The sideeffect of this function is that it tries to set 
    //:       the GlobalUserId cookie if the level of authentication 
    //:       has proved to be NewUser.
    setcookie("globalUserId",$randomId,Loginlib_ExpirationDate, $cookiePath);
}

function timeoutExpired()
{
    global $gorumuser, $gorumroll, $userClassName, $infoText, $lll;
    global $gorumauthlevel, $gorumrecognised, $necessaryAuthLevel;
    global $autoLogout, $autoLogoutTime, $scriptName;

    if( $autoLogout && 
        time()-$gorumuser->lastClickTime > $autoLogoutTime*60 &&
        ($gorumroll->list!=$userClassName || 
        ($gorumroll->method!="create_form"&& 
        $gorumroll->method!="create"&& 
        $gorumroll->method!="login_form"&& 
        $gorumroll->method!="login")))
    {
        logout($s);
        $s = "Timeout expired. Please, log in!";    
        $s.= "<p><a href='$scriptName'>Click here to return to the application!</a>";
        echo $s;
        die();
        $gorumroll->method = "logout";
        $gorumroll->list=$gorumroll->class=$userClassName;
        authenticate();
        return TRUE;
    }
    return FALSE;
}

function createFirstAdmin()
{
    global $gorumauthlevel, $gorumuser, $gorumroll;
    global $gorumrecognised, $registrationType;
    global $userClassName, $HTTP_COOKIE_VARS, $cookiePath;
    
    $gorumauthlevel = Loginlib_LowLevel;
    $gorumrecognised = TRUE;
    $gorumuser = new $userClassName;
    $gorumuser->id = $HTTP_COOKIE_VARS["globalUserId"];
    $gorumuser->name = "admin";
    $gorumuser->password = getPassword("admin");
    $gorumuser->email = "admin@admin.admin";
    $gorumuser->isAdm = TRUE;
    if( $registrationType==User_emailCheck ) $gorumuser->active = TRUE;
    $gorumuser->lastClickTime = time();
    create($gorumuser);
    // azert hogy az isAdm es hasonlok is ki legyenek benne toltve:
    load($gorumuser);
    setcookie("usrPassword",$gorumuser->password,
              Loginlib_ExpirationDate, $cookiePath);
    setcookie("sessionUserId", $gorumuser->id, 0, $cookiePath );
}

function generateRandomId( &$id )
{
    global $userClassName,$randIdMax,$randIdMin;
    if (!isset($randIdMin)) $randIdMin=0;
    if (!isset($randIdMax)) $randIdMax=getrandmax();
    $user = new $userClassName;
    mt_srand((double)microtime()*1000000);
    do
    {
        $id = (int)mt_rand($randIdMin,$randIdMax);
        $user->id = $id;
        $ret = load($user);
    }
    while( !$ret );
    return ok;
}

function initializeTimeoutServices()
{
    // Ez a fuggveny a gorumban nincs felhivva. Ha az applikacioban
    // netalantan felhivnank, akkor ott kell gondoskodni rola, hogy a 
    // leszarmazott userben a lastClickTime attributum benne legyen.
    global $gorumuser;
    global $userClassName;
    
    $gorumuser->lastClickTime = time();
    $user = new $userClassName;
    $user->init( array("id"=>$gorumuser->id, 
                       "lastClickTime"=>$gorumuser->lastClickTime) );
    modify($user);
    return ok;
}

function logout(&$s)
{
    global $gorumroll, $userClassName;
    // Ez azert kell, hogy a logout is feluldefinialhato legyen az 
    // applikacioban:
    $u = new $userClassName;
    $u->logout($s);
}

function showStatusEcho()
{
    global $lll;
    global $gorumuser;
    global $gorumauthlevel;
    global $gorumrecognised;
    $s = "";
    $s.= "<P>";
/*
    global $blockSize;
    global $rangeBlockSize;
    global $timeOut;
    global $upperTemplate;
    global $lowerTemplate;
    global $textAreaRows;
    global $textAreaCols;
    global $minPasswordLength;
    global $htmlTitle;
    global $htmlKeywords;
    global $htmlDescription;
    $s.= "blockSize: <FONT COLOR=Red>$blockSize</FONT><BR>";
    $s.= "rangeBlockSize: <FONT COLOR=Red>$rangeBlockSize</FONT><BR>";
    $s.= "timeOut: <FONT COLOR=Red>$timeOut</FONT><BR>";
    $s.= "upperTemplate: <FONT COLOR=Red>$upperTemplate</FONT><BR>";
    $s.= "lowerTemplate: <FONT COLOR=Red>$lowerTemplate</FONT><BR>";
    $s.= "textAreaRows: <FONT COLOR=Red>$textAreaRows</FONT><BR>";
    $s.= "textAreaCols: <FONT COLOR=Red>$textAreaCols</FONT><BR>";
    $s.= "minPasswordLength: <FONT COLOR=Red>$minPasswordLength</FONT><BR>";
    $s.= "htmlTitle: <FONT COLOR=Red>$htmlTitle</FONT><BR>";
    $s.= "htmlKeywords: <FONT COLOR=Red>$htmlKeywords</FONT><BR>";
    $s.= "htmlDescription: <FONT COLOR=Red>$htmlDescription</FONT><BR>";
    $s.= "<P>";
    // Zorum specifikusak:
    global $timeOut;
    global $allowHtmlInPost;
    global $allowUbbInPost;
    global $allowSmileyInPost;
    global $globalHotTopicNum;
    global $attFileSize;
    global $attAllowExt;
    global $attForbidExt;
    global $adminEmail;
    global $forumView;
    $s.= "timeOut: <FONT COLOR=Red>$timeOut</FONT><BR>";
    $s.= "allowHtmlInPost: <FONT COLOR=Red>$allowHtmlInPost</FONT><BR>";
    $s.= "allowUbbInPost: <FONT COLOR=Red>$allowUbbInPost</FONT><BR>";
    $s.= "allowSmileyInPost: <FONT COLOR=Red>$allowSmileyInPost</FONT><BR>";
    $s.= "globalHotTopicNum: <FONT COLOR=Red>$globalHotTopicNum</FONT><BR>";
    $s.= "attFileSize: <FONT COLOR=Red>$attFileSize</FONT><BR>";
    $s.= "attAllowExt: <FONT COLOR=Red>$attAllowExt</FONT><BR>";
    $s.= "attForbidExt: <FONT COLOR=Red>$attForbidExt</FONT><BR>";
    $s.= "adminEmail: <FONT COLOR=Red>$adminEmail</FONT><BR>";
    $s.= "forumView: <FONT COLOR=Red>$forumView</FONT><BR>";
    $s.= "<P>";
*/    
    $s.= "UserName: <FONT COLOR=Red>$gorumuser->name</FONT><BR>";
    $s.= "UserId: <FONT COLOR=Red>$gorumuser->id</FONT><BR>";
    $s.= "Level of authentication: <FONT COLOR=Red>";
    switch( $gorumauthlevel )
    {
        case Loginlib_NewUser:
            $s.= "NewUser";
            break;
        case Loginlib_GuestLevel:
            $s.= "GuestLevel";
            break;
        case Loginlib_BasicLevel:
            $s.= "BasicLevel";
            break;
        case Loginlib_LowLevel:
            $s.= "LowLevel";
            break;
        case 0:
            $s.= "0";
            break;
        default:    
            $s.= $gorumauthlevel;
            break;
    }
    $s.= "</FONT><BR>";
    $s.= isset($gorumrecognised) ? "gorumrecognised: $gorumrecognised<BR>" : "gorumrecognised is not set<BR>";
    $s.="---------------------------<BR>";
    global $gorumroll;
    $s.= "Loginlib_GlobalUserId: $gorumroll->globalUserId<BR>";
    $s.= "Loginlib_SessionUserId: $gorumroll->sessionUserId<BR>";
    $s.= "Loginlib_Password: $gorumroll->usrPassword<BR>";
    return $s;
}
?>
