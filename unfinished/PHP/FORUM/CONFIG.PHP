<?php
$dbUser="root";
$dbUserPw="root";
$dbName="zorum";
$hostName="localhost";
$dbPort="";
$dbSocket="";

$dbHost=$hostName;
if ($dbPort!="") $dbHost.=":".$dbPort;
if ($dbSocket!="") $dbHost.=":".$dbSocket;
?>
