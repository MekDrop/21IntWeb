<?php
function zmail($from,$to,$subject,$body,$html=0,$vfrom="",$vto="",$vcc="",$vdate="",$host="localhost",$port="25",$client="")
{
    global $zmailErrStr,$zmailSR;
    global $HTTP_SERVER_VARS;
    global $rcptErrArr;

    $zmailErrStr="";
    $zmailSR="";
    $eol="\r\n";
    //socket open
    $socket = fsockopen($host,$port, $errno, $errstr);
    if (!$socket) {
        $zmailErrStr="fsockopen failed with parameters host:$host, ".
                     "port: $port, errno: $errno, errstr: $errstr";
        return 1;
    }
    //socket_set_blocking($socket,TRUE);
    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="220") {
        $zmailErrStr="The server response was not 220, mail server is".
                     " unavailable.";
        return 2;
    }
    //HELO
    /*
    if ($client=="" && isset($HTTP_SERVER_VARS["HTTP_HOST"]) &&
        $HTTP_SERVER_VARS["HTTP_HOST"])
    {
        $client=$HTTP_SERVER_VARS["HTTP_HOST"];
    }
    if ($client=="" && isset($HTTP_SERVER_VARS["SERVER_NAME"]) &&
        $HTTP_SERVER_VARS["SERVER_NAME"])
    {
        $client=$HTTP_SERVER_VARS["SERVER_NAME"];
    }
    if ($client=="") $client="zorum";
    */
    $client="mailgust";

    fputs($socket,"HELO $client$eol");
    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="250") {
        $zmailErrStr="The server response to the HELO request was not".
                     " 250.";
        return 3;
    }
    //MAIL
    fputs($socket,"MAIL from: $from$eol");
    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="250") {
        $zmailErrStr="The server response to the MAIL request was not".
                     " 250.";
        return 4;
    }
    //RCPT
    if (is_array($to)) {
        foreach($to as $t) {
            fputs($socket,"RCPT TO: $t$eol");
            $s=fgets($socket, 4096);
            $zmailSR.=$s;
            $repCode=substr($s,0,3);
            if ($repCode!="250" && $repCode!="251") {
                $rcptErrArr[]=$t;
            }
            else $rcptOk=1;
        }
    }
    else {
        fputs($socket,"RCPT TO: $to$eol");
        $s=fgets($socket, 4096);
        $zmailSR.=$s;
        $repCode=substr($s,0,3);
        if ($repCode!="250" && $repCode!="251") {
            $rcptErrArr[]=$to;
        }
        else $rcptOk=1;
    }
    //RCPT - CC
    if ($vcc && is_array($vcc)) {
        foreach($vcc as $cc) {
            fputs($socket,"RCPT TO: $cc$eol");
            $s=fgets($socket, 4096);
            $zmailSR.=$s;
            $repCode=substr($s,0,3);
            if ($repCode!="250" && $repCode!="251") {
                $rcptErrArr[]=$cc;
            }
            else $rcptOk=1;
        }
    }
    if ($vcc && is_string($vcc)) {
        fputs($socket,"RCPT TO: $vcc$eol");
        $s=fgets($socket, 4096);
        $zmailSR.=$s;
        $repCode=substr($s,0,3);
        if ($repCode!="250" && $repCode!="251") {
            $rcptErrArr[]=$vcc;
        }
        else $rcptOk=1;
    }
    if (!isset($rcptOk)) {
        $zmailErrStr="The server response to the RCPT request".
                     " was not 250 or 251.";
        return 5;
    }
    //DATA
    fputs($socket,"DATA$eol");    
    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="354") {
        $zmailErrStr="The server response to the DATA request was not".
                     " 354.";
        return 6;
    }
    //head
    fputs($socket,"Subject: $subject$eol");
    if ($vfrom) fputs($socket,"From: $vfrom$eol");    
    if ($vto) fputs($socket,"To: $vto$eol");    
    else {
        if (is_array($to)) {
            foreach($to as $t) {
                fputs($socket,"To: $t$eol");    
            }
        }
        else fputs($socket,"To: $to$eol");    
    }
    if ($vcc && is_array($vcc)) {
        foreach($vcc as $cc) {
            fputs($socket,"Cc: $cc$eol");
        }
    }
    if ($vcc && is_string($vcc)) {
        fputs($socket,"Cc: $vcc$eol");
    }
    if ($vdate) fputs($socket,"Date: $vdate$eol");    

    //body
    $body=chop($body);
    $body=str_replace("\n.","\n..",$body);
    if ($html) {
        fputs($socket,"MIME-Version: 1.0$eol");
        $boundary = uniqid("emailwind");
        fputs($socket,"Content-Type: multipart/alternative;".
                      " boundary=\"$boundary\"$eol$eol");
        //plain
        fputs($socket,"--$boundary$eol");
        fputs($socket,"Content-Type: text/plain;$eol");
        fputs($socket,"Content-Transfer-Encoding: 8bit$eol$eol");
        //fputs($socket,"Content-Transfer-Encoding: base64$eol$eol");
        //$bod=chunk_split(base64_encode(strip_tags($body)));
        $bod=strip_tags($body);
        fputs($socket,"$bod$eol");
        //html
        fputs($socket,"--$boundary$eol");
        fputs($socket,"Content-Type: text/html;$eol");
        fputs($socket,"Content-Transfer-Encoding: 8bit$eol$eol");
        //fputs($socket,"Content-Transfer-Encoding: base64$eol$eol");
        //$bod=chunk_split(base64_encode($body));
        $bod=$body;
        fputs($socket,"$bod$eol");
        fputs($socket,"--$boundary--$eol");
    }
    else fputs($socket,"$body$eol");//plain mail

    fputs($socket,".$eol");

    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="250") {
        $zmailErrStr="The server response to the message was not".
                     " 250.";
        return 7;
    }


    //fputs($socket, "RSET$eol"); 
    fputs($socket, "QUIT$eol");

    while(!feof($socket)) $zmailSR.=fgets($socket,4096);
    
    return 0;
}


