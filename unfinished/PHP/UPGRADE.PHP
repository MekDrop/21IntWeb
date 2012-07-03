<?php 

/*
 *	upgrade.php -- txtForumi uuendamiseks versioonidelt <=0.5.0 --> 0.6.0
 *
 *
 *	Copyleft anno 2002 Lauri Kasvandik
 */

echo "<h1>For upgrading from \"old\" (<= 0.5.0) txtForum to ver 0.6.0</h1>";

if (!isset($tegevus))
{

echo 
"
<form name=upgradeform action=$PHP_SELF?tegevus=uuenda method=post>
Vana postituste kataloog: <input name=vanakataloog value='./msg/'>
<br>Vana päisefail: <input name=vanapaisefail value='postifail.txt'>
<br>Uus postituste kataloog <input name=uuskataloog value='./teated/'>
<br>Uus päisefail: <input name=uuspaisefail value='header.txt'>
<br><input type=reset value=taasta> <input type=submit value=uuenda>
</form>
";

}
else if ($tegevus=="uuenda")
{
	if (!file_exists($vanakataloog.$vanapaisefail)) {die("Sellist faili ei leitud: $vanakataloog$vanapaisefail");}
	$vana_pais_f = file($vanakataloog.$vanapaisefail);

	$uus_pais_f = @fopen($uuskataloog.$uuspaisefail, "a");
	if (!file_exists($uuskataloog.$uuspaisefail)) {die("Sellist faili ei leitud: $uuskataloog$uuspaisefail");}
	foreach ($vana_pais_f as $rida)
	{
		$m = explode("||", chop($rida));
		if ($m[0] != "" && $m[1] != "" && $m[2] != "" && $m[3] != "" && isset($m[0]) && isset($m[1]) && isset($m[2]) && isset($m[3]))
		{
			// 0 - failinimi; 1 - teemarida; 2 - vastuste arv; 3 - algpostituse kuupäev; 4 - ip; 5 - viimase postituse aeg; 6 - teemaalustaja nimi; 7 - vaatamiste arv

			$vanafailinimi = $m[0];
			if (file_exists($vanafailinimi))
			{
				$uusfailinimi = date("Ymdhis", $m[3]).".txt";
				$m[0]=$uusfailinimi;
				if ($m[4] == "" || !isset($m[4]) || $m[4]=="1"){ $m[4] = "ip teadmata"; }
				if ($m[5] == "" || !isset($m[5]) || $m[5]=="0"){ $m[5] = $m[3]; }
				if ($m[6] == "" || !isset($m[6])){ $m[6] = "tundmatu sõdur"; }

				if ($m[7] == "" || !isset($m[7])){ $m[7] = "1"; }

				$rida = implode("||", $m);
				fputs($uus_pais_f, trim($rida)."\n");

				$vanapostitus = file($vanafailinimi);

				$fp = fopen($uuskataloog.$uusfailinimi, "w");
				for($i=1;$i<count($vanapostitus);$i++)
				{
					$i >= 2 ? $str = "re: " : $str = "";
					fputs($fp, $str.trim($vanapostitus[0])."||".trim($vanapostitus[$i]). "\n");
				}
				fclose($fp);
			}
		}
	}

	fclose($uus_pais_f);
	echo "Uuendamine sooritatud. Nüüd võiksid käesoleva faili ära kustutada. Igaks juhuks. Äkki keegi veel leiab ning hakkab mängima ja...<p>Probleemide korral kiika aadressile <a href=http://zone.ee/txtforum/forum.php>zone.ee/txtforum/forum.php</a>";
	echo "<p><p><a href=forum.php>kohalikku foorumisse</a>";
}

?>