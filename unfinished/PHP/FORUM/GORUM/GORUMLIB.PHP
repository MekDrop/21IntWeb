<?php
$allowedMethods["delete_form"]='$ret=$base->deleteForm($s);';
$allowedMethods["multipleDelete_form"]='$ret=$base->multipleDeleteForm($s);';
$allowedMethods["create_form"]='$ret=$base->createForm($s);';
$allowedMethods["modify_form"]='$ret=$base->modifyForm($s);';
$allowedMethods["create"]='$ret=$base->create(FALSE,$s);';
$allowedMethods["modify"]='$ret=$base->modify();';
$allowedMethods["delete"]='$ret=$base->delete();';
$allowedMethods["multipleDelete"]='$ret=$base->multipleDelete();';
$allowedMethods["showhtmllist"]='$ret=$base->showHtmlList($s);';
$allowedMethods["sort"]='$ret=makeSort();';
$allowedMethods["range"]='$ret=makeRange();';
$allowedMethods["showdetails"]='$ret=$base->showDetails($s,"id");';
$allowedMethods["remind_password_form"]='$ret=$base->generForm($s);';
$allowedMethods["change_password_form"]='$ret=$base->changePasswordForm($s);';
$allowedMethods["remind_password"]='$ret=$base->remindPassword();';
$allowedMethods["login_form"]='$ret=$base->loginForm($s);';
$allowedMethods["login"]='$ret=$base->lowLevelLogin($s);';
$allowedMethods["logout"]='$ret=logout($s);';
$allowedMethods["activate_new_password"]='$ret=$base->activateNewPassword($s);';
$allowedMethods["change_password"]='$ret=$base->changePassword($s);';
$allowedMethods["edit_group_form"]='$ret=$base->editGroupForm($s);';
$allowedMethods["edit_group"]='$ret=$base->editGroup();';
$allowedMethods["changeAdmStatus"]='$ret=$base->changeAdmStatus();';
$allowedMethods["pophelp"]='showPopupHelp();';
$allowedMethods["showcsv"]='$ret=$base->csvExport();';
$allowedMethods["update"]='update($s);';

if (!isset($fixCss)) $dbClasses[]="stylesheet";
if (isset($FLOOD)) $dbClasses[]="flood";
$dbClasses[]=$userClassName;

function gorumMain(&$s)
{
    global $gorumroll,$lll,$whatHappened,$infoText,$gorumcontent;
    global $initClassName,$navBarText;
    global $upperTemplate,$lowerTemplate;    
    
    $s="";
    $init = new $initClassName;
    $init->initializeSystemSettings();    
    $base = new $gorumroll->class;
    if( $gorumroll->submit==$lll["cancel"] )
    {
        $whatHappened="form_submitted";
        $infoText = $lll["operation_cancelled"];
        goBackward($base);
        processMethod($base, $processResult);
    }
    else
    {
        $whatHappened="";
        processMethod($base, $processResult);
        if( $whatHappened ) // Ez jelzi, hogy meg egy processMethod kell
        {
            goBackward($base);
            processMethod($base, $processResult);
        }
    }
    //show functions
    $gorumcontent="";
    $navBarText=$base->showNavBar();//TODO: a navbar altalanos cucc?
    $gorumcontent.= $processResult;
    $s.=$init->showHtmlHead();
    $upperTemplate=trim($upperTemplate);
    if (ereg("\.php$",$upperTemplate)) {
        $ret=@include($upperTemplate);
        if (!$ret) {
            $infoText = sprintf($lll["incl_header_err"],$upperTemplate);
        }
    }
    else $s.="$upperTemplate\n";
    $lowerTemplate=trim($lowerTemplate);
    if (ereg("\.php$",$lowerTemplate)) {//just check
        $ret=@fopen($lowerTemplate,"r");
        if (!$ret) {
            $infoText = sprintf($lll["incl_footer_err"],$lowerTemplate);
        }
    }

    $s.=$init->showApp();
    if (ereg("\.php$",$lowerTemplate)) $ret=@include($lowerTemplate);
    else $s.="$lowerTemplate\n";
    //$s.=showStatusEcho();
}

function processMethod(&$base, &$s)
{
    global $allowedMethods,$gorumroll;

    $s="";
    if (!isset($allowedMethods[$gorumroll->method])) {
        $txt="Method is not allowed: $gorumroll->method";
        handleError($txt);
    }
    // Ha kivulrol globalban az osztaly attributumainak megfelelo
    // valtozok jonnek, inicializaljuk veluk az objektumot:
    initClassVars($base);
    set_magic_quotes_runtime(0);
    $methodCall = $allowedMethods[$gorumroll->method];
    eval($methodCall);
        
}

