<?php


$user_typ=  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "min" =>"1",
                "create_form: form invisible",
                "login_form: form invisible",
                "remind_password_form: form invisible",
                "form hidden"
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"32",
                "min" =>"1",
                "mandatory",
                "list",
                "details",
                "change_password_form: form invisible",
                "remind_password_form: form invisible",
            ),
            "email"=>array(
                "type"=>"VARCHAR",
                "text",
                "mandatory",
                "max" =>"64",
                "min" =>"1",
                "details",
                "change_password_form: form invisible",
                "list",
            ),
            "password"=>array(
                "type"=>"VARCHAR",
                "max" =>"32",
                "mandatory",
                "remind_password_form: form invisible",
                "modify_form: form invisible",
                "create_form: form invisible",
                "password"
            ),
            "passwordCopy"=>array(
                "type"=>"VARCHAR",
                "max" =>"32",
                "password",
                "mandatory",
                "remind_password_form: form invisible",
                "login_form: form invisible",
                "modify_form: form invisible",
                "create_form: form invisible",
                "no column"
            ),
            "newPassword"=>array(
                "type"=>"VARCHAR",
                "max" =>"32",
                "password",
                "form invisible",
            ),
            "rememberPassword"=>array(
                "type"  =>"INT",
                "bool",
                "remind_password_form: form invisible",
                "change_password_form: form invisible",
            ),            
            "notes"=>array(
                "type"=>"TEXT",
                "textarea",
                "rows" => 10,
                "cols" => 50,
                "details",
                "remind_password_form: form invisible",
                "login_form: form invisible",
                "change_password_form: form invisible",
            ),
            "isAdm"=>array(
                "type"  =>"INT",
                "default"=>"0",
                "form invisible",
            ),
            "affId"=>array(
                "type"  =>"INT",
                "default"=>"0",
                "form invisible",
            ),
            "creationtime"=>array(
                "type"=>"INT",
                "form invisible",
            ),
            "lastClickTime"=>array(
                "type"=>"INT",
                "form invisible",
            ),
            "active"=>array(
                "type"  =>"INT",
                "default"=>"0",
                "form invisible",
            )            
        ),
        "primary_key"=>"id",
        "unique_keys"=>"name",
        "delete_confirm"=>"name",
        "remind_password_form: submit"=>array("ok", "back"),
        "sort_criteria_attr"=>"name",
        "sort_criteria_dir"=>"a"
    );

class User extends Object
{
function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $gorumrecognised, $gorumauthlevel, $gorumuser,$lll;
    global $generalRight;
    $isAdm = ($gorumrecognised && $gorumuser->isAdm);    
    $generalRight = FALSE;
    if( $method==Priv_delete && $isAdm) 
    {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }    
    elseif($method==Priv_delete && isset($this->id) &&
           $this->id==$gorumuser->id)
    {
        $hasRight=TRUE;
        $generalRight = FALSE;
    }    
    elseif( $method==Priv_load )
    {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }    
    elseif( $method==Priv_create )
    {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }
    else if( !$gorumrecognised  )
    {
        $hasRight=FALSE;
        $generalRight = TRUE;
    }
    elseif( $isAdm ) 
    {
        $hasRight=TRUE;
        $generalRight = TRUE;
    }
    elseif( isset($this->id) && $this->id==$gorumuser->id) 
    {
        $hasRight=TRUE;
        $generalRight = FALSE;
    }
    else
    {
        $hasRight=FALSE;
        $generalRight = FALSE;
    }
    if( !$hasRight && $giveError )
    {
        handleError($lll["permission_denied"]);
    }
    return ok;
}
 
