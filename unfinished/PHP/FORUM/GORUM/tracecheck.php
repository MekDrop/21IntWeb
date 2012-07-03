<?php
$fp = fopen("trace/n","r");
$fileid = fread($fp,10);
$fname="trace/$fileid";
$f=file($fname);
$i=1;
echo "checking file: $fname\n";
foreach($f as $l) {
    $l = trim($l);
    $s = explode("#",$l);
    if (!isset($s[4])) {
        echo "error at line: $i: $l\n";
        continue;
    }
    if (!$s[2]) {
        echo "error at line: $i: $l\n";
        continue;
    }
    $i++;
    if ($s[1]=="ENTRY") {
        if (!isset($open[$s[0]])) $open[$s[0]]=0;
        $open[$s[0]]++;
        $fv[]=$s[0];
    }
    if ($s[1]=="EXIT") {
        if (!isset($close[$s[0]])) $close[$s[0]]=0;
        $close[$s[0]]++;
        $fv[]=$s[0];
    }
}
foreach($fv as $key) {
    if (!isset($close[$key])) {
        echo "No close for $key\n";
        continue;
    }
    if ($open[$key]!=$close[$key]) {
        echo "open: $key : $open[$key], close: $close[$key]\n";
    }
}
echo "processed $i lines\n";
?>
