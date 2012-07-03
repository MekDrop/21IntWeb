<?php
error_reporting(63);
$f=fopen("config.php","r");
include("./config.php");
include("./constants.php");
include("./gorum/gorum.php");
include("./include.php");
include("./gorum/installlib.php");
include("./installlib.php");
include("./updatelib.php");
include("./gorum/lang/lang_en.php");
include("./lang/lang_en.php");
updateMain($s);
echo $s;
?>
