<?php

$prodDir="prod";
system("rm -rf $prodDir");
system("mkdir $prodDir");
system("mkdir $prodDir/lang");
system("mkdir $prodDir/gorum");
system("mkdir $prodDir/gorum/lang");
system("cp ../*.css $prodDir");
system("cp ../lang/*.php $prodDir/lang/");
system("cp lang/*.php $prodDir/gorum/lang/");


//application php
system("ls -1 ../*.php >t1.txt");
system("grep -v config.php t1.txt | grep -v uninstall.php >t2.txt");
$f=fopen("t3.txt","w");
$a=file("t2.txt");
for($i=0;isset($a[$i]);$i++) {
    fwrite($f,substr($a[$i],3,strlen($a[$i])-3));
}
fclose($f);
$a=file("t3.txt");
for($i=0;isset($a[$i]);$i++) {
    $a[$i]=trim($a[$i]);
    compressFile("../".$a[$i],$a[$i]);
}
//gorum php
system("ls -1 *.php | grep -v version.php >t1.txt");
$a=file("t1.txt");
for($i=0;isset($a[$i]);$i++) {
    $a[$i]=trim($a[$i]);
    compressFile($a[$i],"gorum/".$a[$i]);
}
system("grep -v trace.php $prodDir/gorum/gorum.php >t1.txt");
system("cp t1.txt $prodDir/gorum/gorum.php");
//images dir
system("mkdir $prodDir/i");
system("cp ../i/* $prodDir/i");


system("rm -f t1.txt t2.txt t3.txt");

echo "Vege\n";


function makeCompressedFiles($fileName)
{
    $a=file($fileName);
    for($i=0;isset($a[$i]);$i++) {
        $a[$i]=trim($a[$i]);
        compressFile($a[$i],$a[$i]);
    }
}
function compressFile($inFileName,$outFileName)
{
    global $prodDir;

    echo "Compress: $inFileName to $outFileName\n";
    $rows=file($inFileName);
    $f=fopen("$prodDir/$outFileName","w");
    foreach($rows as $row) {
        //$row=ereg_replace("^.*trc.*$","",$row);
        $row=ereg_replace("\r\n","\n",$row);//dos newline
        //$row=ereg_replace("//.*$","\n",$row);//comments
        //$row=trim($row);//cut spaces
        //$row=ereg_replace("^ *(\r\n|\n|\r)$","",$row);//empty rows
        fwrite($f,$row);
        //if ($row!="") fwrite($f,"\n");
    }
    fclose($f);
}

?>
