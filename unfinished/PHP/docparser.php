<?

     putenv("include_path='.;./PHP/;./PHP/forum/'");
//		 include("./PHP/$sritis.php");
	 if (file_exists("./PHP/$sritis.php")){

		 include("./PHP/$sritis.php");

		 }

	 else {

	  	 print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');

 	     print("N�ra tokios srities");

		 print("</div><p align=\"left\">Deja, �iuo metu �ioje svetain�j�je n�ra srities \"$sritis\". Pabandykite u�eiti v�liau. :)</p>");

	 }

?>