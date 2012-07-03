<?php
$configFileName="config.php";

if (isset($HTTP_GET_VARS["hostName"])) {
    $hostName=$HTTP_GET_VARS["hostName"];
}
if (isset($HTTP_GET_VARS["dbUser"])) {
    $dbUser=$HTTP_GET_VARS["dbUser"];
}
if (isset($HTTP_GET_VARS["dbUserPw"])) {
    $dbUserPw=$HTTP_GET_VARS["dbUserPw"];
}
if (isset($HTTP_GET_VARS["dbPort"])) {
    $dbPort=$HTTP_GET_VARS["dbPort"];
}
if (isset($HTTP_GET_VARS["dbSocket"])) {
    $dbSocket=$HTTP_GET_VARS["dbSocket"];
}
if (isset($HTTP_GET_VARS["dbName"])) {
    $dbName=$HTTP_GET_VARS["dbName"];
}

if (!isset($hostName)) $hostName="localhost";
if (!isset($dbUser)) $dbUser="root";
if (!isset($dbUserPw)) $dbUserPw="";
if (!isset($dbPort)) $dbPort="";
if (!isset($dbSocket)) $dbSocket="";
if (!isset($dbName)) $dbName="gorum";

function installMain(&$s)
{
    global $lll;
    global $applName, $HTTP_GET_VARS, $HTTP_COOKIE_VARS;
    global $hostName,$dbUser,$dbUserPw,$dbName,$dbPort,$dbSocket;
    global $scriptName, $emailAccount;

    $s="";
    showInstallHeader($s1);
    $s.=$s1;
    if (!isset($HTTP_COOKIE_VARS["globalUserId"]))
    {
        mt_srand((double)microtime()*1000000);
        $randomId = (int)mt_rand();
        setcookie("globalUserId", $randomId,Loginlib_ExpirationDate);
    }
    if (isset($HTTP_GET_VARS["edit"])) {
        $s.=showEditForm(TRUE);
        return;
    }
    
    //if( isset($HTTP_GET_VARS["submit"]) && 
    //    $HTTP_GET_VARS["submit"]==$lll["install"] )
    //{


    //check php4    
    if (ereg("^4",phpversion())) {
        iPrint($lll["php4ok"],"ok",$sp);
        $s.=$sp;
    }
    else {
        iPrint($lll["php4nok"],"err",$sp);
        $s.=$sp;
        return nok;
    }
    //check file creation
    $ret=checkFileCreate();
    if ($ret==ok) {
        iPrint($lll["create_file_ok"],"ok",$sp);
        $s.=$sp;
        $createconf=TRUE;
    }
    else {
        if (!isset($HTTP_GET_VARS["confirm"])) {
            iPrint($lll["create_file_nok_ext"],"warn",$sp);
        }
        else iPrint($lll["create_file_nok"],"warn",$sp);
        $s.=$sp;
        $createconf=FALSE;
    }

    //check mysql connection
    $db->hostName=$hostName;
    $db->user=$dbUser;
    $db->password=$dbUserPw;
    $db->port=$dbPort;
    $db->socket=$dbSocket;
    
    $connectRet=checkMysql($db,$s1);
    $s.=$s1;
    if ($connectRet==ok) {
        iPrint($lll["mysql_found"],"ok",$sp);
        $s.=$sp;
        $connectok=TRUE;
        $pwok=TRUE;
    }
    elseif ($connectRet==mysql_access_denied) {
        iPrint($lll["mysql_found"],"ok",$sp);
        $s.=$sp;
        if ($dbUserPw=="") {
            iPrint(sprintf($lll["need_pw"],$dbUser),
                   "warn",$sp);
            $s.=$sp;
            $s.=showEditForm();
            return ok;
        }
        else {
            iPrint(sprintf($lll["incorr_pw"],$dbUser),
                   "warn",$sp);
            $s.=$sp;
            $s.=showEditForm();
            return ok;
        }
    }
    else {
        iPrint($lll["mysql_not_found"],"warn",$sp);
        $s.=$sp;
        $s.=showEditForm(TRUE);
        return ok;
    }
    if (!isset($HTTP_GET_VARS["confirm"])) {
        $s.=showAskConfirm();
        return ok;
    }

    if (isset($HTTP_COOKIE_VARS["globalUserId"])) {
        iPrint($lll["cookieok"],"ok",$sp);
        $s.=$sp;
    }
    else {
        iPrint($lll["cookienok"],"err",$sp);
        $s.=$sp;
        return;
    }

    //check if db exists
    $ret=mysql_select_db($dbName);
    if ($ret) {
        iPrint(sprintf($lll["db_installed"], $applName, $dbName),"ok",$sp);
        $s.=$sp;
    }
    else {
        $ret=createDb();
        if ($ret!=ok) {
            $s1=sprintf($lll["cantcreatedb"],$dbUser);
            iPrint($s1,"warn",$sp);
            $s.=$sp;
            return ok;
        }
        else {
            iPrint(sprintf($lll["db_created"],$applName,$dbName),
                   "ok",$sp);
            $s.=$sp;
        }
    }
    $ret=installCreateTables();
    if ($ret!=ok) {
        iPrint(sprintf($lll["inst_create_table_err"],$applName),"err",$sp);
        $s.=$sp;
        return $ret;
    }
    else {
        iPrint(sprintf($lll["tables_installed"], $applName),"ok",$sp);
        $s.=$sp;
    }
    $ret=fillTables($sp);
    $s.=$sp;
    if ($ret!=ok) {
        iPrint($lll["fill_table_err"],"err",$sp);
        $s.=$sp;
        return $ret;
    }
    else {
        iPrint(sprintf($lll["tables_filled"], $applName),"ok",$sp);
        $s.=$sp;
    }

    if ($createconf) {//config file can be generated
        $ret=writeConfigFile($s1);
        if ($ret!=ok) {
            $s.=$s1;
            return;
        }
    }
    else {//config can't be created
        iPrint($lll["compare_conf"],"warn",$sp);
        $s.=$sp;
        showConfFileHtml($s1);
        $s.=$s1;
        iPrint($lll["afterwrconf"],"warn",$s1);
        $s.=$s1;
    }
    iPrint($lll["move_inst_file"],"warn",$s1);
    $s.=$s1;
    iPrint(sprintf($lll["congrat"], $applName),"hurra",$s1);
    $s.=$s1;
    if( $emailAccount ) iPrint($lll["inst_ch_pw_withEmailAccount"],"warn",$s1);
    else iPrint($lll["inst_ch_pw"],"warn",$s1);
    $s.=$s1;


    //send him to the application:
    $s.="<a href='$scriptName'>".sprintf($lll["inst_click"],
                                         $applName)."</a>";
    return ok;
}