function initClassVars(&$base,$initWithNull=FALSE)
{
    global $HTTP_GET_VARS,$HTTP_POST_VARS,$HTTP_COOKIE_VARS;
    global $gorumroll;
    $typ = $base->getTypeInfo();
    foreach( $typ["attributes"] as $attr=>$val ) 
    {
        /*
        if (isset($_REQUEST[$attr])) { 
            if (get_magic_quotes_gpc()) {
                $base->{$attr}=stripslashes($_REQUEST[$attr]);
            }
            else $base->{$attr}=$_REQUEST[$attr];
        }
        */
        if (isset($HTTP_GET_VARS[$attr])) { 
            if (get_magic_quotes_gpc()) {
                $base->{$attr}=stripslashes($HTTP_GET_VARS[$attr]);
            }
            else $base->{$attr}=$HTTP_GET_VARS[$attr];
        }
        elseif (isset($HTTP_POST_VARS[$attr])) { 
          if (is_array($HTTP_POST_VARS[$attr])) {
            $base->{$attr} = array();
            foreach($HTTP_POST_VARS[$attr] as $keykey=>$valval) {
                if (get_magic_quotes_gpc()) {
                    $base->{$attr}[$keykey]=stripslashes($valval);
                }
                else $base->{$attr}[$keykey]=$valval;
            }
          }
          else {
            if (get_magic_quotes_gpc()) {
                $base->{$attr}=stripslashes($HTTP_POST_VARS[$attr]);
            }
            else $base->{$attr}=$HTTP_POST_VARS[$attr];
          }
        }
        elseif (isset($HTTP_COOKIE_VARS[$attr])) { 
            if (get_magic_quotes_gpc()) {
                $base->{$attr}=stripslashes($HTTP_COOKIE_VARS[$attr]);
            }
            else $base->{$attr}=$HTTP_COOKIE_VARS[$attr];
        }
        if(!isset($base->{$attr}) && $initWithNull) {
            $base->{$attr} = "";
        }
        // Ez azert kell, hogy a bool attributum FALSE erteke is
        // atjojjon a formbol:
        if(!isset($base->{$attr}) && in_array("bool", $val) && 
           $base->getVisibility($typ, $attr)==Form_visible &&
           !strstr($gorumroll->method, "_form")) {
            $base->{$attr} = FALSE;
        }
        // Ez azert kell, hogy a multipleselection attributum 
        // ures erteke is atjojjon a formbol:
        if(!isset($base->{$attr}) && (in_array("multipleselection", $val) ||
           in_array("multipleclassselection", $val)) && 
           $base->getVisibility($typ, $attr)==Form_visible &&
           !strstr($gorumroll->method, "_form")) {
            $base->{$attr} = array();
        }
        
    }
}

function handleError($error="")
{
    if ($error=="mysql") echo mysql_error();
    else echo $error;
    die();
}

function generBoxUp($tableWidth="100%",$cp="2",$line="line",
                    $boxframe="boxframe")
{
    global $initClassName;
    $init = new $initClassName;
    return $init->generBoxUp($tableWidth,$cp,$line,$boxframe);
}

function generBoxDown()
{
    global $initClassName;
    $init = new $initClassName;
    return $init->generBoxDown();
}

function goBackward(&$base)
{
    global $gorumroll, $whatHappened, $lll;
    $oldList = $gorumroll->list;
    if( $whatHappened=="invalid_form" )
    {
        $gorumroll->method .= "_form";
        if( !isset($gorumroll->invalid) || !$gorumroll->invalid )
        {
            $gorumroll->invalid = TRUE; 
        }    
    }
    elseif( $whatHappened=="form_submitted" )
    {
        // Pl. ha a details page-rol csinaltak deletet, akkor nem a 
        // details page-re kell visszamenni, mert olyan objektum mar
        // nincs, hanem a details-et megelozo dologra (pl. egy listre)
        if( $gorumroll->submit==$lll["ok"] && 
            $gorumroll->method=="delete" &&
            $gorumroll->fromfrommethod )
        {
            restoreFromFrom();
        }
        else
        {
            restoreFrom();
        }
    }
    else
    {
        handleError("Invalid whatHappened: $whatHappened");
    }
    if( $gorumroll->list != $oldList || 
        // ha egy formbol egy masik osztaly formjaba akarunk ugrani, 
        // akkor a gorumroll->list-et, class-ot, method-ot atirjuk, es
        // a whatHappened-et invalid_form-ra allitjuk. Ekkor a base-t
        // ujra kell new-sni, mert megvaltozott a class. Pl. a 
        // topic create_form-bol a poll create_form-ba ugras.
        get_class($base)!=$gorumroll->class) 
    {
        // A class-ot a list-bol szarmaztatjuk:
        $slices = explode("_", $gorumroll->list);
        $gorumroll->class = $slices[0];
        if( !class_exists($gorumroll->class) )
        {
            global $defaultMethod;
            global $defaultList;
            global $defaultRollId;
            $gorumroll->list = $defaultList;
            $slices = explode("_", $gorumroll->list);
            $gorumroll->class = $slices[0];
            //$gorumroll->class = $gorumroll->list = $defaultList;
            $gorumroll->method = $defaultMethod;
            $gorumroll->rollid = $defaultRollId;
        }
        $base = new $gorumroll->class;
    }
    return ok;
}
function vertSpacer($height=0)
{
    global $xi,$vertSpacer;
    if ($height==0) $height=$vertSpacer;
    $s="<table border='0' cellpadding='0' cellspacing='0'><tr><td>".
       "<img src='$xi/b.gif' height='$height'></td></tr></table>\n";
    return $s;
}

?>