function loginForm(&$s)
{
    global $gorumuser, $necessaryAuthLevel, $gorumauthlevel; 
    global $gorumrecognised, $gorumroll, $lll, $pwRemind, $emailAccount;    

    $s="";
    if( !$gorumroll->invalid )
    {
        $this->password="";
        if( $gorumauthlevel<=Loginlib_GuestLevel ) 
        {
            $this->rememberPassword = 
                ($necessaryAuthLevel==Loginlib_BasicLevel);   
        }
        elseif( $gorumauthlevel==Loginlib_BasicLevel ) 
        {
            // Ha egy initial password emailben kattintanak ra a linkre,
            // akkor a $this->name ki lesz toltve, kulonben pedig az
            // azonositas soran megallapitott juzernevet kell a Name
            // mezoben megjeleniteni
            if( $emailAccount )
            {
                if( !isset($this->email))$this->email=$gorumuser->email;
            }
            else
            {            
                if( !isset($this->name) ) $this->name =$gorumuser->name;
            }    
            $this->rememberPassword = FALSE;
        }
        else // nagyobb egyenlo, mint low level
        {
            // Login as different user
            if( $emailAccount ) $this->email = "";
            else $this->name = "";
            $this->rememberPassword = 
                ($necessaryAuthLevel==Loginlib_BasicLevel);   
        }    
    } 
    else $gorumroll->invalid=FALSE;
    global $necessaryAuthLevel;
    generForm($this,$s);
    if ($pwRemind) {
        $tempRoll = $gorumroll;
        $tempRoll->method = "remind_password_form";
        if( $emailAccount ) $tempRoll->email = $this->email;
        else $tempRoll->name = $this->name;
        $s.="<br>".$tempRoll->generAnchor($lll["remind_me_pw"],"item").
            "<br>";
    }
}

function changePasswordForm(&$s)
{
    global $gorumroll, $gorumuser, $lll;
    
    // Mindenki csak a sajat jelszavat modosithatja, kiveve az admin:
    hasAdminRights( $isAdm );
    if( $this->id!=$gorumuser->id && !$isAdm )
    {
        handleError($lll["permission_denied"]);
    }
    if( !$gorumroll->invalid )
    {    
        $this->password = $this->passwordCopy = ""; 
    } 
    else $gorumroll->invalid = FALSE;
    $this->generForm($s);
}

function modifyForm(&$s)
{
    global $gorumroll, $lll, $user_typ, $emailAccount;
    
    if( !$gorumroll->invalid )
    {    
        $ret = $this->load();
        if( $ret )
        {
            $txt = $lll["not_found_in_db"];
            handleError($txt);
        }
    } 
    else $gorumroll->invalid = FALSE;
    $this->hasObjectRights($hasRight, Priv_modify, TRUE);
    hasAdminRights($isAdm);
    if( !$isAdm && !$emailAccount ) // az emailt csak az adm valtoztathatja meg
    {
        $user_typ["attributes"]["email"][]="modify_form: form invisible";  
    }
    $this->generForm($s);
}

function create($overrulePrivilege, &$s)
{
    global $lll, $emailAccount;
    global $gorumuser, $userClassName;
    global $gorumauthlevel;
    global $gorumroll, $registrationType;
    global $whatHappened, $infoText;

    $s="";
    $whatHappened = "form_submitted";
    $this->hasObjectRights( $hasRight, Priv_create, TRUE );
    
    // Generalunk egy passwordot:
    mt_srand((double)microtime()*1000000);
    $this->password = mt_rand(1000000, 10000000);
    settype($this->password, "string");
    $this->active=FALSE; // Majd az elso bejelentkezes utan lesz tru

    // Meg kell orizni, hogy az email checkes emaillel ez menjen ki:
    $plainPassword = $this->password;
    $this->password = getPassword($this->password);
    
    // check if exists:
    global $userClassName;
    $userCheck = new $userClassName;
    $userCheck->name = $this->name;
    $ret = load($userCheck, array("name"));
    if( $ret==ok  )
    {
        $whatHappened="invalid_form";
        $infoText = $lll["userAllreadyExists"];
        return ok;
    }
    if( $emailAccount )
    {
        $userCheck = new $userClassName;
        $userCheck->email = $this->email;
        $ret = load($userCheck, array("email"));
        if( $ret==ok  )
        {
            $whatHappened="invalid_form";
            $infoText = $lll["userAllreadyExistsWithEmail"];
            return ok;
        }
    }
    if( $gorumauthlevel==Loginlib_NewUser )  // nem tud kukizni
    {
        $whatHappened="invalid_form";
        $infoText = $lll["cannotAcceptCookie"];
        return ok;
    }
    if( $gorumauthlevel==Loginlib_GuestLevel )
    {
        // Ez az install script miatt van, hogy az elsonek
        // regisztralt user admin maradhasson:
        unset($this->isAdm);
        // don't create a new user, only updating the current 
        // nameless user with the newly registered username and 
        // password:
        $this->id = $gorumuser->id;
        modify($this);
        if( $whatHappened=="invalid_form") {
            return ok;
        }
    } 
    else if( $gorumauthlevel==Loginlib_BasicLevel  ||
             $gorumauthlevel==Loginlib_LowLevel )
    {
        generateRandomId( $randomId );
        $this->id = $randomId;
        create($this);
        if( $whatHappened=="invalid_form") {
            return ok;
        }
    }                 
    $infoText = $lll["youWillGetAEmailCheckEmail"];
    $n = new Notification;
    $n->id = Notification_initialPassword;
    $n->load();
    $tempRoll = new Roll;
    $tempRoll->list = $userClassName;
    $tempRoll->method = "login_form";
    if( $emailAccount ) $tempRoll->email = $this->email; 
    else $tempRoll->name = $this->name;
    $url = $tempRoll->makeUrl("mixed");
    $n->send( $this->email, $plainPassword, $url );
    return ok;
} 

