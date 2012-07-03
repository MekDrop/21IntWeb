<?php
  $file=$komanda;
  if (file_exists("Images/Others/$file")) {
     $pavadinimas="Paveikslëliø perþiûra";
  	 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
	 print($pavadinimas);
	 print("<p align=\"center\"><img src=\"Images/Others/$file\" border=\"0\"></p>");
  }
  else {
     $pavadinimas="Paveikslëliø perþiûra";
  	 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
	 print($pavadinimas);
	 print("</div>");
	 print("<p align='left'>&nbsp;Deja, nurodyto pieðinëlio (");
	 print('"');
	 print($file);
	 print('"');	 
	 print(") dabar nëra.");  
  } 
?>