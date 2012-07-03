<?php
error_reporting(63);

if (isset($HTTP_GET_VARS["finderror"])) {
    $finderror=$HTTP_GET_VARS["finderror"];
}

$trdepthcol[]="#EEEEEE";
$trdepthcol[]="#DDDDDD";
$trdepthcol[]="#CCCCCC";
$trdepthcol[]="#BBBBBB";
$trdepthcol[]="#AAAAAA";
$trdepthcol[]="#999999";
$trdepthcol[]="#777777";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
$trdepthcol[]="#555555";
function removeStr(&$from,$this)
{
    $pos = strpos ($from,$this);
    if ($pos === false) { // note: three equal signs
        return;
    }
    $t1=substr($from,0,$pos);
    $pos+=strlen($this);
    $t2=substr($from,$pos,strlen($from)-strlen($this));
    $from=$t1.$t2;
    return;
}
function removeChar(&$s,$c)
{
    $pos = strpos ($s,$c);
    if ($pos === false) { // note: three equal signs
        return;
    }
    $t1=substr($s,0,$pos);
    $pos++;
    $t2=substr($s,$pos,strlen($s));
    $s=$t1.$t2;
    return;
}
if (!isset($entryonly)) $entryonly=0;
if (!isset($nolog)) $nolog=0;
if (!isset($maxdepth)) $maxdepth=0;
if (!isset($finderror)) $finderror=0;
if (!isset($depthcolors)) $depthcolors=1;
if (!isset($del)) $del=0;
//if ($finderror>-1) {
    //TODO:Ezeket el kellene menteni, a formba visszairni.
    $maxdepth=20;
    if (!isset($depthcolors)) $depthcolors=1;
    $entryonly=0;
//}
echo "<head><script language=\"Javascript\">\n<!--";
echo "\nfunction init() {\n".
     //" document.foo.onid.value=id\n".
     "\n}";
echo "\nfunction setMaxDepth(val) {\n".
     " document.foo.maxdepth.value=eval(document.foo.maxdepth.value)".
     "+eval(val)\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction setDepthColors(val) {\n".
     " document.foo.depthcolors.value=val\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction setNoLog(val) {\n".
     " document.foo.nolog.value=val\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction delFiles(val) {\n".
     " document.foo.del.value=val\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction setFileId(val) {\n".
     " document.foo.fileid.value=val\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction setFindError(val) {\n".
     " document.foo.finderror.value=val\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction setEntryOnly(val) {\n".
     " document.foo.entryonly.value=val\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction onTrId(id) {\n".
     " document.foo.onid.value=id\n".
     " document.foo.submit()\n".
     "\n}";
echo "\nfunction offTrId(id) {\n".
     " document.foo.offid.value=id\n".
     " document.foo.submit()\n".
     "\n}";
echo "\n//--></script>";
echo "<STYLE TYPE='text/css'>\n<!--";
echo "\n--></STYLE>\n";
echo "</head>\n";
echo "<body onload='javascript:init()'>\n";
//onids1: a bekapcsolt traceid-ek elso helyiertekein levo betuk
//onid: az epp bekapcsolt traceid
//offid: az epp kikapcsolt traceid
//if (isset($onid)) echo "onid:$onid\n<BR>";
//else echo "onid not set\n<BR>";
//echo "\n";
$querystr="";
//define("f_id",0);
define("f_method",0);
define("f_entry",1);
define("f_file",2);
define("f_line",3);
define("f_text",4);
define("f_depth",5);
$trsep="#";
$dir="trace";
if (isset($del)&&$del!=0) {
    system("rm $dir/*");
}
$fp = @fopen("$dir/n","r");
if (!isset($fileid)||$fileid==0) {
    if ($fp) {
        $fileid = fread($fp,10);
    }	
    else {
        echo "No trace files!";
        die();
    }
}
$farr = file("$dir/$fileid");
$headerlines=0;
$methodLen=0;
$fileLen=0;
$lineLen=0;
$depth=-1;
$errorNum=0;
for($i=$headerlines,$j=0;isset($farr[$i]);$i++,$j++) {
    $row[$j]=explode($trsep,$farr[$i]);
    if ($row[$j][f_entry]=="ENTRY") {
        $depth++;
        $lastentry=$j;
    }
    $row[$j][f_depth]=$depth;
    if (($row[$j][f_entry]=="EXIT") || ($row[$j][f_entry]=="ERROR")) {
        $depth--;
    }
    if ($entryonly && $row[$j][f_entry]!="ENTRY") {
        $j--;
        continue;
    }
    if ($nolog && $row[$j][f_entry]=="LOG") {
        $j--;
        continue;
    }
    if ($maxdepth<$row[$j][f_depth]) {
        $j--;
        continue;
    }
    $row[$j][f_text]=chop($row[$j][f_text]);
    if ($row[$j][f_entry]=="ERROR") {
        $errorlines[]=$j;
        $entryBeforeError[]=$lastentry;
        $errorNum++;
    }
    $l=strlen($row[$j][f_method]);
    if ($methodLen<$l) $methodLen=$l;
    $l=strlen($row[$j][f_file]);
    if ($fileLen<$l) $fileLen=$l;
    $l=strlen($row[$j][f_line]);
    if ($lineLen<$l) $lineLen=$l;
}
$rowNum=$j;
echo "Trace file num:$fileid - ";
if ($errorNum>0) echo "<STRONG>ERROR FOUND!</STRONG><BR>";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setFileId(0)'>Last Trace file</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setFileId(".($fileid-1).
     ")'>Prev Trace file</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setFileId(".($fileid+1).
     ")'>Next Trace file</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:delFiles(1)'>Delete Trace files</A>\n";
