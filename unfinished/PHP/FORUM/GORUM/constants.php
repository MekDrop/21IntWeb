<?php
define( "Loginlib_NewUser", 1 );
define( "Loginlib_GuestLevel", 2 );
define( "Loginlib_BasicLevel", 3 );
define( "Loginlib_LowLevel", 4 );
define( "Loginlib_ExpirationDate", 2000000000 );

define( "User_simpleReg", 1 );
define( "User_emailCheck", 2 );
define( "User_adminApproval", 3 );
define( "User_checkNumber", 4 ); // meg nem implementalt

//A SZAMOKAT NE VALTOZTASD!!!
//AZ APPLICATION constants.php direktben hasznalja oket!
define( "Explanation_no", 0 );
define( "Explanation_text", 1 );
define( "Explanation_qhelp", 2 );
define( "Explanation_popup", 3 );

define( "Priv_load", 1 );
define( "Priv_modify", 2 );
define( "Priv_delete", 4 );
define( "Priv_create", 8 );
define( "Priv_insert", 16 );
define( "Priv_multipledelete", 32 );

define( "Form_visible", 1 );
define( "Form_hidden", 2 );
define( "Form_invisible", 3 );
define( "Form_readonly", 4 );

define("now",time());

define("search_all",1);
define("search_any",2);

define("Init_register",1);
define("Init_login",2);
define("Init_loginDifferent",3);
define("Init_cangePwd",4);
define("Init_logout",5);
define("Init_myProfile",6);
define("Init_myItems",7);
define("Init_addItem",8);
define("Init_recentItems",9);
define("Init_popularItems",10);
define("Init_search",11);
define("Init_home",12);
define("Init_settings",13);
define("Init_modStyle",14);
define("Init_userList",15);
define("Init_badWords",16);
define("Init_activeItems",17);
define("Init_inactiveItems",18);
define("Init_cronjobs",19);
define("Init_notifications",20);
define("Init_addCategory",21);
define("Init_modCategory",22);
define("Init_delCategory",23);

// Notificationok (az intallibben ilyen id-vel letre kell hozni a megf.
// notification objektumokat):
define("Notification_initialPassword", 1);
define("Notification_remindPassword", 2);

if (!isset($maxInputLength)) $maxInputLength=40;
if (!isset($maxFieldLength)) $maxFieldLength=250;

if( !isset($showIcon) ) $showIcon = TRUE;
if( !isset($autoLogout) ) $autoLogout = FALSE;
if( !isset($autoLogoutTime) ) $autoLogoutTime = 0;
if( !isset($boxWidthFrame) ) $boxWidthFrame = FALSE;
if( !isset($boxShadow) ) $boxShadow = FALSE;
if( !isset($showChangePassword) ) $showChangePassword = FALSE;
if (!isset($vertSpacer)) $vertSpacer="10";
if (!isset($saveSearchSupport)) $saveSearchSupport=FALSE;
if (!isset($chAdmAct)) $chAdmAct=FALSE;
if (!isset($enableCsvExport)) $enableCsvExport=FALSE;
if (!isset($htmlNotifications)) $htmlNotifications=FALSE;
if (!isset($pwRemind)) $pwRemind=TRUE;
if (!isset($emailAccount)) $emailAccount=FALSE;

// Ha a globalsettings nincs beinkludalva, akkor a kovetkezo resz
// inicializalja a globalis valtozokat:
if (!isset($necessaryAuthLevel))$necessaryAuthLevel=Loginlib_BasicLevel;
if (!isset($blockSize)) $blockSize = 100000;
if (!isset($rangeBlockSize)) $rangeBlockSize = 10;
if (!isset($headTemplate)) $headTemplate = "";
if (!isset($upperTemplate)) $upperTemplate = "<body>\n";
if (!isset($lowerTemplate)) $lowerTemplate = "</body>";
if (!isset($textAreaRows)) $textAreaRows = 10;
if (!isset($textAreaCols)) $textAreaCols = 50;
if (!isset($showExplanation)) $showExplanation = Explanation_qhelp;
if (!isset($minPasswordLength)) $minPasswordLength = 1;
if (!isset($dateFormat)) $dateFormat = "d.m.Y H:i";
if (!isset($htmlTitle)) $htmlTitle = "";
if (!isset($htmlKeywords)) $htmlKeywords = "";
if (!isset($htmlDescription)) $htmlDescription = "";
if (!isset($adminEmail)) $adminEmail = "system@system.system";
if (!isset($language)) $language = "en";
if (!isset($registrationType)) $registrationType = User_simpleReg;

if( !isset($userClassName) ) $userClassName = "user";
if( !isset($initClassName) ) $initClassName = "init";
if( !isset($groupClassName) ) $groupClassName = "group";
if( !isset($categoryClassName) ) $categoryClassName = "category";
if( !isset($itemClassName) ) $itemClassName = "item";
if( !isset($searchClassName) ) $searchClassName = "search";
if( !isset($cookiePath) ) $cookiePath = "";
?>