function checkMysql($db,&$s)
{
    $db->host=$db->hostName;
    if ($db->port!="") $db->host.=":".$db->port;
    if ($db->socket!="") $db->host.=":".$db->socket;
    ob_start();
    mysql_connect($db->host,$db->user,$db->password );
    $errMsg = ob_get_contents();
    ob_end_clean();
    $s="";
    if (strstr($errMsg,"Unknown MySQL Server Host")) {
        return mysql_host_error;
    }
    if (strstr($errMsg,"Access denied")) {
        return mysql_access_denied;
    }
    if (strstr($errMsg,"Can't connect")) {
        return mysql_connect_error;
    }
    return ok;
}

function createDb()
{
    global $dbName;
    
    $query="CREATE DATABASE $dbName";
    $result=mysql_query($query);
    if ($result==0) {
        return nok;
    }
    else {
        return ok;
    }
}

function installCreateTables()
{
    global $dbName;
    global $dbClasses;


    $ret = mysql_select_db($dbName);
    if (!$ret) {
        return general_mysql_error;
    }    
    foreach( $dbClasses as $class ) 
    {
        $object = new $class;
        $ret = createTable($object);
        if ($ret!=ok) {
            return nok;
        }    
    }
    return ok;         
}

function fillTables(&$s)
{
    global $lll, $registrationType, $emailAccount;
    
    //make installer administrator
    createFirstAdmin();
    if( $emailAccount ) iPrint($lll["admin_ok_withEmailAccount"],"ok",$sp);
    else iPrint($lll["admin_ok"],"ok",$sp);
    $s=$sp;
    global $FLOOD;
    if (isset($FLOOD)) {
        for($i=0;$i<50;$i++) {
            $f = new Flood;
            $f->flood=(string)$i;
            create($f);
        }
    }
    if( $registrationType==User_emailCheck && class_exists("notification") )
    {
        $n = new Notification;
        $n->id = Notification_initialPassword;
        $n->title = "Sent to the user after the registration, contains the initial password";
        $n->subject="Initial password";
        $n->body="You have successfully registered in our system. Your initial password is:\n\n\$pwd\n\nYou can log in under:\n\$url\n\nIt is recommended that you change your password after the first login.";
        $n->variables="pwd, url";
        $n->active=TRUE; 
        create($n);
        
        $n = new Notification;
        $n->id = Notification_remindPassword;
        $n->title = "Contains a new password if the user forgot the old one.";
        $n->subject="New password";
        $n->body="Your login name is: \$name<br>\nYour new password is: \$pwd<br>\nClick <a href='\$url'>here</a> to activate the password, than try to log in!<br>\n<br>\nIt is recommended that you change your password afterwards.";
        $n->variables="name, pwd, url";
        $n->active=TRUE; 
        create($n);
    }
    appFillTables();
}

