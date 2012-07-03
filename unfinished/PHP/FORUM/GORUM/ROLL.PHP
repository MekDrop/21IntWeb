<?php
$roll_typ =  
    array(
        "attributes"=>array(
            "method"=>array("roll"),
            "class"=>array("no roll"),
            "list"=>array("roll"),
            "invalid"=>array("no roll"),
            "rollid"=>array("roll"),
            "fromlist"=>array("roll"),
            "frommethod"=>array("roll"),
            "fromid"=>array("roll"),
            "fromfromlist"=>array("roll"),
            "fromfrommethod"=>array("roll"),
            "fromfromid"=>array("roll"),
            "generallist"=>array("no roll"),
            "generalmethod"=>array("no roll"),
            "generalrollid"=>array("no roll"),
            "globalUserId"=>array("no roll"),
            "sessionUserId"=>array("no roll"),
            "usrPassword"=>array("no roll"),
            "timeOut"=>array("no roll"),
            "submit"=>array("no roll"),
            "sortby"=>array("roll"),
            "sortdir"=>array("roll"),
            "affname"=>array("roll"),
        )
    );

class Roll extends Object
{
function create()
{
    global $defaultMethod,$defaultList,$defaultRollId,$HTTP_GET_VARS;
    initClassVars($this,TRUE);
    // Ha a method es list nem jon kivulrol, 
    // az alapertelmezest vesszuk, mert ezeknek mindenkeppen be
    // kell lenni allitva:
    if( !$this->method ) $this->method=$defaultMethod;
    if( !$this->list ) $this->list=$defaultList;
    if( !$this->rollid ) $this->rollid=$defaultRollId;
    
    // A class-ot a list-bol szarmaztatjuk:
    $slices = explode("_", $this->list);
    $this->class = $slices[0];
    // Ha egy linkre kattintas utjan megy at az ember egy 
    // listre, akkor a megjelenites elott az offseteket nullazni 
    // kell:
    if( isset($HTTP_GET_VARS["clearoff"]) ) clearOffsets();
}
    
function generFormHeaderAndHiddenFields($type="POST")
{
    global $scriptName,$gorumroll;
    $s = "";
    //TODO: ENCTYPE='multipart/form-data'".
    $s.="<FORM METHOD='$type' ENCTYPE='multipart/form-data'".
        " ACTION='$scriptName'>\n";
    $object_vars = get_object_vars($this);
    $typ = $this->getTypeInfo();
    global $FLOOD;
    if($gorumroll->method=="create_form" && isset($FLOOD)) {
        srand((double)microtime()*1000000);
        $flood=substr(md5(uniqid(rand())),0,20);
        $s.=generHiddenField("flood",$flood);
    }
    
    foreach( $object_vars as $attr=>$value )
    {
        if( $value && (!isset($typ["attributes"][$attr]) || !in_array("no roll", $typ["attributes"][$attr])))
        {
            $s.=generHiddenField($attr,$value);
        }
    }
    return $s;
}

function makeUrl($type="withScript")
{
    //TODO: kiszedve: veryScriptName, x, u
    // lehet, hogy meg tovabbi dolgok is vannak itt.
    global $cgiUrl,$scriptName;
    
    
    $s = "";
    if( $type=="withScript" )
    {
        global $dotDot;
        if (isset($dotDot)) $s.=$dotDot."/";
        $s.="$scriptName?";
    }
    elseif( $type=="mixed" )
    {
        if( $cgiUrl )
        {
            $s.="http:/"."/$cgiUrl$scriptName?";
        }
        else
        {
            global $HTTP_SERVER_VARS;
            $s.="http:/"."/$HTTP_SERVER_VARS[SERVER_NAME]".
                substr($HTTP_SERVER_VARS["SCRIPT_NAME"], 0,
                strrpos($HTTP_SERVER_VARS["SCRIPT_NAME"], "/")+1).
                "$scriptName?";
        }        
        
    }
    $typ = $this->getTypeInfo();
    $object_vars = get_object_vars($this);
    foreach( $object_vars as $attr=>$value )
    {
        if( $value && (!isset($typ["attributes"][$attr]) || !in_array("no roll", $typ["attributes"][$attr])))
        {
            $value=urlencode($value);
            $s.="$attr=$value&";
        }    
    }
    // Ha egy linkre kattintas utjan megy at az ember egy 
    // listre, akkor a megjelenites elott az offseteket nullazni 
    // kell:
    if(isset($this->method) && $this->method=="showhtmllist" ) {
        $s.="clearoff=1&";
    }
    if ($s=="index.php?") $s="index.php";//home url-ben ? ne legyen
    return $s;       
}

function generAnchor($label,$class="",$withScript=TRUE,$newWindow=FALSE)
{
    $type = $withScript ? "withScript" : "";       
    $href=$this->makeUrl($type);
    $s="";
    $s.="<a href='$href'";
    if ($class) $s.=" class='$class'";
    if( $newWindow ) $s.=" target='_blank'";
    $s.=">";
    $s.=$label;
    $s.="</a>";
    return $s;
}

function generImageAnchor($src,$alt,$width="",$height="")
{
    $label="<img src=\"$src\" alt=\"$alt\" border=\"0\"".
           " title='$alt'";
    if ($width!="") $label.=" width=\"$width\"";
    if ($height!="") $label.=" height=\"$height\"";
    $label.=">";
    return $this->generAnchor($label);
}  
  
function generConfirmation($label)
{
    global $lll;
    
    $s = "";
    $s.=$this->generFormHeaderAndHiddenFields();
    $s.=generBoxUp();
    $s.="<tr class='header'><th colspan='2'>\n";
    $s.= $label."</th></tr>";
    $s.= "<tr class='cell'><td align='center' class='cell'>";
    $s.="<input type=submit value='$lll[yes]' name=submit class='button'>\n";
    $s.="<input type=submit value='$lll[no]' name=submit class='button'>\n";
    $s.="</td></tr>";
    $s.=generBoxDown();
    $s.="</form>\n";
    return $s;
}
}// END CLASS -------------------------------------------------------

