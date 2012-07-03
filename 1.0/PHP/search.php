<?php
  
  # Informacija apie failø tipus:
  # 0 - failas su nuoroda á kitus failus
  # 1 - nuorodø failas
  # 2 - atsiuntimø sàraðo failas
  # 3 - naujienos
  # 4 - idëjos
  
  if ($action=="")  {
   print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
   print("Paieðka negalima\n");
   print("</div><br>");
   print("Áveskite frazæ, þodá arba þodþio dalá ir jo bus ieðkoma 21Int duomenø bazëje. :-)");
   return;
  }

  $file="Data/search.lst";
  $data=file($file);
  
  $komanda=trim($komanda);
  $komanda=rawurldecode($komanda);
    
  $noi=0;
  
  for ($i=0;$i<count($data);++$i){
	  #atskirimas failas nuo savo tipo
	  $mdk[$i]=explode(",", $data[$i]);
	  $mdk[$i][1]=trim($mdk[$i][1]);
	  $mdk[$i][0]=trim($mdk[$i][0]);
  } 
  
  $rasta=0;
  
  for ($i=0;$i<count($data);++$i){
  	  #ieðkoma 
	  $datex=file($mdk[$i][0]);
	  
	  if ($mdk[$i][1]=="0")  
	   for ($o=0;$o<count($datex);$o++){
          $tfile=explode(".",$mdk[$i][0]);
		  $tfile=$tfile[0];
		  $datex[$o]=trim($datex[$o]);
	      $tfile="$tfile/$datex[$o].lst";
		  $tdata=file($tfile);
		  $noi=0;
		  for ($m=0;$m<count($tdata);++$m){
		    $tdata[$m]=strip_tags($tdata[$m]);
		    $duom=explode(strtolower($komanda), strtolower($tdata[$m]));
		    if (count($duom)>1) {
			  if ($noi==0) {
			     $tf=explode(".",$mdk[$i][0]);
   		         $tf=$tf[0];
				 $tf=explode("/",$tf);
   		         $tf=$tf[count($tf)-1];
			     $rx[$rasta][0]="index.php?site=$tf&action=$datex[$o]";
				 $rx[$rasta][2]=$datex[$o];
				 ++$rasta;
		      }		 
			  ++$noi;
			 $rx[$rasta-1][1]=$rx[$rasta-1][1]+((count($tdata)-m)*(count($duom)-1));
			}   
		  }
	   }

	  if (($mdk[$i][1]=="1")||($mdk[$i][1]=="4")||($mdk[$i][1]=="3")) {
	    $noi=0;
	    for ($o=0;$o<count($datex);$o++){
		    $datex[$o]=strip_tags($datex[$o]);
		    $duom=explode(strtolower($komanda), strtolower($datex[$o]));
		    if (count($duom)>1) {
			  if ($noi==0) {
			     #Gaunamas srities pavadinimas, kur buvo rasta
				 $tf=explode(".",$mdk[$i][0]);
   		         $tf=$tf[0];
				 $tf=explode("/",$tf);
	             $tf=$tf[count($tf)-1];
 			     #Generuojami paieðkos rezultatai
			     if ($mdk[$i][1]=="1") {
					$ntkey=$o%2;
					if ($ntkey==0) $ntkey=$o;
					   else $ntkey=$o+1;
			        $rx[$rasta][0]=$datex[$ntkey+1];
					$rx[$rasta][2]=$datex[$ntkey];
				 }
				 if (($mdk[$i][1]=="3")||($mdk[$i][1]=="4")){
				    $rx[$rasta][0]="index.php?site=$tf&action=display";
					$pavadinimas="";
					if ($tf=="news") $pavadinimas="Naujienos";
					if ($tf=="ideas") $pavadinimas="Idëjos";
					$rx[$rasta][2]=$pavadinimas;
				 }	
				 ++$rasta;
		      }		 
			  ++$noi;
			 $rx[$rasta-1][1]=$rx[$rasta-1][1]+1;
		     if ($mdk[$i][1]=="1") $rx[$rasta-1][1]=$rx[$rasta-1][1]+10;
			}   
	    }
      }
	  
	  if ($mdk[$i][1]=="2")
	     for ($o=0;$o<count($datex);$o++){
          $tfile=explode(".",$mdk[$i][0]);
		  $tfile=$tfile[0];
		  $datex[$o]=trim($datex[$o]);
		  $nrx=$o+1; 
	      $tfile="$tfile/$nrx.lst";
		  $tdata=file($tfile);
		  $noi=0;
		  for ($m=0;$m<count($tdata);++$m){
		    $tdata[$m]=strip_tags($tdata[$m]);
		    $duom=explode(strtolower($komanda), strtolower($tdata[$m]));
		    if (count($duom)>1) {
			  if ($noi==0) {
			     $rx[$rasta][0]="index.php?site=downloads&action=showitem&item=$o";
				 $rx[$rasta][2]=$tdata[1];
				 ++$rasta;
		      }		 
			  ++$noi;
			 $rx[$rasta-1][1]=$rx[$rasta-1][1]+1;
			}   
		  }
		  }
		  
  }
  
  #suranda, nará kurio yra didþiausias reitingas
  $max=0;
  for ($i=0;$i<count($rx);++$i)
    if ($max<$rx[$i][1])
	  if (trim(strtolower($rx[$i][2]))!=trim(strtolower($komanda))){
	      $max=$rx[$i][1];}//*21;}
	  else	  {
	      $max=$rx[$i][1];}
  ++$max;
  $max=$max+$rasta%10;
	
  #parodo paieðkos rezultatus
   print('<div align="left" style="text-decoration: none; text-shadow: navy 1em 1em; font: normal normal Times New Roman; font-family: Times New Roman; font-size: 24px; font-style: normal; font-variant: normal; color: #0000CC">');
   print("Paieðkos rezultatai\n");
   print("</div>");
  if (count($rx)>0){ 
   $tk=count($rx);
   $gal="rasti $tk puslapiai, kuriuose";
   if (($tk%10)==0) $gal="rasta $tk puslapiø, kuriuose"; 
   if (($tk<20)&&($tk>10)) $gal="rasti $tk puslapiø, kuriuose";    
   if (($tk%10)==1) $gal="rastas $tk puslapis, kuriame";
   print("<p align=\"left\">Buvo $gal yra paraðyta frazë \"$komanda\":\n<br>");
   print("<table border=\"0\" width=\"100%\">");
   $spalva="white";
   print("<tr bgcolor=\"#b0c4de\"><th width=\"70px\"><font color=\"$spalva\">Atitikimas</font></th>\n<th width=\"100px\"><font color=\"$spalva\">Pavadinimas</font></th>\n<th><font color=\"$spalva\">Adresas</font></th></tr>\n");
   for ($o=$max;0<$o;$o=$o-1)
    for ($i=0;$i<count($rx);++$i)
	    if ($o==$rx[$i][1]){
		  print("<tr>\n");

		  $maxr=100/$max*$rx[$i][1];
		  $percent[0]=round($maxr%100);
		  $percent[1]=round($maxr);
		  $max=$max+$rx[$i][1];
		  if ($percent[1]=="-") $percent[1]=0;
		  if ($percent[1]!=100)
		      $percent="$percent[1].$percent[0]";
          else
		      $percent="$percent[1]";
		  print("<td align=\"center\" valign=\"top\">$percent%</td>\n");
		  
		  print('<td valign=\"top\"><a href="');
	      print($rx[$i][0]); 
		  print('">');
		  print($rx[$i][2]);
		  print("</a></td>\n");

		  print("<td valign=\"top\"><a href=\"");
	      print($rx[$i][0]); 
		  print('">');
		  print($rx[$i][0]);
		  print("</a></td>\n");
		  
#	      print(",");	
#		  print($rx[$i][1]);
	      print("</tr>\n");
	    }	
  print("</table>");		
  }
  else {
     print("<p>Frazës \"$komanda\" nepavyko surasti.");
  }   
  print("</p>");
?>
<script lang="JavaScript">
  document.search.action.focus();
</script>