<?php
  $file="Data/batcommands.lst";
  $data=file($file);
 
  if ($komanda==""){
      print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">BAT Komandos</div>');
	  print('<p align="left">&nbsp;Ðtai èia yra BAT komandø sàraðas:');
	  print('<table border="0"><tr>');
	  $kt=-1;	  
	  for ($i=0; $i<count($data); ++$i){
		  $kt=$kt+1;
		  if ($kt==7) { 
		     print('</tr>');
		     print('<tr>');
		     $kt=0;
		  }	
		  print('<td bgcolor="#FFFFCC">');
		  print('<div id="BatCommands');
		  print($i);
		  print('" style="width:80px;height:20px;cursor: pointer" onMouseOver="MM_changeProp(\'BatCommands');
		  print($i);
		  print('\',\'\',\'style.backgroundColor\',\'#FFFF99\',\'DIV\')" onMouseOut="MM_changeProp(\'BatCommands');
		  print($i);
		  print('\',\'\',\'style.backgroundColor\',\'#FFFFCC\',\'DIV\')"');
		  $data[$i]=trim($data[$i]);
		  print(' OnClick="GoTo(\'index.php?site=batcommands&action=');
		  print($data[$i]);
		  print('\')">');
		  print("$data[$i]</div></td>");
      } 
  print('</table>');
  }
 else{
    if (!file_exists("Data/batcommands/$komanda.lst")) {
	    print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">BAT Komandos</div>');
		print('<p align="left">&nbsp;Jûsø komanda buvo nerasta duomenø bazëje.<p>');
    }
	else {
	    print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
		print($komanda);
		print('</div><br>');
		readfile("Data/batcommands/$komanda.lst");
	} 
 } 	 
?>