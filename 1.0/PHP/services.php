<?php
	 $file="Data/services.lst";
	 $data=file($file);
	 $pavadinimas="Paslaugos";
	 
 	 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
	 print($pavadinimas);
 
	 print('</div><p align="left">&nbsp;Pasirinkite paslaugà, kuria Jûs norite pasinaudoti (visos ðios paslaugos ðiuo metu yra nemokamos):');
	 print("\n");
   
	 print('<table border="0">');
	 print("\n");   

      $v=0;
	  for ($i=0;$i<count($data);$i=$i+2){
		   print("\n<tr>\n<td width='50px' align='center'>\n");
		   print($v+1);
		   print(".\n</td>\n<td>\n");
		   print('<div id="Services');
		   print($i);
		   print('" style="width:100%;height:20px;cursor: pointer;color:#099FFF" onMouseOver="MM_changeProp(\'Services');
		   print($i);
		   print('\',\'\',\'style.color\',\'blue\',\'DIV\')" onMouseOut="MM_changeProp(\'Services');
		   print($i);
		   print('\',\'\',\'style.color\',\'#099FFF\',\'DIV\')"');
		   print("\n");
		   $text=$data[$i+1];
		   $text=trim($text);
		   print("\n OnClick=\"GoTo('index.php?site=$text')\">"); 
		   print("\n");	  	  
   		   print($data[$i]); 
		   print('</div></td>');
		   $v++;
	 }

?>