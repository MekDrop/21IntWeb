<?php
function popGetMail($user,$pass,&$popErrStr,&$popmailSR)
{
 $host="localhost";
 $port="110";

 $socket = fsockopen($host,$port, $errno, $errstr);
 $popmailSR="";
 $popErrStr="";
 $eol="\r\n";
 if (!$socket) {
    $popErrStr="fsockopen failed with parameters host:$host, ".
                 "port: $port, errno: $errno, errstr: $errstr";
    return 1;
 }

 $s=fgets($socket, 4096);
 $popmailSR.=$s;
 $repCode=substr($s,0,3);
 if ($repCode!="+OK") {
    fputs($socket,"QUIT $eol");
    while(!feof($socket)) $popmailSR.=fgets($socket,4096);
    $popErrStr="Pop connection open error.<br>\n";
    return 2;
 }

 fputs($socket,"USER $user$eol");
 $s=fgets($socket, 4096);
 $popmailSR.=$s;

 fputs($socket,"PASS $pass$eol");
 $s=fgets($socket, 4096);
 $popmailSR.=$s;
 $repCode=substr($s,0,3);
 if ($repCode!="+OK") {
    fputs($socket,"QUIT $eol");
    while(!feof($socket)) $popmailSR.=fgets($socket,4096);
    $popErrStr="Pop authentication failed.<br>\n";
    return 3;
 }

 fputs($socket,"STAT $eol");
 $s=fgets($socket, 4096);
 $popmailSR.=$s;
 $repCode=substr($s,0,3);
 if ($repCode!="+OK") {
    fputs($socket,"QUIT $eol");
    while(!feof($socket)) $popmailSR.=fgets($socket,4096);
    $popErrStr="Pop command STAT failed.<br>\n$popmailSR";
    return 4;
 }

 /*
 fputs($socket,"LIST 1 $eol");
 $s=fgets($socket, 4096);
 $popmailSR.=$s;
 $repCode=substr($s,0,3);
 if ($repCode!="+OK") stopIt();

 fputs($socket,"RETR 1 $eol");
 $s=fgets($socket, 4096);
 $popmailSR.=$s;
 $repCode=substr($s,0,3);
 if ($repCode!="+OK") stopIt();
 $s="";
 while(!ereg("$eol.$eol\$",$s)) $s.=fgetc($socket);
 $popmailSR.=$s;
 $popmailSR.="<br>---<br>";
 */

 //socket_set_blocking($socket,TRUE);

 fputs($socket,"QUIT $eol");
 while(!feof($socket)) $popmailSR.=fgets($socket,4096);
 return 0;
 /*
 $reg="[[:space:]]";
 $s="1\n21";
 $a=split($reg,$s);
 */
}

function stopIt()
{
    global $socket,$eol,$popmailSR;
    fputs($socket,"QUIT $eol");
    while(!feof($socket)) $popmailSR.=fgets($socket,4096);
    echo nl2br($popmailSR);
    echo "<br>END";
    die();
}
?>
