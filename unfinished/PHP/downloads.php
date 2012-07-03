<?php
 if ($komanda=="showitem"){
   	 $vnt++;
     $file="Data/downloads/$vnt.lst";
	 $data=file($file);
	 $pavadinimas=$data[1];
	 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
	 print($pavadinimas);
	 print("</div>");
//	 print('<div style="color: #CC3399; font: x-small san serif; font-family: san serif; font-size: x-small">Atsisiuntimai</div></div>');
	 print("<p>");
	 print("<table border=0 width=160>");
	 print("<tr>");
	 print("<td valign=top align=left width=160>");
	 print("<table border=0 bordercolor=white cellpadding=0 cellspacing=0 cols=1 frame='void'>");
	 
	 if (file_exists("Images/Others/$vnt-small.jpg"))
	    $picture="<img border=0 width=160 height=120 src='Images/Others/$vnt-small.jpg'>";
	 else
 	    $picture='<table border=0 height=100% width=100%><tr align=center valign=middle><td>Nëra pieðinio<td></tr></table>';
		 
	 print("<tr style='background: infobackground; background-color: infobackground;'><td colspan=2><div style='width: 160px; height: 120px; background: infobackground; background-color: infobackground;'>$picture</div></td></tr>");
	 print('<tr><td align=left style="background: rgb(100%,90%,80%)" height="14" nowrap><a href="');
	 print($data[5]);
	 print('"><div style="text-decoration: none; color: blue; font-family: sans-serif; font-size: 12px;">&nbsp;<Img border="0" align="middle" src="Images/Downloads/save.gif">&nbsp;Atsisiøsti</div></a></td>');
	 print('<td align=right style="background: rgb(80%,90%,90%)" height="14" nowrap><a href="');
	 print("index.php?site=showimage&action=$vnt-big.jpg");
	 print('"><div style="text-decoration: none; color: blue; font-family: sans-serif; font-size: 12px;">Perþiûrëti&nbsp;<Img border="0" align="middle" src="Images/Downloads/view.gif">&nbsp;</div></a></td></tr>');
	 print("</table>");	 
     print("</td><td valign=top align=left>");
 	 
	 $style='<div style="color: #006600; font-family: sans-serif; font-size: 12px;">';
	 $endstyle='</div>';
	 
 	 print("<table border=0 align=left>");
	 print("<tr><td>$style Pavadinimas:$endstyle</td><td nowrap>$style$pavadinimas$endstyle</td></tr>");
	 
	 for ($i=0;$i<10;++$i){
	    $data[$i]=trim($data[$i]);
	    if ($data[$i]=="") $data[$i]="-";
	}
		 
 	 if (!($data[2]=="-")) print("<tr><td>$style Autorius:$endstyle</td>\n<td nowrap>$style$data[2]$endstyle</td></tr>");
	 if (!($data[3]=="-")) print("<tr><td>$style Metai:$endstyle</td>\n<td nowrap>$style$data[3]$endstyle</td></tr>");
 	 if (!($data[4]=="-")) print("<tr><td>$style Svetainë:$endstyle</td>\n<td nowrap>$style<a href='$data[4]'>$data[4]</a>$endstyle</td></tr>");
	 if (($data[6]=="-")) $data[6]="nemokama";
	 
	 print("<tr><td>$style Kaina:$endstyle</td><td nowrap>$style$data[6]$endstyle</td></tr>");
	 print("<tr><td>$style Paketo tipas:$endstyle</td><td nowrap>$style$data[7]$endstyle</td></tr>");
	 print("<tr><td>$style Failo dydis:$endstyle</td><td nowrap>$style$data[9]$endstyle</td></tr>");
	 print("<tr><td>$style Ávertinimas:$endstyle</td><td nowrap>$style$data[8]$endstyle</td></tr>");	 
     print("</table>");
	 print("</td></table>");
	 print("<p>$data[0]</p>");
}	 
 else {
	 $file='Data/downloads.lst';
	 $data=file($file);
	 for ($i=0;$i<count($data);++$i)
	   $failai[$i]=explode("\\",$data[$i]);
 
	 $rex=explode("\\",$komanda);
 
	 $pavadinimas="";
	 if ($komanda=='display') $pavadinimas="Pagrindinis Puslapis";
	 $pozicija=count($rex);
	 $pozicija=$pozicija-1;
	 if ($pavadinimas=='') $pavadinimas=$rex[$pozicija];
	 if ($pavadinimas=="") $pavadinimas="Bloga komanda";
 
	 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
	 print($pavadinimas);
	 print("</div>");
//	 print('<div style="color: #CC3399; font: x-small san serif; font-family: san serif; font-size: x-small">Atsisiuntimai</div></div>');

	 if ($komanda=='display')
    	 $paieskoszona=0;
	 else 
    	 $paieskoszona=count($rex);
	 
	 print('<p align="left">&nbsp;Pasirinkite failà/kategorijà:');
	 print("\n");
   
	 print('<table border="0">');
	 print("\n");   
   
	 if ($paieskoszona>1){
		$v=0;
		for ($i=0;$i<count($failai);++$i){
			 
			 $did=false;
			 for ($k=0;$k<$i;++$k)
			      @($did=$did | ($failai[$i][$paieskoszona]==$failai[$k][$paieskoszona]));
			 
			 if (($failai[$i][$paieskoszona-1]==$rex[count($rex)-1]) && (!$did)){
			   print("\n<tr>\n<td width='50px' align='center'>\n");
		 	   print($v+1);
			   print(".\n</td>\n<td>\n");
			   print('<div id="Downloads');
			   print($v);
			   print('" style="width:100%;height:20px;cursor: pointer;color:#099FFF" onMouseOver="MM_changeProp(\'Downloads');
			   print($v);
			   print('\',\'\',\'style.color\',\'blue\',\'DIV\')" onMouseOut="MM_changeProp(\'Downloads');
			   print($v);
			   print('\',\'\',\'style.color\',\'#099FFF\',\'DIV\')"');
			   print("\n");
			   print(' OnClick="GoTo(\'index.php?site=downloads&action=');
			   print("showitem&item=$i");
			   print('\')">'); 
			   print($failai[$i][$paieskoszona]);
			   print("\n");	  
			   print('</div></td>');
			   ++$v;
			 }
	}
	}
	else {
	    $v=0;
		for ($i=0;$i<count($failai);++$i){

 		 $did=false;
		 for ($k=0;$k<$i;++$k)
		      $did=$did | ($failai[$i][$paieskoszona]==$failai[$k][$paieskoszona]);
			  
		 $kid=($failai[$i][$paieskoszona-1]==$rex[count($rex)-1]);
		 if ($paieskoszona==0) $kid=true;
		 
		 if ($kid && (!$did)){
		   print("\n<tr>\n<td width='50px' align='center'>\n");
	 	   print($v+1);
		   print(".\n</td>\n<td>\n");
		   print('<div id="Downloads');
		   print($v);
		   print('" style="width:100%;height:20px;cursor: pointer;color:#099FFF" onMouseOver="MM_changeProp(\'Downloads');
		   print($v);
		   print('\',\'\',\'style.color\',\'blue\',\'DIV\')" onMouseOut="MM_changeProp(\'Downloads');
		   print($v);
		   print('\',\'\',\'style.color\',\'#099FFF\',\'DIV\')"');
		   print("\n");
		   print(' OnClick="GoTo(\'index.php?site=downloads&action=');
		   for ($t=0;$t<=$paieskoszona;++$t){
		       print($failai[$i][$t]);
			   if ($t<$paieskoszona) print("\\\\");
		   }
		   print('\')">'); 
		   print($failai[$i][$paieskoszona]);
		   print("\n");	  
		   print('</div></td>');
		   $v++;	  
	     }
	 }	 
	}
	print("\n");
    print('</table>');
}
?>