function createTable($base)
{
    global $lll;
    global $infoText;
    
    $typ = $base->getTypeInfo();
    getCreateTableQuery( $typ, get_class($base), $query );
    $result=mysql_query($query);
    if( $result==0 ) 
    {
        $txt = mysql_error();
        $infoText = $txt;
        return general_mysql_error;
    }
    return ok;
} 

function getCreateTableQuery( $typ, $className, &$query )
{
    global $applName;
    
    $query = "CREATE TABLE  $applName"."_$className ( ";
    $firstField = TRUE;
    foreach( $typ["attributes"] as $attribute=>$attrInfo )
    {   
        if( in_array("no column", $attrInfo) ) continue;
        if( !$firstField ) $query.=", ";
        $firstField = FALSE;
        //if( $attrInfo["type"]=="DATE" ) $query .= "  $attribute INT";
        //else $query .= "  $attribute ".$attrInfo["type"];
        $query .= "  $attribute ".$attrInfo["type"];
        if( ereg("INT", $attrInfo["type"]) &&isset($attrInfo["length"]))
        {
            $query.="(".$attrInfo["length"].")";
        }
        else if( ereg("CHAR", $attrInfo["type"]) &&
                 isset($attrInfo["max"]) )
        {
            $query.="(".$attrInfo["max"].")";
        }
        if( isset($attrInfo["default"]) && $attrInfo["type"]!="TEXT") 
        {
            $query.=" DEFAULT '".$attrInfo["default"]."'";
        }
        $query.=" NOT NULL";
        if( in_array("auto increment", $attrInfo) ) 
        {
            $query.=" AUTO_INCREMENT";
        }
    }
    if( isset($typ["primary_key"]) )
    {
        $query.=getKeySectionForCreateTableQuery($typ["primary_key"],
                                                 "PRIMARY KEY" );
    }
    if( isset($typ["unique_keys"]) )
    {
        $query.=getKeySectionForCreateTableQuery($typ["unique_keys"],
                                                 "UNIQUE" );
    }
    if( isset($typ["keys"]) )
    {
        $query.=getKeySectionForCreateTableQuery($typ["keys"],
                                                 "KEY" );
    }
    $query.=" )";
    if( in_array("heap", $typ) ) $query.=" TYPE=HEAP";
    if( isset($typ["select"]) ) $query.=" ".$typ["select"];
    $query.=";";
    return $query;
}

function getKeySectionForCreateTableQuery( $keys,$primaryOrKeyOrUnique)
{
    
    $query = "";
    if( $primaryOrKeyOrUnique=="PRIMARY KEY" )
    {
        $query.=",   $primaryOrKeyOrUnique ";
        if( is_string($keys) )  // the simplest case
        {
            $query.="($keys)";
        }
        else
        {
            $innerFirstField = TRUE;
            foreach( $keys as $attrOrIndex=>$attrOrLength )
            {
                if( $innerFirstField )
                {
                    $query.="(";
                    $innerFirstField = FALSE;
                }
                else $query.=",";
                if( is_numeric($attrOrIndex) ) $query.=$attrOrLength;
                else $query.="$attrOrIndex($attrOrLength)";
            }
            $query.=")";
        }
    }
    else
    {
        if( is_string($keys) )  // the simplest case
        {
            $query.=",   $primaryOrKeyOrUnique ($keys)";
        }
        else
        {
            foreach( $keys as $keyOrIndex=>$keyOrLength )
            {
                $query.=",   $primaryOrKeyOrUnique ";
                if( is_numeric($keyOrIndex) )
                {
                     if( is_string($keyOrLength))//the simplest case
                     {
                        $query.="($keyOrLength)";
                     }
                     else
                     {
                         $innerFirstField = TRUE;
                         foreach( $keyOrLength as 
                                  $attrOrIndex=>$attrOrLength )
                         {
                             if( $innerFirstField )
                             {
                                 $query.="(";
                                 $innerFirstField = FALSE;
                             }
                             else $query.=",";
                             if( is_numeric($attrOrIndex) )
                             {
                                 $query.=$attrOrLength;
                             }
                             else
                             {   
                                 $query.= "$attrOrIndex($attrOrLength)";
                             }
                        }
                        $query.=")";
                    }
                }
                else $query.="$keyOrIndex($keyOrLength)";
            }
        }
    }
    return $query;
}

