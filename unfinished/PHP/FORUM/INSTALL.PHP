<?php
$dbName="zorum";
include("./constants.php");
include("./gorum/gorum.php");
include("./gorum/installlib.php");
include("./installlib.php");
include("./include.php");

// Az installacio nyelve:
$language="en";
$languageFile = "lang_".$language.".php";
include("./gorum/lang/$languageFile");
include("./lang/$languageFile"); 

installMain($s);
echo $s;
?>
