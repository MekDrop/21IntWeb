<?
		$file="Data/CoolLinks.lst";
		$data=file($file);
		$dli = "Images/MenuIcons/ln1.gif";
		$nrgen=0;


Do {
  $rnd = rand(0,count($data)/2);
  $rnd=$rnd*2;
    if ($data[$rnd] != "")  

      {

	  $kintamasisA=trim($data[$rnd+1]);
  	  $kintamasisB=trim($data[$rnd]);
      print("<A class=\"nuorodanepakeista\" href=\"$kintamasisA\" target=\"_blank\"><div id=\"LinkItem$rnd\" class=\"meniufx-normalus\" \nonMouseOver=\"MM_changeProp('LinkItem$rnd','','style.backgroundColor','$MeniuUzvestaSpalva','DIV')\" \nonMouseOut=\"MM_changeProp('LinkItem$rnd','','style.backgroundColor','$MeniuNormaliSpalva','DIV')\" \n> \n\n");

      print("&nbsp;<img src=\"$dli\" width=\"16\" height=\"16\" align=\"absmiddle\" border=\"0\">\n$kintamasisB\n</div></a>\n\n");

	  $data[$rnd]="";

      ++$nrgen;

      }	  

}	  

While ($nrgen<2);

?>
          </td>
        </tr>
        <tr> 
          <td class="meniusritis">Skaitliukai</td>
        </tr>
        <tr> 
          <td> 
<?	$file="Data/counter.lst";
	readfile($file);
?>