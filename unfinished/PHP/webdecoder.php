<?php
 if ($komanda==""){
     print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">Web I�kodavimas</div>');
     print('<Form method="get" action="index.php"><p align="left">&nbsp;�veskite tekst�, kur� reikia jums i�koduoti:<input type="hidden" name="site" value="webdecoder"><br><TextArea name="action" cols=50 rows=10></TextArea><br><input type="submit" value="I�koduoti"></form>');
 }
 else {
     print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">Web I�kodavimas</div>');
	 print('<p align="left">&nbsp;Tekstas, kuris k� tik buvo �vestas dabar yra jau i�koduotas:<br>');
     print('<TextArea cols=50 rows=10 atomicselection="true" readonly>');
	 $decode=rawurldecode($komanda);
	 $decode=rawurldecode($decode);
	 print($decode);
	 print('</TextArea>');
}

?>