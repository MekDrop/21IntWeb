<?php
 $howmanyshow=$HTTP_GET_VARS["msgperpage"];
 if ($howmanyshow==0) $howmanyshow=10;
 $newsfile='Data/news.lst';
 $newsdata=file($newsfile);
 for ($i=0;$i<=count($newsdata);++$i) {
  list($pavadinimas[$i],$tekstas[$i],$data[$i],$paskelbe[$i])=split("\|",$newsdata[$i]);
  $pavadinimas[$i]=strip_tags($pavadinimas[$i]);
  $data[$i]=strip_tags($data[$i]);
  $paskelbe[$i]=strip_tags($paskelbe[$i]);
 }
 if (intval($vnt)<0) $vnt=0;
 if ($komanda=="add") {
	 ?>
	 <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
	     Nickas: <INPUT TYPE="text" NAME="nick" value="<?=$HTTP_POST_VARS["nick"];?>">
		 Pavadinimas: <INPUT TYPE="text" NAME="name" value="<?=$HTTP_POST_VARS["name"];?>">
		 Naujiena: <TEXTAREA NAME="text" ROWS="5" COLS="20" wrap="no" nowrap><?=$HTTP_POST_VARS["text"];?></TEXTAREA>
		 <INPUT TYPE="hidden" NAME="refresh" value="<?=$HTTP_POST_VARS["refresh"];?>">
		 <INPUT TYPE="submit" value="Paskelbti naujienà">
	 </FORM>
	 <?
 } else {
	if ($komanda=="additem"){
		$klaida=($HTTP_POST_VARS["nick"]=="")||($HTTP_POST_VARS["name"]=="")|| ($HTTP_POST_VARS["text"]=="");
		if ($klaida) {
			?>
			 Klaida: jûs uþpildëte nevisus laukus!
			 <FORM METHOD=post ACTION="index.php?site=news&action=add&menu=<?=$HTTP_GET_VARS["menu"];?>">
				 <INPUT TYPE="hidden" NAME="nick" value="<?=$HTTP_POST_VARS["nick"];?>">
				 <INPUT TYPE="hidden" NAME="name" value="<?=$HTTP_POST_VARS["name"];?>">
				 <INPUT TYPE="hidden" NAME="text" value="<?=$HTTP_POST_VARS["text"];?>">
		 		 <INPUT TYPE="hidden" NAME="refresh" value="<?=$HTTP_POST_VARS["refresh"];?>">
				 <INPUT TYPE="submit" value="Gráþti atgal">
			 </FORM>
			<?
		} else {
		   $i=-1;
  		   $pavadinimas[$i]=$HTTP_POST_VARS["name"];
		   $tekstas[$i]=$HTTP_POST_VARS["text"];
		   $data[$i]=date("Y.m.d G:i:s");
		   $paskelbe[$i]=$HTTP_POST_VARS["nick"];
		   $newsdata[$i]="$pavadinimas[$i]|$tekstas[$i]|$data[$i]|$paskelbe[$i]";
		   $id=fopen("./Data/news.lst","w+");
		   for ($o=-1;$o<count($newsdata);++$o)
			   if (trim($newsdata[$o])!="") fwrite($id,trim($newsdata[$o])."\n");
		   fclose($id);
		   ?>
		     Naujiena buvo pridëta
		   <?
		   $refresh=$HTTP_POST_VARS["refresh"];
		   if ($refresh!="")
			   echo	"<Meta http-equiv=\"Refresh\" content='0; URL=\"$refresh\"'>";
		}
	} else {
	// phpinfo();
	 $o=intval($vnt)+$howmanyshow;
	 if ($o>count($newsdata)) $o=count($newsdata);
	 for ($i=0+intval($vnt);$i<=$o-1;++$i)
       if ($tekstas[$i]!="") {
		   $text=$tekstas[$i];
		   include("./PHP/coolmsg.php");
		   $tekstas[$i]=$text;
		   echo "<table border=0 width=100%>\n";
		   echo "<tr><td>";
		   echo "<font size=2 color=gray><B>$pavadinimas[$i]</B></font><BR>";
		   echo "</td><td align=right>";
		   echo "<font size=2 color=#A0A0A0><B>$data[$i]</B></font><BR>";
		   echo "<td></tr>";
   		   echo "<tr><td colspan=2>";
		   echo "<font size=2>&nbsp;$tekstas[$i]</font><BR>";
		   echo "<td></tr>";
   		   echo "<tr><td colspan=2 align=right>";
		   echo "<font size=2><b>Paskelbë:</b> $paskelbe[$i]</font><br>";
		   echo "<td></tr></table><hr>";
	   }
	?><div align="right"><?
	if (intval($vnt)>0) {
		?>
			<a href="index.php?site=news&action=show&item=<?=(intval($vnt)-$howmanyshow)?>&menu=<?=$HTTP_GET_VARS["menu"];?>"><< Naujesnës þinutës</a> &nbsp;
		<?
	}
	if (intval($vnt)<(count($newsdata)-2)) {
		?>
			<a href="index.php?site=news&action=show&item=<?=(intval($vnt)+$howmanyshow)?>&menu=<?=$HTTP_GET_VARS["menu"];?>">Senesnës þinutës >></a>
		<?
	}
	?></div><?
  }
 }
 exit;
?>