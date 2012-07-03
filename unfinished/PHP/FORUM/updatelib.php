<?php

$versionHistory = array(
    "3_0",
    "3_1",
    "3_2",
    "3_3"
);    
 
function updateMain(&$s) 
{
    global $lll;
    global $submit,$HTTP_GET_VARS;
    global $versionHistory;
    global $dbName,$dbUser,$dbUserPw, $hostName;    
    
    $s="";
    showInstallHeader($s1);
    $s.=$s1;
    $s.="<h1>".$lll["u_maintitle"]."</h1>\n";
    connectDb($hostName, $dbUser, $dbUserPw, $dbName);
    
    $toVersion = $versionHistory[sizeof($versionHistory)-1];
    if (isset($HTTP_GET_VARS["submit"])) {
        $submit=$HTTP_GET_VARS["submit"];
    }
    if( !isset($submit) )
    {
        iPrint($lll["secure_copy"],"warn",$sp);
        $s.=$sp;
        $s.="<br><br>";
        iPrint(sprintf($lll["ready_to_update"], $dbName, $toVersion),
               "ok",$sp);
        $s.=$sp;
        $s.="<form method='get' action='update.php'>\n";
        $s.="<input type='submit' name='submit' value='".
            $lll["ok"]."'>";
        $s.="<input type='submit' name='submit' value='".
            $lll["cancel"]."'>";
        $s.="</form>\n";
    }
    else if( $submit==$lll["cancel"] )
    {
        iPrint($lll["operation_cancelled"],"ok",$sp);
        $s.=$sp;
    }
    else if( $submit==$lll["ok"] )
    {
        $fgs = new GlobalStat;
        $fgs->id=1;
        load($fgs, "", "instver");
        update( $s1, $dbName, $fgs->instver, $toVersion );
        $s.=$s1;
        $s.="<br><br><a href='index.php'>".$lll["backToForum"]."</a>";
    }
}
    
function update( &$s, $dbName, $fromVersion, $toVersion="Latest" )
{
    global $versionHistory;
    global $lll;

    $s="";
    $length = sizeof($versionHistory);
    if( $toVersion=="Latest" ) $toVersion = $versionHistory[$length-1];
    if( !in_array( $fromVersion, $versionHistory) ||
        !in_array( $toVersion, $versionHistory) )
    {
        iPrint(sprintf($lll["invalid_version"],"$fromVersion, $toVersion"),"err",$sp);
        $s.=$sp;
        return ok;
    }  
    if( $fromVersion==$toVersion )
    {
        iPrint(sprintf($lll["already_installed"], $toVersion),"ok",$sp);
        $s.=$sp;
        return ok;
    } 
    $fromIndex = getIndex($fromVersion);
    $toIndex   = getIndex($toVersion);
    if( $toIndex < $fromIndex )
    {
        iPrint(sprintf($lll["invalid_version"],"$fromVersion, $toVersion"),"err",$sp);
        $s.=$sp;
        return ok;
    }
    for( $i=$fromIndex; $i<$toIndex; $i++ )
    {
        $fromVer = $versionHistory[$i];
        $toVer   = $versionHistory[$i+1];
        $functionName = "update_".$fromVer."_to_".$toVer;
        if( function_exists($functionName) )
        {
            $functionName($s1, $dbName);
            $s.=$s1;
        }
    }
    
    // Updating the version number in forumglobalstat:
    $query = "UPDATE zorum_globalstat".
             " SET instver='$toVersion' WHERE id='1'";
    $result=executeQuery($query);    
    iPrint($lll["updateSuccessful"],"ok",$sp);
    $s.=$sp;
             
}

function getIndex( $version )
{   
    global $versionHistory;
    foreach( $versionHistory as $index=>$ver )
    {
        if( $ver==$version ) return $index;
    }
}
function update_3_0_to_3_1(&$s, $dbName)
{
    global $lll;
    iPrint(sprintf($lll["updating"],"3_0", "3_1"),"ok",$s1);
}
function update_3_1_to_3_2(&$s, $dbName)
{
    global $lll;
    iPrint(sprintf($lll["updating"],"3_1", "3_2"),"ok",$s1);
}
function update_3_2_to_3_3(&$s, $dbName)
{
    global $lll;
    iPrint(sprintf($lll["updating"],"3_2", "3_3"),"ok",$s1);
}
?>
