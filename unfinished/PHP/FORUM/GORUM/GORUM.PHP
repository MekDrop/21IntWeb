<?php
if (!isset($xi)) $xi="i";
if (!isset($trcPattern)) $trcPattern="...";
include("./gorum/constants.php");
include("./gorum/loginlib.php");
include("./gorum/gorumlib.php");
include("./gorum/object.php");
include("./gorum/form.php");
include("./gorum/generformlib.php");
include("./gorum/roll.php");
include("./gorum/dbproperty.php");
include("./gorum/privilegeproperty.php");
include("./gorum/htmllist.php");
include("./gorum/tools.php");
include("./gorum/showdetails.php");
if (!isset($fixCss)) include("./gorum/stylesheet.php");
if (isset($FLOOD)) include("./gorum/flood.php");
switch( $registrationType )
{
    case User_simpleReg:
        include("./gorum/user_simple.php");
        break;
    case User_emailCheck:
        include("./gorum/user_email.php");
        break;
}        
include("./gorum/init.php");

define("ok",0);
define("nok",1);
define("already_in_db",3);
define("permission_denied",5);
define("deep_struct",10);
define("no_father",11);
define("input_param",12);
define("general_mysql_error",51);
define("not_found_in_db",52);
define("cannot_connect_to_server",53);
define("cannot_create_db",54);
define("select_db_failed",55);
define("wrong_object",101);
define("no_such_user",1102);
define("mysql_host_error",1301);
define("mysql_access_denied",1302);
define("mysql_connect_error",1303);
?>
