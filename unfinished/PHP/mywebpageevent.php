<?php
for ($i=0;$i<count($items);++$i){
   $udm[$i]=explode("=",$items[$i]);
   if ($udm[$i][0]=="email") $epastas=trim($udm[$i][1]);
   if ($udm[$i][0]=="name") $vardas=trim($udm[$i][1]);
   if ($udm[$i][0]=="url") $url=trim($udm[$i][1]);
   if ($udm[$i][0]=="event") $ivykis=($udm[$i][1]);
   if ($udm[$i][0]=="comments") $komentarai=($udm[$i][1]);
} 
   if (trim($url)=="") $url="http://";
 
if ($komanda==""){
	print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
	print('Svetainës Ávykiai');
	print('</div>');
	print('<form name="frmx" method="get" action="index.php">');
	print('<p align="left">');
	print('&nbsp; <nobr> Jeigu norite praneðti ir kitiems, apie savo svetainës ávyká, uþpildykite ðià anketà:');
	print('<br><br>');
	print('<table width="530" border="0" cellspacing="0" cellpadding="2">');
	print('<input type="hidden" name="site" value="mywebpageevent">');
	print('<input type="hidden" name="action" value="sendemail">');
	print('<tr> <td nowrap  align="right" valign="top">E-Paðtas:</td><td nowrap  align="left" valign="middle">');
	print("<input type=\"text\" name=\"email\" size=\"30\" value=\"$epastas\">");
	print('</td></tr><tr><td nowrap  align="right" valign="top">Savininko vardas/slapyvardis: </td><td nowrap  align="left" valign="middle"> ');
	print("<input type=\"text\" name=\"name\" size=\"30\" value=\"$vardas\">");
	print('</td></tr><tr> <td nowrap  align="right" valign="top">Puslapio adresas:</td><td nowrap align="left" valign="middle">');
	print("<input type=\"text\" name=\"url\" value=\"$url\" size=\"30\"></td>");
	print('</tr><tr><td nowrap align="right" valign="top">Ávykis: </td><td nowrap  align="left" valign="middle">');
	print(' <select name="event"><option value="Sukurta nauja svetainë" selected>Sukurta nauja svetainë</option><option value="Atnaujintas svetainës turinys">Atnaujintas svetainës ');
	print(' turinys</option><option value="Naujas svetainës dizainas">Naujas svetainës dizainas</option><option value="Pasikeitë svetainës adresas">Pasikeitë svetainës adresas');
	print('</option>');
	print('</select>');
	print('</td></tr><tr><td nowrap  align="right" valign="top">Komentarai apie ávyká:</td>');
	print('<td nowrap  align="left" valign="middle">');
	print('<textarea name="comments" wrap="VIRTUAL" rows="15" cols="30">');
	print($komentarai);
	print('</textarea></td></tr><tr> <td nowrap  align="right" valign="top">&nbsp;</td><td nowrap  align="left" valign="middle"> ');
	print('<input type="submit" name="Submit" value="Siøsti"></td></tr></table></nobr>');
	print('</form>');
}
else {

	 $truksta=false;
	 $truksta=$truksta||($epastas=="");
	 $truksta=$truksta||($vardas=="");
	 $truksta=$truksta||($url=="");
	 $truksta=$truksta||($ivykis=="");

	 if ($truksta) {
		print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">Svetainës Ávykiai</div>');
		print('<p>&nbsp;Deja, ne visos anketos dalys buvo uþpildytos. <br>&nbsp;Jeigu norite gauti ðià paslaugà, turite uþpildyti anketà. <div align="right">');
		print('<a href="');
		print("index.php?site=mywebpageevent&email=$epastas&name=$vardas&url=$url&comments=$komentarai&event=$ivykis");
		print('">');
		print('Atgal prie anketos pildymo</A></div>');
	 }
	 else { 	 	 	 	 
		 $txt[0]="Elektroninis paðtas: $epastas";
		 $txt[1]="Savininko vardas/slapyvardis: $vardas";
		 $txt[2]="Svetainës adresas: $url";
		 $txt[3]="Ávykis: $ivykis";
		 $txt[4]="Komentarai: $komentarai";
		 $text=join(" <br> ", $txt);
 		 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">Svetainës Ávykiai</div>');
		 print('<p>&nbsp;Aèiû uþ uþpildytà anketà. Jûsø anketa bus perþiûrëta ir pagal jos duomenis bus paskelbta þinutë naujienø skyrelyje.<br>');
 		 mail("mekdrop@omni.lt", "21 Int: Mano puslapio ávykis", $text);
	}	 
}
?>