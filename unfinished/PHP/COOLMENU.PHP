<?
 $sritis=$HTTP_GET_VARS["site"];
 $komanda=$HTTP_GET_VARS["action"]; 
 @($vnt=$HTTP_GET_VARS["item"]); 
 @($meniu=$HTTP_GET_VARS["menu"]); 
 $le=$meniu;
 if (($meniu=="")||($meniu=="-")||($meniu=="undefined")) $le="default";

 $file="Data/menu/$le.lst";
 $data=file($file);

 for ($i=0;$i<count($data);++$i){
   $subdata=explode("=",$data[$i]);
   $le2=trim($subdata[1]);
   $file="Data/menu/$le2.lst";
   $MenuItems[$i]=$subdata[0];
   if (file_exists($file)) {
	   $MenuIcons[$i]="Images/MenuIcons/fo1.gif";
  	   if ($MenuItems[$i]=="..") $MenuIcons[$i]="Images/MenuIcons/fo2.gif";
       $MenuLinks[$i]="index.php?site=$sritis&action=$komanda&item=$vnt&menu=$le2";
   } else {
       $MenuIcons[$i]="Images/MenuIcons/fi1.gif";
	   $subdata=explode("_",$le2);
       @($MenuLinks[$i]="index.php?site=$subdata[0]&action=$subdata[1]&vnt=$subdata[2]&menu=$meniu");
   }

   print("<A class=\"nuorodanepakeista\" href=\"$MenuLinks[$i]\">\n");
   print("<div id=\"MenuItem$i\" class=\"meniufx-normalus\" onMouseOver=\"MM_changeProp('MenuItem$i','','style.backgroundColor','$MeniuUzvestaSpalva','DIV')\" onMouseOut=\"MM_changeProp('MenuItem$i','','style.backgroundColor','$MeniuNormaliSpalva','DIV')\">\n");
   print("&nbsp;<img src=\"$MenuIcons[$i]\" width=\"16\" height=\"16\" align=\"absmiddle\" border=\"0\">\n");
   print("$MenuItems[$i]\n</div></a>\n");
}
?>