<?php
 $file='Data/ideas.lst';
 $data=file($file);
if ($komanda=='display') {
   $k=count($data);
   if ($k!=0 | !file_exists($file)){
     print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">Id�jos</div>');
     print('<p align="left">&nbsp;�iuo metu jau yra �iek tiek id�j�:');
     for ($i=0;$i<=$k;$i=$i+3){
       print('<p align="left">&nbsp;');
	   print($data[$i]);
	   print('<div align="right" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal x-small Times New Roman; font-family: Times New Roman; font-size: small; font-style: normal; font-variant: normal; color: #3399CC">');
	   print('<a href="mailto:');
	   print($data[$i+1]);
	   print('" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal x-small Times New Roman; font-family: Times New Roman; font-size: small; font-style: normal; font-variant: normal; color: #3399CC">');
	   print($data[$i+2]);
	   print('<a></div>');
	 }
   }	 
   if ($k==0){
     print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">N�ra Id�j�</div>');
     print('<p align="left">&nbsp;�iuo metu dar n�ra �ia id�j�, bet jei kam nors kils ir jas para�ys - jos tikrai �ia atsiras.');
   }
    print('<hr noshade color="gray">');
    print('<div align="right"><a href="index.php?site=ideas&action=add" class="melynanuorodabepakeitimu">Prid�ti Id�j�</a></div>');
	return;
}

if ($komanda=='add') {
   print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">Prid�ti Id�j�</div>');
   print('<p align="left">&nbsp;Kad j�s� id�ja b�t� prid�ta � id�j� s�ra��, pirmiausia u�pildykite �i� anket�:');
   print('<form action="mailto:mekdrop@omni.lt?subject=21 Int -> nauja ideja" method="post">');
   print('<table width="80%" border="0" cellspacing="0" cellpadding="2" style="border: 0pt none black; margin: 4px; ">');
   print('<tr>');
   print('<td valign="middle"><nobr>J�s� vardas/slapyvardis:</nobr></td>');
   print('<td valign="top"><input type="text" name="nick" size="44" maxlength="45"></td>');
   print('<tr>');
   print('<td valign="middle">J�s� e-pa�tas:</td>');
   print('<td valign="top"><input type="text" name="e-mail" size="44" maxlength="45"></td>');
   print('<tr>');
   print('<td valign="top">J�s� id�ja(-os):</td>');
   print('<td valign="top">');
   print('<input type="text" name="idea" size="44" maxlength="255"></textarea>');
   print('</td>');
   print('<tr>');
   print('<td valign="top" width="36%" height="33"></td>');
   print('<td valign="top" width="64%" height="33">');
   print('<input type="hidden" name="site" value="ideas">');
   print('<input type="hidden" name="action" value="senddata">');
   print('<input type="submit" value="I�si�sti"></td>');
   print('</table>');
   print('</form>');
   print("Pastaba: ra�ydami id�j�, nera�ykite joki� HTML tag'�, nes jie vistiek nebus parodyti.");
   return;
//   print('</p><p align="left">&nbsp;J�s� id�ja bus i�si�sta elektroniniu pa�tu, tod�l ji i� karto �ia neatsiras.');
}

 if ($komanda=='senddata') {
   print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">D�kojame u� id�j�</div>');
   print('<p align="left">&nbsp;Mes d�kojame Jums u� id�j�.<br>&nbsp;J�s� id�ja, jau yra patalpinta skyrelyje.');
   print('<p><div align="right"><a href="index.php?site=ideas&action=display">Paspauskite �ia, kad sugri�t�m�te � pagrindin� id�j� puslap�.</a></div></p>');
   for ($i=0;$i<count($udm);++$i){
	 if ($udm[$i][0]=="nick") $nickname=$udm[$i][1];
	 if ($udm[$i][0]=="e-mail") $email=$udm[$i][1];
	 if ($udm[$i][0]=="idea") $idea=$udm[$i][1];
   }
   $txt=strip_tags("$idea\n$nickname\n$email\n");
   $f=fopen($file, "write" );
   fwrite($f, "$txt");	  
   for ($i=0;$i<count($data);++$i){
	    $data[$i]=trim($data[$i]);
	    fwrite($f, "$data[$i]\n");
   }
   fclose($f);
//   mail('mekdrop@omni.lt', '21int: Nauja id�ja', $txt);
//   print('</p><p align="left">&nbsp;J�s� id�ja bus i�si�sta elektroniniu pa�tu, tod�l ji i� karto �ia neatsiras.');
}
?>