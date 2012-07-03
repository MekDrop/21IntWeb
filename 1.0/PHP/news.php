<?php
 $newsfile='Data/news.lst';
 $newsdata=file($newsfile);
// phpinfo();
 for ($i=0;$i<=count($newsdata);$i=$i+3){
   print("<div align=left style='text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color:#0000CC'>$newsdata[$i]</div>");
   print("<p align=left>&nbsp;");
   print($newsdata[$i+1]);
   print("<div align=right style='text-decoration: none; text-shadow: navy 1em 1em; font: normal normal x-small Times New Roman; font-family: Times New Roman; font-size: small; font-style: normal; font-variant: normal; color: #0099CC'>");
   print($newsdata[$i+2]);
   print("</div>");
 }
 exit;
?>