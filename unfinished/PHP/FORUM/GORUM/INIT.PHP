<?php

class Init
{
function initializeSystemSettings()
{
    global $hostName, $dbUser, $dbUserPw, $dbName;
    global $gorumroll, $HTTP_POST_VARS, $HTTP_GET_VARS ;
    
    connectDb($hostName, $dbUser, $dbUserPw, $dbName);
    //hackImageInput();
    $gorumroll = new Roll;
    $gorumroll->create();
    authenticate(); 
    $this->initializeUserSettings();
    global $FLOOD;
    if (isset($FLOOD)) {
        if (isset($HTTP_POST_VARS["flood"])) {
            floodCheck($HTTP_POST_VARS["flood"]);
        }
        if (isset($HTTP_GET_VARS["flood"])) {
            floodCheck($HTTP_GET_VARS["flood"]);
        }
    }
    if( class_exists("badwords") )
    {
        global $gorumbadwords;
        $gorumbadwords = new BadWords;
        loadObjectsSql( $gorumbadwords, "", $gorumbadwords );
    }
    if( class_exists("cronjob") )
    {
        executeCronJobs();
    }
    if( class_exists("reflog") ) {
        $log = new RefLog;
        $log->create();
    }
    if( class_exists("statlog") ) {
        $log = new StatLog;
        $log->create();
    }
}
    
function initializeUserSettings()
{
    global $lll;//A lang file beli lll miatt, ne torold!
    global $emailAccount, $user_typ;

    if( class_exists("globalsettings") )
    {
        $globalSettings = new GlobalSettings;
        $globalSettings->load();
        $globalSettings->initGlobals();
    }
    // a megfelelo nyelvi file-okat behozzuk:
    global $language;
    $languageFile = "lang_".$language.".php";
    include("./gorum/lang/$languageFile");
    include("./lang/$languageFile");
    //@include("./../../lang/$languageFile");TODO
    if( $emailAccount )
    {
        $user_typ["attributes"]["email"][]="modify_form: readonly";
        $user_typ["attributes"]["name"][]="login_form: form invisible";
        $user_typ["unique_keys"]="email";
        $lll["user_name"]=$lll["user_displayName"];
        $lll["userAllreadyExists"]=$lll["userAllreadyExistsWithName"];
    }
    else
    {
        $user_typ["attributes"]["name"][]="modify_form: readonly";
        $user_typ["attributes"]["email"][]="login_form: form invisible";
    }
}

function showHtmlHead()
{
    global $headTemplate,$htmlTitle,$htmlKeywords,$htmlDescription;
    global $applName,$language;
    
    $s="";
    $s.="<head>\n";
    if ($htmlTitle) $s.="<title>$htmlTitle</title>\n";
    //else $s.="<title>$applName</title>\n";//tesztelesnel jol jon
    if ($htmlKeywords) {
        $s.="<meta name='keywords' content='$htmlKeywords'>\n";
    }
    if ($htmlDescription) {
        $s.="<meta name='description' content='$htmlDescription'>\n";
    }
    if ($language=="gr") {
        $s.="<META HTTP-EQUIV='Content-Type'".
            " CONTENT='text/html; charset=ISO-8859-7'>\n";   
    }
    if ($language=="ci") {
        $s.="<META HTTP-EQUIV='content-type' CONTENT='text/html;".
            " charset=GB2312'>";
    }
    $s.="$headTemplate\n";
    $s.=$this->showCss();
    $s.="</head>\n";
    return $s;
}

function showCss()
{
    global $fixCss;
    $s="";
    if ($fixCss) {
        if ($fixCss=="no") {
            $s="";
        }
        else {
            $s.="<link rel='StyleSheet' href='$fixCss' type='text/css'>\n";
        }
    }
    else {
        $ss = new StyleSheet;
        $ss->id=1;
        load($ss);
        $s.="<style type='text/css'>\n".$ss->css."</style>\n";
    }
    return $s;
}

function generBoxUp($tableWidth="100%",$cp="2",$line="line",
                    $boxframe="boxframe")
{
    global $boxWithFrame,$boxShadow;
    $s="";

    $w = $tableWidth;
    if ($boxShadow) {
        $s.="<table border='0' width='$tableWidth' cellpadding='0'".
            " cellspacing='0'>";
        $s.="<tr><td>";
        $w="100%";
    }
    if ($boxWithFrame) {
        $s.="<table border='0' class='$boxframe' width='$w'".
            " cellpadding='0' cellspacing='0'><tr><td>\n";
        $s.="<table border='0' width='100%' cellpadding='0'".
            " cellspacing='1'><tr><td>\n";
        $w="100%";
    }
    $s.="<table border='0' class='$line' width='$w'".
        " cellpadding='0' cellspacing='0'><tr><td>\n";
    $s.="<table border='0' width='100%' cellpadding='$cp'".
        " cellspacing='1'>\n";
    return $s;
}

function generBoxDown()
{
    global $boxWithFrame,$xi,$boxShadow;
    $s="";
    //ezt a ket sort lehet, hogy forditva kellene irni
    if ($boxWithFrame) $s.="\n</td></tr></table>\n</td></tr></table>\n";
    $s.="\n</table>\n</td></tr></table>\n";
    if ($boxShadow) {
        $s.="</td>";
        $s.="<tr><td background='$xi/boxshadow.gif'>".
            "<img src='$xi/b.gif' height='4'></td></tr></table>";
    }
    return $s;
}

function showApp()
{
    global $gorumcontent,$navBarText,$infoText;
    global $upperTemplate,$lowerTemplate,$lll;
    global $gorumrecognised,$gorumuser;

    
    $s="";
    $s.="<table border='0' width='100%'><tr><td valign='top' align='center' width='90%' class='userstatus'>\n";
    $s.=$this->showUserStatus();
    $s.="</td></tr>\n";
    $s.="<tr><td valign='top' align='center' width='90%'>\n";
    $this->makeLoginMenu($loginMenu, "menu");
    $s.=generBoxUp("100");
    $s.="<tr class='cell'>\n";
    $this->makeLoginMenu($loginMenu, "menu");
    $s.=$this->showLoginMenu($loginMenu);
    $this->makeUserMenu($userMenu, "menu");
    $s.=$this->showUserMenu($userMenu);
    $s.="</tr>\n";
    $s.=generBoxDown();
    $s.="</td></tr>\n";

    if( $gorumrecognised  && $gorumuser->isAdm) {
        $s.="<tr><td valign='top' align='center' width='90%'>\n";
        $s.=generBoxUp("100");
        $s.="<tr class='cell'>\n";
        $this->makeAdminMenu($adminMenu, "menu");
        $s.=$this->showAdminMenu($adminMenu);
        $s.="</tr>\n";
        $s.=generBoxDown();
        $s.="</td></tr>\n";
    }
    
    if( $navBarText ) {
        $s.="<tr><td valign='top' align='center' width='90%'>\n";
        $s.="<br>$navBarText\n";
        $s.="</td></tr>\n";
    }    
    if( $infoText ) {
        $s.="<tr><td valign='top' align='center' width='90%'>\n";
        $s.="<br><span class='messageLine'>$infoText</span>\n";
        $s.="</td></tr>\n";
    }
    $s.="<tr><td valign='top' align='center' width='90%'>\n";
    $s.=$gorumcontent;  
    $s.="</td></tr></table>\n";
    $s.=$this->showVersionFooter();
    return $s;
}

// Ezt a fg-t mindenkeppen felul kell irni a leszarmaztatott osztalyban:
function showVersionFooter()
{
    return "<br><center><font color='#888888' size='-1'>General application platform from ".
           "PhpOutsourcing</font></center>\n";
}

function showUserStatus()
{
    global $gorumuser,$gorumrecognised,$gorumroll,$lll;
    $s="";
    if ($gorumrecognised) {
        $s.=sprintf($lll["loggedinas"],
                htmlspecialchars($gorumuser->name));
    }
    else {
        $s.=$lll["regorlog"];
    }
    $s.="\n";
    return $s;
}

// A menupontok kirakasanak legegyszerubb alapesete. Ha maskepp akarjuk,
// akkor ezeket a fg-eket felul kell irni:
function showLoginMenu($menu)
{
    $s="";
    foreach($menu as $id=>$menuPoint) {
        $s.="<td nowrap>$menuPoint</td>\n";
    }
    return $s;
}

function showUserMenu($menu)
{
    $s="";
    foreach($menu as $id=>$menuPoint) {
        $s.="<td nowrap>$menuPoint</td>\n";
    }
    return $s;
}

function showAdminMenu($menu)
{
    $s="";
    foreach($menu as $id=>$menuPoint) {
        $s.="<td nowrap>$menuPoint</td>\n";
    }
    return $s;
}

// Ez a fg hatarozza meg, hogy mely menupont milyen feltetellel lesz 
// kirakva. Ha az applikacioban uj menupontokat akarunk felvenni, vagy
// elvenni a menupontokbol, vagy mas feltetelhez kotni a kirakasukat,
// ezt a fg-t kell felulirni
function display( $what )
{
    global $itemClassName, $categoryClassName, $gorumroll;
    global $gorumrecognised, $fixCss, $immediateAppear;
    
    hasAdminRights($isAdm);
    switch( $what )
    {
    case Init_register:
        return !$gorumrecognised;
    case Init_login:
        return !$gorumrecognised;
    case Init_loginDifferent:
        return FALSE;
    case Init_cangePwd:
        return $gorumrecognised;
    case Init_logout:
        return $gorumrecognised;
    case Init_myProfile:
        return $gorumrecognised;
    case Init_myItems:
        return $gorumrecognised && class_exists("item");    
    case Init_addItem:
        return $gorumrecognised && class_exists("item") && 
               $gorumroll->list==$categoryClassName && 
               $gorumroll->method=="showhtmllist" && $gorumroll->rollid;    
    case Init_recentItems:
        return class_exists("item");
    case Init_popularItems:
        return class_exists("item");
    case Init_search:
        return $gorumrecognised && class_exists("search");
    case Init_home:
        return TRUE;
    case Init_settings:
        return $isAdm && class_exists("globalsettings");
    case Init_modStyle:
        return $isAdm && !isset($fixCss);
    case Init_userList:
        return $isAdm;
    case Init_badWords:
        return $isAdm && class_exists("badwords");
    case Init_activeItems:
        return $isAdm && class_exists("item") && !$immediateAppear;
    case Init_inactiveItems:
        return $isAdm && class_exists("item") && !$immediateAppear;
    case Init_cronjobs:
        return FALSE;
    case Init_notifications:
        return $isAdm && class_exists("notification");
    case Init_addCategory:
        return $isAdm && isset($categoryClassName) && 
               $gorumroll->list==$categoryClassName &&
               $gorumroll->method=="showhtmllist";
    case Init_modCategory:
        return $isAdm && isset($categoryClassName) && $gorumroll->rollid 
               && $gorumroll->list==$categoryClassName &&
               $gorumroll->method=="showhtmllist";
    case Init_delCategory:
        return $isAdm && isset($categoryClassName) && $gorumroll->rollid 
               && $gorumroll->list==$categoryClassName &&
               $gorumroll->method=="showhtmllist";
    default:
        return FALSE;           
    }
}

// A display fg altal megszabott feltetelek alapjan kitolti a $menu 
// tombot:
function makeLoginMenu(&$menu, $spanCl="")
{
    global $gorumuser, $gorumroll, $userClassName;
    global $gorumauthlevel, $gorumrecognised;
    global $necessaryAuthLevel;
    global $lll;

    $menu=array();
    $tempRoll = $gorumroll;
    $tempRoll->list = $userClassName;
    $tempRoll->method = "create_form";
    saveInFrom($tempRoll);
    if( $this->display( Init_register ))
    {
        $menu[Init_register]=$tempRoll->generAnchor($lll["register"],$spanCl);
    }
    $tempRoll->method = "login_form";
    if( $this->display( Init_login ))
    {
        $menu[Init_login]=$tempRoll->generAnchor($lll["login"],$spanCl);
    }
    if( $this->display( Init_loginDifferent ))
    {
        $menu[Init_loginDifferent]=$tempRoll->generAnchor($lll["loginDifferrent"],$spanCl);
    }    
    if( $this->display( Init_cangePwd ))
    {
        $tempRoll->method = "change_password_form";
        $tempRoll->id = $gorumuser->id;
        $menu[Init_cangePwd]=$tempRoll->generAnchor($lll["changePassword"],$spanCl);
    }
    if( $this->display( Init_logout ))
    {
        $tempRoll->method = "logout";
        $menu[Init_logout]=$tempRoll->generAnchor($lll["logout"],$spanCl);
    }
}

// A display fg altal megszabott feltetelek alapjan kitolti a $menu 
// tombot:
function makeUserMenu(&$menu, $spanCl="")
{
    global $gorumroll,$userClassName,$gorumuser,$lll;
    global $itemClassName, $categoryClassName, $searchClassName;

    $menu=array();
    // My profile:
    if( $this->display( Init_myProfile ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $userClassName;
        $tempRoll->method = "modify_form";   
        $tempRoll->id = $gorumuser->id;   
        saveInFrom($tempRoll);
        $menu[Init_myProfile]=$tempRoll->generAnchor($lll["my_profile"], $spanCl);        
    }
    // My items:
    if( $this->display( Init_myItems ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $itemClassName."_my";
        $tempRoll->method = "showhtmllist";   
        $tempRoll->rollid = 0;   
        $menu[Init_myItems]=$tempRoll->generAnchor($lll[$itemClassName."_my"], $spanCl);        
    }
    // Add item:
    if( $this->display( Init_addItem ))
    {
        $n = new $itemClassName;
        $tempRoll = $gorumroll;
        $tempRoll->list = $itemClassName;
        $tempRoll->class = $itemClassName;
        $tempRoll->method = "create_form";   
        $tempRoll->rollid = 0;   
        saveInFrom($tempRoll);
        $n->showNewToolPlusUrl($tempRoll);
        $label=$lll[$itemClassName."_newitem"];
        $menu[Init_addItem]=$tempRoll->generAnchor($label, $spanCl);   
    }
    // Recent added items:
    if( $this->display( Init_recentItems ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $itemClassName."_active";
        $tempRoll->method = "showhtmllist";   
        $tempRoll->rollid = 0;   
        $menu[Init_recentItems]=$tempRoll->generAnchor($lll[$itemClassName."_recent"], $spanCl); 
    }       
    // Most popular items:
    if( $this->display( Init_popularItems ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $itemClassName."_popular";
        $tempRoll->method = "showhtmllist";   
        $tempRoll->rollid = 0;   
        $menu[Init_popularItems]=$tempRoll->generAnchor($lll[$itemClassName."_popular"], $spanCl);  
    }      
    // Search form:
    if( $this->display( Init_search ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $searchClassName;
        $tempRoll->method = "create_form";   
        $tempRoll->rollid = 0;   
        saveInFrom($tempRoll);
        $menu[Init_search]=$tempRoll->generAnchor($lll["search"], $spanCl);        
    }
    // Home link:
    if( $this->display( Init_home ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = "";
        $tempRoll->method = "";   
        $tempRoll->fromlist = "";
        $tempRoll->frommethod = "";
        $tempRoll->fromid = "";
        $tempRoll->fromfromlist = "";
        $tempRoll->fromfrommethod = "";
        $tempRoll->fromfromid = "";
        $tempRoll->rollid = 0;   
        global $defaultMethod;
        if ($defaultMethod=="showhtmllist") {
            $tempRoll->clearoff = 1;
        }
        $menu[Init_home]=$tempRoll->generAnchor($lll["home"], $spanCl);
    }
}

// A display fg altal megszabott feltetelek alapjan kitolti a $menu 
// tombot:
function makeAdminMenu(&$menu, $spanCl="")
{
    global $gorumroll,$lll,$immediateAppear, $itemClassName; 
    global $userClassName, $categoryClassName;

    $menu=array();
    // Settings:
    if( $this->display( Init_settings ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = "globalsettings";        
        $tempRoll->method = "modify_form";   
        $tempRoll->rollid = 0;   
        $menu[Init_settings]=$tempRoll->generAnchor($lll["adminsett"], $spanCl);        
    }
    // Modify style:
    if( $this->display( Init_modStyle ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = "stylesheet";
        $tempRoll->method = "modify_form";   
        $tempRoll->rollid = 1;   
        $menu[Init_modStyle]=$tempRoll->generAnchor($lll["modcss"], $spanCl);        
    }
    // User list:
    if( $this->display( Init_userList ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $userClassName;
        $tempRoll->method = "showhtmllist";   
        $tempRoll->rollid = 0;   
        $menu[Init_userList]=$tempRoll->generAnchor($lll["users"], $spanCl);    
    }    
    // Bad words:
    if( $this->display( Init_badWords ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = "badwords";
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = 0;
        $menu[Init_badWords]=$tempRoll->generAnchor($lll["BadWords"],$spanCl);
    }
    // Approved items:
    if( $this->display( Init_activeItems ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $itemClassName."_active";
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = 0;
        $menu[Init_activeItems]=$tempRoll->generAnchor($lll[$itemClassName."_Active"],$spanCl);
    }    
    // Pending items:
    if( $this->display( Init_inactiveItems ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $itemClassName."_inactive";
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = 0;
        $menu[Init_inactiveItems]=$tempRoll->generAnchor($lll[$itemClassName."_Inctive"],$spanCl);
    }
    // Cronjobs:
    if( $this->display( Init_cronjobs ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = "cronjob";
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = 0;
        $menu[Init_cronjobs]=$tempRoll->generAnchor($lll["Cronjobs"],$spanCl);
    }
    // Notifications:
    if( $this->display( Init_notifications ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = "notification";
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = 0;
        $menu[Init_notifications]=$tempRoll->generAnchor($lll["Notifications"],$spanCl);
    }    
    // Create category:
    if( $this->display( Init_addCategory ))
    {
        $c = new $categoryClassName;
        $tempRoll = $gorumroll;
        $tempRoll->class = $categoryClassName;
        $tempRoll->method = "create_form";   
        $tempRoll->rollid = 0;   
        saveInFrom($tempRoll);
        $c->showNewToolPlusUrl($tempRoll);
        $label=$lll[$categoryClassName."_newitem"];
        $menu[Init_addCategory]=$tempRoll->generAnchor($lll[$categoryClassName."_newitem"], $spanCl);        
    }
    // Modify category:
    if( $this->display( Init_modCategory ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $categoryClassName;
        $tempRoll->method = "modify_form";
        $tempRoll->id = $gorumroll->rollid;
        saveInFrom($tempRoll);
        $menu[Init_modCategory]=$tempRoll->generAnchor($lll[$categoryClassName."_mod"],$spanCl);
    }
    // Delete category:
    if( $this->display( Init_delCategory ))
    {
        $tempRoll = $gorumroll;
        $tempRoll->list = $categoryClassName;
        $tempRoll->method = "delete_form";
        $tempRoll->id = $gorumroll->rollid;
        saveInFrom($tempRoll);
        $menu[Init_delCategory]=$tempRoll->generAnchor($lll[$categoryClassName."_del"],$spanCl);
    }
}


function showNavBar()
{
    return "";
}     

}
function hackImageInput()
{
    global $HTTP_POST_VARS,$lll;

    if (isset($HTTP_POST_VARS["cancel_x"])) {
        $HTTP_POST_VARS["submit"]="Cancel";//TODO: internacion.
    }
}
//Ez direkt nincs az osztalyban!
function showPopupHelp()
{
    global $HTTP_GET_VARS,$lll;
    
    $helpText = $HTTP_GET_VARS["expl"];
    $helpTitle = $HTTP_GET_VARS["title"];
    if( function_exists("showAppPopupHelp") ) 
    {
        showAppPopupHelp($helpTitle, $helpText);
    }
    else
    {    
        echo "<center><b>$helpTitle</b></center><br><br>\n";
        echo $helpText;
        die();
    }    
}
/*
Ezt a fuggvenyt csak azert nem torlom ki, mert annyira szep!

function regGlob(&$a,$vn,$e="")
{
    global $HTTP_GET_VARS,$HTTP_POST_VARS,$HTTP_COOKIE_VARS;
    foreach($a as $key=>$val) {
        if (is_array($val) && get_magic_quotes_gpc()) {
            regGlob($val,$vn,$e."['$key']");
        }
        else {
            if (get_magic_quotes_gpc()) {
                $command="\$GLOBALS$e"."['$key']=".
                         "stripslashes(\$$vn$e"."['$key']);";
            }
            else {
                $command="\$GLOBALS$e"."['$key']=".
                         "(\$$vn$e"."['$key']);";
            }
            //echo "comm:$command.<br>\n";
            eval($command);
        }
    }
}
*/
?>