function remindPassword()
{
    global $gorumroll,$lll,$gorumuser;
    global $whatHappened, $infoText;
    global $applName;
    
    if( $gorumroll->submit==$lll["back"] )
    {   
        $whatHappened = "invalid_form";
        $gorumroll->method = "login";
        $infoText = $lll["operation_cancelled"];
        return ok;
    } 
    global $userClassName;
    $query = "SELECT * FROM $applName"."_$userClassName ".
             "WHERE email='$this->email'";
    $users = new $userClassName;         
    loadObjectsSql($users, $query, $users);
    if( !count($users) ) {
        $whatHappened = "invalid_form";
        $infoText=$lll["invalid_email"];
        return ok;
    }
    foreach( $users as $user )
    {
        mt_srand((double)microtime()*1000000);
        $newPassword = mt_rand(1000000, 10000000);
        $user->newPassword = getPassword($newPassword);
        modify($user);
        
        $tempRoll = new Roll;
        $tempRoll->method = "activate_new_password";
        $tempRoll->list = $userClassName;
        $tempRoll->pwd = $newPassword;
        $tempRoll->id = $user->id;
        $url=$tempRoll->makeUrl("mixed");
        if( class_exists("notification") )
        {
            $n = new Notification;
            $n->id = Notification_remindPassword;
            $n->load();
            if( $n->active )
            {
                $n->send( $this->email, $user->name, $newPassword,$url);
            }
        }
        else
        {
            $mailtext=sprintf($lll["remindmail_text"],$user->name,
                              $newPassword,$url);
            //TODO:from
            global $adminEmail;
            if ($adminEmail) {
                $from="From: $adminEmail";
                mail($this->email,$lll["remindmail_subj"],
                     $mailtext,$from);
            }
            else {
                mail($this->email,$lll["remindmail_subj"],
                     $mailtext);
            }
        }
    }
    $infoText=$lll["remindmail_sent"];
    $whatHappened = "invalid_form";
    $gorumroll->method = "login";
    return ok;
}

function changePassword()
{
    global $whatHappened, $infoText, $lll;
    global $gorumroll, $gorumuser, $cookiePath;
    $whatHappened = "form_submitted";
    // Mindenki csak a sajat jelszavat modosithatja, kiveve az admin:
    hasAdminRights( $isAdm );
    if( $this->id!=$gorumuser->id && !$isAdm )
    {
        handleError($lll["permission_denied"]);
    }
    global $minPasswordLength;
    if( $this->password!=$this->passwordCopy )
    {
        $whatHappened="invalid_form";
        $infoText = $lll["mistypedPassword"];
        return ok;
    }
    elseif( strlen($this->password)<$minPasswordLength )
    {
        $whatHappened="invalid_form";
        $infoText = sprintf($lll["passwordTooShort"],
                            $minPasswordLength);
        return ok;
    }
    $this->password = getPassword($this->password);
    privilegeModify($this);

    // Ha a sajat passwordjet modositja:    
    if( $this->id==$gorumuser->id )
    {
        setcookie("usrPassword", $this->password, 
                  Loginlib_ExpirationDate, $cookiePath);
    }          
    $infoText = $lll["passwordModified"];
    return ok;
}

