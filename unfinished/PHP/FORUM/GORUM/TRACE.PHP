<?php
    $trsep="#";
    //TODO: Csak egyvalaki csinalhatja egyszerre egy directoryba!
    //$fp = fopen("php://stdout", "w");
    $traceDir="gorum/trace";
    $fpn = @fopen("$traceDir/n", "r");
    if (!$fpn) {//if number file not exists write 1 to it
        $fpn = @fopen("$traceDir/n", "w");
        if (!$fpn) {
            echo "Cannot create file in $traceDir";
            return;
        }
        system("chmod 664 $traceDir/n");
        $fname="1";
        fwrite($fpn,$fname);
    }
    else {//increment value in number file
        $fname = fread($fpn,10);
        fclose($fpn);
        $fpn = @fopen("$traceDir/n", "w");
        if (!$fpn) {
            echo "Cannot create file in $traceDir";
            return;
        }
        fwrite($fpn,++$fname);
    }
    fclose($fpn);
    $fp = fopen("$traceDir/$fname", "w");
    system("chmod 664 $traceDir/$fname");
    if (!$fp) {
        echo "Cannot create file in $traceDir";
        return;
    }
}
{

    $numargs = func_num_args();
    $arg_list = func_get_args();
    $s="";
    for ($i=0;$i<$numargs;$i++) {
         $t=ereg_replace("\n","___",$arg_list[$i]);
         $t=ereg_replace("\r","___",$t);
         $s.=$t;
         if ($i+1<$numargs) $s.=$trsep;
    }    
    fwrite($fp,"$s\n");
}
{
    global $sw1,$swall,$callNumFv,$perfON;

    $fname = substr($fname,strrpos($fname,"/")+1,strlen($fname)-1);
}
{
    
    $fname = substr($fname,strrpos($fname,"/")+1,strlen($fname)-1);
}
{
    global $TST;
    global $sw1,$swall,$callNumFv,$perfON;

    if ($status) $EN="EXIT";
    else {
            echo "<SCRIPT LANGUAGE='Javascript'>";
            echo "alert('Trace Error');";
            echo "</SCRIPT>";
        }
        $EN="ERROR";
    }
    $fname = substr($fname,strrpos($fname,"/")+1,strlen($fname)-1);
}
?>