function zmail2($from,$to,$subject,$body,$vfrom="",$vto="",$vcc="",$vdate="",$host="localhost",$port="25",$client="")
{
    global $zmailErrStr,$zmailSR;
    global $HTTP_SERVER_VARS;
    global $rcptErrArr;
    $zmailErrStr="";
    $zmailSR="";
    $eol="\r\n";
    //socket open
    $socket = fsockopen($host,$port, $errno, $errstr);
    if (!$socket) {
        $zmailErrStr="fsockopen failed with parameters host:$host, ".
                     "port: $port, errno: $errno, errstr: $errstr";
        return 1;
    }
    //socket_set_blocking($socket,TRUE);
    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="220") {
        $zmailErrStr="The server response was not 220, mail server is".
                     " unavailable.";
        return 2;
    }
    //HELO
    if ($client=="" && isset($HTTP_SERVER_VARS["HTTP_HOST"]) &&
        $HTTP_SERVER_VARS["HTTP_HOST"])
    {
        $client=$HTTP_SERVER_VARS["HTTP_HOST"];
    }
    if ($client=="" && isset($HTTP_SERVER_VARS["SERVER_NAME"]) &&
        $HTTP_SERVER_VARS["SERVER_NAME"])
    {
        $client=$HTTP_SERVER_VARS["SERVER_NAME"];
    }
    if ($client=="") $client="zorum";

    fputs($socket,"HELO $client$eol");
    $s=fgets($socket, 4096);
    $zmailSR.=$s;
    $repCode=substr($s,0,3);
    if ($repCode!="250") {
        $zmailErrStr="The server response to the HELO request was not".
                     " 250.";
        return 3;
    }
    $j=0;
    foreach($to as $t) {
        $j++;
        //MAIL
        fputs($socket,"MAIL from: $from$eol");
        $s=fgets($socket, 4096);
        $zmailSR.=$s;
        $repCode=substr($s,0,3);
        if ($repCode!="250") {
            $zmailErrStr="The server response to the MAIL request was".
                         " not 250.";
            return 4;
        }
        //RCPT
        fputs($socket,"RCPT TO: $t$eol");
        $s=fgets($socket, 4096);
        $zmailSR.=$s;
        $repCode=substr($s,0,3);
        if ($repCode!="250" && $repCode!="251") {
            $rcptErrArr[]=$t;
            $continue;
        }
        else $rcptOk=1;

        //DATA
        fputs($socket,"DATA$eol");    
        $s="";
        $s=fgets($socket, 4096);
        $zmailSR.=$s;
        $repCode=substr($s,0,3);
        if ($repCode!="354") {
            $zmailErrStr="The server response to the DATA request was not".
                         " 354.";
            return 6;
        }
        fputs($socket,"Subject: $subject$eol");
        if ($vfrom) fputs($socket,"From: $vfrom$eol");    
        if ($vto) fputs($socket,"To: $vto$eol");    
        if ($vcc && is_array($vcc)) {
            foreach($vcc as $cc) {
                fputs($socket,"Cc: $cc$eol");
            }
        }
        if ($vcc && is_string($vcc)) {
            fputs($socket,"Cc: $vcc$eol");
        }
        if ($vdate) fputs($socket,"Date: $vdate$eol");    

        $sendbody=str_replace("\n.","\n..",$body);
        $sendbody.=$j;
        fputs($socket,"$sendbody$eol");

        fputs($socket,".$eol");

        $s=fgets($socket, 4096);
        $zmailSR.=$s;
        $repCode=substr($s,0,3);
        if ($repCode!="250") {
            $zmailErrStr="The server response to the message was not".
                         " 250.";
            return 7;
        }
        //fputs($socket, "RSET$eol"); 
    }
    if (!isset($rcptOk)) {
        $zmailErrStr="The server response to the RCPT request".
                     " was not 250 or 251.";
        return 5;
    }
    fputs($socket, "QUIT$eol");

    while(!feof($socket)) $zmailSR.=fgets($socket,4096);
    
    return 0;
}
function gmail($from,$to,$subject,$body,$html=0,$vfrom="",$vto="",$vcc="",$vdate="",$host="localhost",$port="25",$client="")
{
    global $zmailOk,$zmailHost;
    if (isset($zmailOk) && $zmailOk) {
        //echo "ZMAIL<br>";
        if (isset($zmailHost) && $zmailHost) $host=$zmailHost;
        $ret=zmail($from,$to,$subject,$body,$html,$vfrom,$vto,$vcc,
                   $vdate,$host,$port,$client);
        //TODO: ret...
    }
    else {
        //echo "ORIG<br>";
        $header="";
        $eol="\r\n";
        if ($vfrom) $header.="From: $vfrom$eol";
        elseif ($from) $header.="From: $from$eol";
        if ($vcc) $header.="Cc: $vcc$eol";
        if ($vto) $header.="To: $vto$eol";
        if ($html) {
            $header.="MIME-Version: 1.0$eol";
            $header.="Content-Type: text/html; charset=ISO-8859-1$eol" .
            $header.="Content-Transfer-Encoding: base64$eol";
            $body= chunk_split(base64_encode($body));
        }
        $ret=mail($to,$subject,$body,$header);
        //$ret=!$ret;
        $ret=ok;
        //TODO: ret...
    }
    return $ret;
}
/*
function gmails($from,$to,$subject,$body,$vfrom="",$vto="",$vcc="",$vdate="",$host="localhost",$port="25",$client="")
{
    global $zmailOk,$zmailBlockSize;

    if (isset($zmailOk) && $zmailOk) {
        $tos = array();
        $i=1;
        foreach($to as $t) {
            $tos[]=$t;
            if ($i % $zmailBlockSize == 0) {
                $ret=zmail($from,$tos,$subject,$body,$vfrom,$vto,$vcc,
                           $vdate,$host,$port,$client);
                $tos = array();
            }
            $i++;
        }
        if (isset($tos[0])) {
            $ret=zmail($from,$tos,$subject,$body,$vfrom,$vto,$vcc,
                       $vdate,$host,$port,$client);
        }
    }
    else {
        $header="";
        $eol="\r\n";
        $header.="From: $from$eol";
        $tos="";
        $i=1;
        foreach($to as $t) {
            if ($tos) $tos.=",";
            $tos.=$t;
            if ($i % $zmailBlockSize == 0) {
                $ret=mail($tos,$subject,$body,$header);
                $tos="";
            }
            $i++;
        }
        if ($tos) {
            $ret=mail($tos,$subject,$body,$header);
        }
    }
}
*/
/*
include("stopwatch.php");
$sw = new Stopwatch;

$from="zorum@zorum.com";
$subj="valami";
$vfrom="kuka@itt";
$vcc="";
//$vdate="23 NOV 02 11:12:13 UT";
$vdate="";
$body = <<< EOF
Szevasz!

Ez egy level.
..
.

Regards,
Bela
EOF;
//$to[]="nagyz@zorum.com";
//$to[]="nagyzzorum.com";
//$to[]="zorum@zorum.com";
$sw->start();
//$ret=zmail($from,$to,$subj,$body,$from,$to,$vcc,$vdate);
$to="test1@phpoutsourcing.com";
for($i=0;$i<20;$i++) $ret=mail($to,$subj,$body,"From: $from");
$sw->stop();
echo "Elapsed time:".$sw->elapsed()."<br>";
if (isset($rcptErrArr)) {
    foreach($rcptErrArr as $e) {
        echo "rcpt err: $e<br>";
    }
}
if ($ret) echo "$zmailErrStr<br>";
else echo "<br>zmail returned with ok<br>";
echo "<br>".nl2br($zmailSR)."<br>";
echo "<br>END";
*/
/*
Ido teszt eredmenye:

levelszam   zmail2  zmail   mail
1           0.06    0.06    0.06
10          0.4     0.06    0.5
20          0.8     0.1     0.95


*/
?>