function lowLevelLogin(&$s)
{
    global $lll, $emailAccount;
    global $whatHappened;
    global $gorumroll;
    global $infoText, $cookiePath;
    
    $s="";
    $whatHappened="form_submitted";
    if( $gorumroll->submit==$lll["no"] )
    {
        return ok;
    }
    validUser($isValid,$this->id,
              $emailAccount ? $this->email : $this->name,
              $this->password);
    if( $isValid )
    {
        // Ha az uj id nem ugyanaz mint a regi, akkor a cookie-t
        // felulirjuk
        global $gorumuser;
        global $gorumauthlevel;
        // A regi usert es azokat a dolgokat, amiket o hozott letre,
        // de mar nem kellenek toroljuk:
        if( $gorumauthlevel==Loginlib_GuestLevel && 
            $this->id!=$gorumuser->id )
        {
            delete($gorumuser);
        }    
          
        $gorumroll->globalUserId = $gorumroll->sessionUserId =$this->id;
        $gorumroll->usrPassword = getPassword($this->password);    
        setcookie("globalUserId", $this->id, 
                  Loginlib_ExpirationDate, $cookiePath);
        setcookie("sessionUserId", $this->id, 0, $cookiePath);
        setcookie("usrPassword", $gorumroll->usrPassword, 
                  Loginlib_ExpirationDate, $cookiePath);
        // Reauthenticate:
        authenticate(TRUE);        
        global $initClassName;
        $infoText = sprintf($lll["greeting"],
                            htmlspecialchars($gorumuser->name) );            
        // az uj userhez rogton az uj settingek is kellenek:
        $init = new $initClassName;
        $init->initializeUserSettings();
        // modositjuk a rememberPassword-ot, ha szukseges:
        global $gorumuser;
        if( !isset($this->rememberPassword) ) $this->rememberPassword=FALSE;
        $mod = FALSE;
        if( $this->rememberPassword!=$gorumuser->rememberPassword )
        {
            $gorumuser->rememberPassword = $this->rememberPassword;
            $mod = TRUE;
        }
        if( !$gorumuser->active )
        {
            $gorumuser->active = TRUE;
            $mod = TRUE;
        }
        if( $mod ) modify($gorumuser);
        global $defaultList, $defaultMethod, $defaultRollId;
        $gorumroll->fromlist = $defaultList;
        $gorumroll->frommethod = $defaultMethod;
        $gorumroll->fromid = $defaultRollId;
    }
    else
    {
        $infoText = $lll["loginInvalid"];
        $whatHappened = "invalid_form";
    }
}

function changeAdmStatus()
{
    global $gorumuser;
    global $lll;
    
    hasAdminRights( $isAdm );
    if( !$isAdm )
    {
        handleError($lll["permission_denied"]);
    }
    load($this);
    unset($this->passwordCopy);
    $this->isAdm = !$this->isAdm;
    modify($this); 
    global $whatHappened;
    $whatHappened = "form_submitted";                      
    return ok;
}

function activateNewPassword(&$s)
{
    global $gorumroll, $HTTP_GET_VARS;
    
    load($this);
    // Az biztositja, hogy tenyleg az hivta fel a linket, akinek 
    // levelben ki lett kuldve:
    if( $this->newPassword==getPassword($HTTP_GET_VARS["pwd"]) )
    {
        $this->password = $this->newPassword;
        $this->newPassword="";
        modify($this);
        // Hogy a loginForm-ban ne lassak csillagokat:
        $this->password="";
        $gorumroll->method = "login_form";
        $ret = $this->generForm($s);        
    }
    else
    {
        global $infoText, $lll;
        $infoText = $lll["permission_denied_mistyped_link"];
        return ok;
    }
    return ok;
}

