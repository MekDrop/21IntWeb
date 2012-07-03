<?php
error_reporting(63);
$f=@fopen("config.php","r");
if ($f) {
    include("./config.php");
    include("./constants.php");
    include("./gorum/gorum.php");
    include("./include.php");
    gorumMain($s);
    echo $s;
}
else {
echo <<< EOF
<HEAD>
<META HTTP-EQUIV='Refresh' CONTENT='3;url=install.php'>
</HEAD>
<BODY BGCOLOR='white'>
It seems that Zorum is not installed on your system.
<br><br>
You will be forwarded to the installation process.
<br><br>
If your browser doesn't forward you, please click
<a href='install.php'>here</a>.
</BODY>
EOF;
}
?>
