<?
  $rfile="./Data/coolmsg.lst";
  $rdata=file($rfile);
  for ($rindex=0;$rindex<count($rdata);++$rindex){
	  list($trvalue,$nvalue)=explode("=", $rdata[$rindex]);
	  $trvalue=trim($trvalue);	
	  $nvalue=trim($nvalue);
	  $pos=true;
	  while ($pos){
 	   $pos = strpos("$trvalue", "$text");
       $text = str_replace("$trvalue", "<img src=\"./Images/Emotions/$nvalue\">", "$text");
      }
  }

?>