function showInstallHeader(&$s)
{
    $s="";
    $s.="<HEAD>\n";
    $s.="<STYLE TYPE='text/css'>\n";
    $s.="body {text-align:center;}\n";
    $s.="h1 {color:#669999;}\n";
    $s.="table.edit {color:darkblue;background-color:lightblue;}\n";
    $s.="td.edit {color:darkblue;}\n";
    $s.=".err {color:red; }\n";
    $s.=".ok {color:green; }\n";
    $s.=".warn {color:#ff6600; }\n";
    $s.=".hurra {color:darkblue; }\n";
    $s.=".msg {color:#669999; }\n";
    $s.="-->\n";
    $s.="</STYLE>\n";
    $s.="</HEAD>\n";
    $s.="<BODY BGCOLOR='ffffff'>\n";
}

function iPrint($txt,$style,&$s)
{
    $s="";
    $s.="<b>";
    $s.="<span class='$style'>";
    $s.=$txt;
    $s.="</span>";
    $s.="</b>\n<br>";
}

function uninstallMain(&$s)
{
    global $dbName,$dbUser,$dbUserPw, $hostName;    


    $s="";
    if (!isset($dbName)) {
        iPrint($s1,"err",$sp);
        $s.=$sp;
        $txt="dbName not set";
        handleError($txt);
    }
    showInstallHeader($s1);
    $s.=$s1;
    $s.="<h1>Uninstall</h1>";
    connectDb($hostName, $dbUser, $dbUserPw, $dbName);
    $query="DROP DATABASE $dbName";
    $result=executeQuery($query);
    iPrint("Db dropped","ok",$sp);
    $s.=$sp;
    iPrint("Uninstall successful","hurra",$sp);
    $s.=$sp;
    return ok;    
}
function checkFileCreate()
{
    global $configFileName;
    
    $fN="./$configFileName";
    $content=@file($fN);
    $f=@fopen($fN,"w");
    if ($f) {
        if (is_array($content)) {
            foreach($content as $row) fwrite($f,$row);
        }
        return ok;
    }
    else {
        return nok;
    }
}
function generHiddens()
{
    global $hostName,$dbUser,$dbUserPw,$dbName,$dbPort,$dbSocket;
    $s="";
    $s.="<input type='hidden' name='dbUser'".
        " value='$dbUser'>\n";
    $s.="<input type='hidden' name='dbUserPw'".
        " value='$dbUserPw'>\n";
    $s.="<input type='hidden' name='dbName'".
        " value='$dbName'>\n";
    $s.="<input type='hidden' name='hostName'".
        " value='$hostName'>\n";
    $s.="<input type='hidden' name='dbSocket'".
        " value='$dbSocket'>\n";
    $s.="<input type='hidden' name='dbPort'".
        " value='$dbPort'>\n";
    return $s;
}
function showAskConfirm()
{
    global $lll;
    global $applName, $HTTP_GET_VARS, $HTTP_COOKIE_VARS;
    global $hostName,$dbUser,$dbUserPw,$dbName,$dbPort,$dbSocket;
    global $scriptName;

    $s="";
    iPrint($lll["inst_params"],"ok",$s1);
    $s.=$s1;
    $s.="<table border='1'>";
    $s.="<table border='0'><tr><td>";
    $s.="<pre><strong>";
    $s.=$lll["mysqluser"].":$dbUser<BR>";
    $s.=$lll["dbHostName"].":$hostName<BR>";
    $s.=$lll["dbDbName"].":$dbName<BR>";
    if ($dbSocket!="") {
        $s.=$lll["dbSocket"].":$dbSocket<BR>";
    }
    if ($dbPort!="") {
        $s.=$lll["dbPort"].":$dbPort<BR>";
    }
    $s.="</strong></pre>";
    $s.="</td></tr></table>\n";
    $s.="<form method='get' action='install.php'>\n";
    $s.=generHiddens();
    $s.="<input type='submit' name='edit' value='".
        $lll["edit_params"]."'>";
    $s.="<br><br>\n";
    $s.="<input type='submit' name='confirm' value='".
        $lll["install"]."'>";
    $s.="</form>\n";
    return $s;
}
function showEditForm($all=FALSE)
{
    global $HTTP_GET_VARS;
    global $hostName,$dbUser,$dbUserPw,$dbName,$dbPort,$dbSocket;
    global $lll;

    $s="";
    $length=30;
    $s.="<center>\n";
    $s.="<table border='0' class='edit' cellpadding='4'".
         " cellspacing='1'>\n";
    $s.="<form method='get' action='install.php'>\n";
    $s.="<tr class='edit'>\n";
    $s.="<th class='edit' colspan='2'>".$lll["formtitle"]."</th>";
    $s.="</tr>\n";
    $s.="<tr class='edit'>\n";
    $s.="<td class='edit'>".$lll["mysqluser"]."</td>\n";
    $s.="<td class='edit'>";
    $s.="<input type='text' length='$length' name='dbUser'".
         " value='$dbUser'>";
    $s.="</td>\n";
    $s.="</tr>\n";
    $s.="<tr class='edit'>\n";
    $s.="<td class='edit'>".$lll["password"]."</td>\n";
    $s.="<td class='edit'>";
    $s.="<input type='password' length='$length' name='dbUserPw'".
         " value='$dbUserPw'>";
    $s.="</td>\n";
    $s.="</tr>\n";
    if ($all) {
        $s.="<tr class='edit'>\n";
        $s.="<td class='edit'>".$lll["dbName"]."</td>\n";
        $s.="<td class='edit'>";
        $s.="<input type='text' length='$length' name='dbName'".
             " value='$dbName'>";
        $s.="</td>\n";
        $s.="</tr>\n";
        $s.="<tr class='edit'>\n";
        $s.="<td class='edit'>".$lll["hostName"]."</td>\n";
        $s.="<td class='edit'>";
        $s.="<input type='text' length='$length' name='hostName'".
             " value='$hostName'>";
        $s.="</td>\n";
        $s.="</tr>\n";
        $s.="<tr class='edit'>\n";
        $s.="<td class='edit'>".$lll["dbPort"]."</td>\n";
        $s.="<td class='edit'>";
        $s.="<input type='text' length='$length' name='dbPort'".
             " value='$dbPort'>";
        $s.="</td>\n";
        $s.="<tr class='edit'>\n";
        $s.="<td class='edit'>".$lll["dbSocket"]."</td>\n";
        $s.="<td class='edit'>";
        $s.="<input type='text' length='$length' name='dbSocket'".
             " value='$dbSocket'>";
        $s.="</td>\n";
        $s.="</tr>\n";
    }
    else {
        $s.="<input type='hidden' name='dbName'".
             " value='$dbName'>";
        $s.="<input type='hidden' name='hostName'".
             " value='$hostName'>";
        $s.="<input type='hidden' name='dbPort'".
             " value='$dbPort'>";
        $s.="<input type='hidden' name='dbSocket'".
             " value='$dbSocket'>";
    }
    $s.="<tr class='edit'>\n";
    $s.="<th class='edit' colspan='2'>";
    $s.="<input type='submit' value='OK'>";
    $s.="</th>";
    $s.="</tr>\n";
    $s.="</form>\n";
    $s.="</table>\n";
    $s.="</center>\n";
    return $s;
}
function showConfFile(&$s)
{
    global $hostName,$dbUser,$dbUserPw,$dbName,$dbPort,$dbSocket;

    $s="";
    $s.="<"."?php\n";
    $s.="\$dbUser=\"$dbUser\";\n";
    $s.="\$dbUserPw=\"$dbUserPw\";\n";
    $s.="\$dbName=\"$dbName\";\n";
    $s.="\$hostName=\"$hostName\";\n";
    if (isset($dbPort)) {
        $s.="\$dbPort=\"$dbPort\";\n";
    }
    if (isset($dbSocket)) {
        $s.="\$dbSocket=\"$dbSocket\";\n";
    }
    $s.="\n";
    $s.='$dbHost=$hostName;'."\n";
    $s.='if ($dbPort!="") $dbHost.=":".$dbPort;'."\n";
    $s.='if ($dbSocket!="") $dbHost.=":".$dbSocket;'."\n";
    $s.="?".">\n";
}
function writeConfigFile(&$s)
{
    global $configFileName,$lll;

    $s="";
    $f=@fopen("./$configFileName","w+");
    if ($f==0) {
        $s.=$lll["conf_file_write_err"];
        return nok;
    }
    showConfFile($sc);
    fwrite($f,$sc);
    fclose($f);
    return ok;
}
function showConfFileHtml(&$s)
{
    $s="";
    showConfFile($s1);
    $s.="<pre style='text-align:left;background-color:#eeeeee'>".
        "<strong>".htmlspecialchars($s1)."</strong></pre>";
}
?>