function showListVal($attr)
{
    global $lll,$gorumroll;
    $s="";
    if ($attr=="creationtime" || $attr=="lastClickTime")
    {
        if (isset($this->{$attr})) $s=showTimestamp($this->{$attr});
        else $s=$lll["never"];
    }
    else if( $attr=="email" )
    {
        if( $this->{$attr} )
        {
            $m=htmlspecialchars($this->{$attr});
            $s="<a href='mailto:$m'>$m</a>";
        }
    }
    else if( $attr=="name" )
    {
        $tempRoll = $gorumroll;
        global $userClassName;
        $tempRoll->list=$userClassName;
        $tempRoll->method="showdetails";
        $tempRoll->rollid=$this->id;
        saveInFromFrom($tempRoll);
        $s=$tempRoll->generAnchor($this->{$attr});
    }
    else $s=nl2br(htmlspecialchars($this->{$attr}));
    return $s;
}
function getListSelect()
{
    global $applName,$userClassName;
    $select = "SELECT * FROM $applName"."_$userClassName ".
              "WHERE id!=name AND active='1'";
    return $select;
}

function showModTool($rights)
{
    global $lll,$gorumroll, $showChangePassword, $gorumuser;
    
    $s="";
    hasAdminRights( $isAdm );
    if( ($isAdm || $this->id==$gorumuser->id) && $showChangePassword )
    {
        $tempRoll = $gorumroll;
        $tempRoll->method="change_password_form";
        $tempRoll->rollid = 0;
        saveInFrom($tempRoll);
        $tempRoll->id = $this->id;
        $s.=$tempRoll->generAnchor($lll["changepassword"])." | ";
    }
    $s.=Object::showModTool($rights);
    return $s;
}

function logout(&$s)
{
    global $gorumroll, $onlyInfoText, $cookiePath;

    if( $gorumroll->globalUserId )
    {
        setcookie("globalUserId","",Loginlib_ExpirationDate, $cookiePath);
    }
    if( $gorumroll->sessionUserId )
    {
        setcookie("sessionUserId","",0, $cookiePath);
    }
    if( $gorumroll->usrPassword )
    {
        setcookie("usrPassword","",Loginlib_ExpirationDate, $cookiePath);
    }
    $gorumroll->globalUserId = 0;
    $gorumroll->sessionUserId = 0;
    $gorumroll->usrPassword = 0;
    global $whatHappened;
    global $infoText;
    global $lll;
    global $gorumuser, $gorumroll;
    global $scriptName;
    $what_happened="invalid_form";
    $infoText = sprintf($lll["goodbye"], $gorumuser->name);
    // Ha az applikacio showApp-ja ezt olvassa, akkor csak az infoTextet
    // szabad kiirnia az oldalra es semmi mast:
    $onlyInfoText = TRUE;
    $s = "<a href='$scriptName'>".$lll["return_to_forum"]."</a>";
}

}
    
function validUser( &$isValid, &$userId, $nameOrEmail, $password )
{
    global $emailAccount;
    global $userClassName;
    $user = new $userClassName;
    if( $emailAccount ) 
    {
        $user->email = $nameOrEmail;
        $ret = load($user, array("email"));
    }
    else
    {
        $user->name = $nameOrEmail;
        $ret = load($user, array("name"));
    }    
    if( $ret==ok )
    {
        if( getPassword($password)==$user->password && 
            $user->id!=$user->name)
        {
            $isValid = TRUE;
            $userId = $user->id;
        }
        else
        {
            $isValid = FALSE;
            $userId = $user->id;
        }
    }
    else if( $ret==not_found_in_db )
    {
        $isValid = FALSE;
    }
    return ok;
}

function getPassword( $s ) 
{ 
    $s=addcslashes($s,"'\\");
    $data = mysql_query("select password('$s')"); 
    $row = mysql_fetch_row($data); 
    return $row[0]; 
} 

?>