echo "\n<BR>";
if ($nolog==0) {
    echo "<A STYLE='text-decoration:none;color:red;'".
         " HREF='javascript:setNoLog(1)'>NO LOG</A>\n";
} 
else {
    echo "<A STYLE='text-decoration:none;color:green;'".
         " HREF='javascript:setNoLog(0)'>NO LOG</A>\n";
}
echo " - ";
if ($entryonly==0) {
    echo "<A STYLE='text-decoration:none;color:red;'".
         " HREF='javascript:setEntryOnly(1)'>ENTRY ONLY</A>\n";
} 
else {
    echo "<A STYLE='text-decoration:none;color:green;'".
         " HREF='javascript:setEntryOnly(0)'>ENTRY ONLY</A>\n";
}
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setDepthColors(0)'>No depth colors</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setDepthColors(1)'>Simple depth colors</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setDepthColors(2)'>Adv. depth colors</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setMaxDepth(1)'>Deeper</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setMaxDepth(-1)'>Shallower</A>\n";
echo " - ";
echo "Depth: $maxdepth\n<BR>";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setFindError(".($finderror-1).
     ")'>Prev Error</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setFindError(".($finderror+1).
     ")'>Next Error</A>\n";
echo " - ";
echo "<A STYLE='text-decoration:none;color:red;'".
     " HREF='javascript:setFindError(-1)'>Don't fint Error</A>\n";
echo " - ";
echo "<BR>";
echo "<PRE>\n";
if ($finderror>-1&&$errorNum>0) {
    $maxdepth=7;//TODO
    $depthcolors=1;
    $entryonly=1;
}
for($i=0;$i<$headerlines;$i++) echo $farr[$i];
for($i=0;$i<$rowNum;$i++) {
    if ($entryonly && $row[$i][f_entry]!="ENTRY") {
        continue;
    }
    //hibakereses, melysegbbe le, csak entry kikapcs
    if ($entryonly && $row[$i][f_entry]=="ENTRY"
        &&$finderror>-1&&$errorNum>0
        &&$entryBeforeError[$finderror]==$i) {
            $entryonly=0;
    }
    //hiba megvolt, csak entry bekapcs
    if ($row[$i][f_entry]=="ERROR"
        &&$finderror>-1&&$errorNum>0
        &&$entryBeforeError[$finderror]<$i) {
            $entryonly=1;
    }
    if ($depthcolors==2) {
        $col=$trdepthcol[0];
        for($j=0;$j<$maxdepth+1;$j++) {
            echo "<SPAN STYLE='background:$col;'>_</SPAN>";
            if ($row[$i][f_depth]>$j&&$j+1<$maxdepth+1) {
                $col=$trdepthcol[$j+1];
            }
        }
    }
    if ($depthcolors!=0) {
        echo "<SPAN STYLE='background:".
             $trdepthcol[$row[$i][f_depth]].";";
        if ($row[$i][f_entry]=="ENTRY") echo "color:#004400;";
        if ($row[$i][f_entry]=="LOG") echo "color:black;";
        if ($row[$i][f_entry]=="EXIT") echo "color:#0000AA;";
        if ($row[$i][f_entry]=="ERROR") echo "color:#AA0000;";
        echo "'>";
    }
    //if ($entryonly && $row[$i][f_entry]!="ENTRY") continue; 
    //if ($nolog && $row[$i][f_entry]=="LOG") continue; 
    //if ($maxdepth<$row[$i][f_depth]) continue;
    echo $row[$i][f_method];
    $lblanks=$methodLen-strlen($row[$i][f_method]);
    for($j=0;$j<$lblanks;$j++) echo "_";
    echo "#";
    if ($row[$i][f_entry]=="LOG") echo "__";
    if ($row[$i][f_entry]=="EXIT") echo "_";
    echo $row[$i][f_entry];
    echo "#";
    echo $row[$i][f_file];
    $fblanks=$fileLen-strlen($row[$i][f_file]);
    for($j=0;$j<$fblanks;$j++) echo "_";
    echo "#";
    $lblanks=$lineLen-strlen($row[$i][f_line]);
    for($j=0;$j<$lblanks;$j++) echo "_";
    echo $row[$i][f_line];
    echo "#";
    echo $row[$i][f_depth];
    echo "#";
    echo $row[$i][f_text];
    if ($depthcolors!=0) {
        echo "</SPAN>";
    }
    echo "\n";
}
echo "</PRE>";
if ($del==1) $del=0;
echo "<FORM ACTION='tracewatch.php' METHOD='GET' NAME='foo'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='del' VALUE='$del'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='fileid' VALUE='$fileid'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='finderror' VALUE='$finderror'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='depthcolors' VALUE='$depthcolors'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='maxdepth' VALUE='$maxdepth'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='entryonly' VALUE='$entryonly'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='nolog' VALUE='$nolog'>\n";
echo "<INPUT TYPE='HIDDEN' NAME='onid' VALUE=''>\n";
echo "<INPUT TYPE='HIDDEN' NAME='offid' VALUE=''>\n";
echo "</FORM>\n";
?>