function generHiddenField($name="",$value="")
{
    if( $name )
    {
        $value=htmlspecialchars($value);
        return "<input type='hidden' name='$name' value='$value'>\n";
    }
}

function saveInFrom( &$tempRoll )
{
    global $gorumroll;
    if( !strstr($gorumroll->method, "_form") || 
        strstr($gorumroll->method, "Complex"))  // TODO!!!
    {
        $tempRoll->frommethod=isset($gorumroll->generalmethod) && $gorumroll->generalmethod ? 
                         $gorumroll->generalmethod : $gorumroll->method;
        $tempRoll->fromlist=isset($gorumroll->generallist) && $gorumroll->generallist ? 
                         $gorumroll->generallist : $gorumroll->list;
        $tempRoll->fromid=isset($gorumroll->generalrollid) && $gorumroll->generalrollid ? 
                         $gorumroll->generalrollid : $gorumroll->rollid;
    }
}
function saveInFromFrom( &$tempRoll )
{
    global $gorumroll;
    $tempRoll->fromfrommethod=isset($gorumroll->generalmethod) && $gorumroll->generalmethod ? 
                     $gorumroll->generalmethod : $gorumroll->method;
    $tempRoll->fromfromlist=isset($gorumroll->generallist) && $gorumroll->generallist ? 
                     $gorumroll->generallist : $gorumroll->list;
    $tempRoll->fromfromid=isset($gorumroll->generalrollid) && $gorumroll->generalrollid ? 
                     $gorumroll->generalrollid : $gorumroll->rollid;
}
function restoreFrom()
{
    global $gorumroll;
    $gorumroll->list = $gorumroll->fromlist;
    $gorumroll->method = $gorumroll->frommethod;
    $gorumroll->rollid = $gorumroll->fromid;
    $gorumroll->fromlist=0;
    $gorumroll->frommethod=0;
    $gorumroll->fromid=0;
}
function restoreFromFrom()
{
    global $gorumroll;
    $gorumroll->list = $gorumroll->fromfromlist;
    $gorumroll->method = $gorumroll->fromfrommethod;
    $gorumroll->rollid = $gorumroll->fromfromid;
    $gorumroll->fromfromlist=0;
    $gorumroll->fromfrommethod=0;
    $gorumroll->fromfromid=0;
}
?>
