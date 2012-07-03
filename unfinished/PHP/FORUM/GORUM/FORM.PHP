<?php
function generForm(&$base, &$s)
{
    global $lll,$xi;
    global $gorumroll;
    global $maxInputLength, $maxFieldLength;

    $s="";
    $typ=$base->getTypeInfo();
    $class = $gorumroll->class;
    $method = $gorumroll->method;
    // A kovetkezo method beallitasa:
    $tempRoll = $gorumroll;
    $tempRoll->method = str_replace("_form","",$tempRoll->method);
    
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    global $mainBoxWidth,$mainBoxPadding;
    if (!isset($mainBoxWidth)) $mainBoxWidth="100%";
    if (!isset($mainBoxPadding)) $mainBoxPadding="2";
    $s.=generBoxUp($mainBoxWidth,$mainBoxPadding);    
    global $SABLON;
    if (isset($SABLON)) {
        $s="<b>".$lll[$class."_".$method]."</b><br><br>\n".$s;
    }
    else {
        $s.="<tr><th class='header' colspan='2'>";
        $s.=$lll[$class."_".$method]; // A form cime
        $s.="</th></tr>\n";
    }
    $hiddens="";
    $attributeList = isset($typ["order"]) ? 
                     $typ["order"] : array_keys($typ["attributes"]);
    foreach( $attributeList as $attr )
    {
        $val = & $typ["attributes"][$attr];
        if( $base->getVisibility( $typ, $attr )==Form_invisible )
        {
            continue;
        }
        elseif( in_array("section",$val) )
        {
            if( isset($lll[$attr]) )
            {
                $s.="<tr><th class='separator' colspan='2'>";
                $s.=$lll[$attr]; 
                $s.="</th></tr>\n";
            }    
            continue;
        }
        elseif( in_array("txtsection",$val) )
        {
            if( isset($lll[$attr]) )
            {
                $s.="<tr><td class='cell' colspan='2'>";
                $s.=$lll[$attr]; 
                $s.="</td></tr>\n";
            }    
            continue;
        }
        // Ahelyett, hogy a processMethodban initeznenk, itt allitjuk
        // az attributumot defaultra, ha nincs beszettelve:
        if( !isset($base->{$attr}) ) 
        {
            $base->{$attr} = $base->getDefault($typ, $attr);
        }    
        if( $base->getVisibility( $typ, $attr )==Form_hidden )
        {
            $hiddens.=generHiddenField($attr,$base->{$attr});
            continue;
        }
        
        // A mezo parametereinek meghatarozasa:
        // txt: a mezo cime,
        // expl: a mezohoz adott magyarazo szoveg,
        // fieldLength: a mezo hossza,
        // inputLength: a mezobe irhato szoveg hossza
        $expl="";
        global $showExplanation;
        if (isset($lll[$class."_".$attr])) 
        {            
            $txt=$lll[$class."_".$attr];
            if (isset($lll[$class."_".$attr."_expl"])) 
            {
                $expl=$lll[$class."_".$attr."_expl"];
                if ($showExplanation==Explanation_popup) {
                    $expl=$class."_".$attr."_expl";
                }
            }
        }
        else 
        {
            $txt=$lll[$attr];
            if (isset($lll[$attr."_expl"])) 
            {
                $expl=$lll[$attr."_expl"];
                if ($showExplanation==Explanation_popup) {
                    $expl=$attr."_expl";
                }
            }
        }  
        if (isset($val["max"])) $fieldLength=$val["max"];
        else $fieldLength=$maxFieldLength;
        if (isset($val["length"])) $inputLength=$val["length"]; 
        else $inputLength=$maxInputLength;
        if (in_array("$method: mandatory",$val) || 
            in_array("mandatory",$val)) $txt.=" *";
        $afterField = isset($lll[$class."_".$attr."_afterfield"]) ?
                      $lll[$class."_".$attr."_afterfield"] : "";
        // A mezok generalasa:  
        if( $base->getVisibility( $typ, $attr )==Form_readonly )
        {
            $hiddens.=generHiddenField($attr,$base->{$attr});
            $value = $base->showListVal($attr);
            $s.=generReadonlyField($txt,$value,"cell");
        }
        elseif( in_array("$method: file",$val) )
        {
            $s.=generFileField($attr,$txt, $expl,
                               $inputLength, $fieldLength,
                               "cell");
        }
        elseif( in_array("$method: text",$val) )
        {
            $s.=generTextField("text",$attr,$txt, $expl, 
                               $base->{$attr},$inputLength,$fieldLength,
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("$method: password",$val) )
        {
            $s.=generTextField("password",$attr,$txt, $expl, 
                               $base->{$attr},$inputLength,$fieldLength,
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("$method: button",$val) )
        {
            $buttonText = $lll[$class."_".$attr."_buttontext"];
            $s.=generTextField("button","submit",$txt, $expl, 
                               $buttonText,$inputLength,$fieldLength,
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("$method: textarea",$val) )
        {
            $s.=generTextAreaField($attr,$txt, $expl, $base->{$attr}, 
                                   $val["rows"], $val["cols"], 
                                   "cell", "", $afterField);
        }
        elseif( in_array("$method: multipleselection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            $labels = array();
            foreach($val["values"] as $key=>$value) 
            {
                if( isset($lll[$class."_".$attr."_".$value]) ) {
                    $labels[$key]=$lll[$class."_".$attr."_".$value];
                }
                else $labels[$key]=$lll[$attr."_".$value];
            }
            $s.=generMultipleSelection($attr, $txt, $expl, $labels, 
                                       $val["values"], $base->{$attr}, 
                                       $size, 
                                       "cell");
        }
        elseif( in_array("$method: selection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            $labels = array();
            if (isset($val["configvalues"])) {
                $valName=$val["configvalues"];
                global $$valName;
                foreach($$valName as $key => $value) {
                    $labels[$value]=$lll[$valName."_".$value];
                    $values[$value]=$value;
                }
                $s.=generSelectField($attr,$txt,$expl, $labels, 
                                     $values,$base->{$attr},
                                     $size,"cell", "", TRUE, FALSE, 
                                     "label",$afterField);
            }
            else {
                foreach($val["values"] as $key=>$value) {
                    if( isset($lll[$class."_".$attr."_".$value]) ) {
                        $labels[$key]=$lll[$class."_".$attr."_".$value];
                    }
                    else $labels[$key]=$lll[$attr."_".$value];
                }
                $s.=generSelectField($attr,$txt,$expl, $labels, 
                                     $val["values"],$base->{$attr},
                                     $size,"cell", "", TRUE, FALSE, 
                                     "label",$afterField);
            }
        }
        elseif( in_array("$method: classselection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            global $applName;
            $tableName = $applName."_".$val["class"];
            if( isset($val["query"]) ) $query=$val["query"];
            else $query = "SELECT id, $val[labelAttr] FROM $tableName";
            if( isset($val["where"]) ) $query.=" WHERE $val[where]";
            if( isset($val["ordered"]) ) $query.=" ORDER BY $val[ordered]";
            $objects = new $val["class"];
            loadObjectsSQL($objects, $query, $objects);
            $labels = array();
            $values = array();
            if( isset($val["nothing selected"]) )
            {
                $values[] = 0;
                $labels[]=$lll[$val["nothing selected"]];
            }
            foreach( $objects as $obj )
            {
                $values[]=$obj->id;
                if( $obj->id ) $labels[]=$obj->{$val["labelAttr"]};
                else $labels[]=$lll["nothingSelected"];
            }
            $s.=generSelectField($attr,$txt,$expl, $labels, 
                                 $values,$base->{$attr},
                                 $size,"cell", "", TRUE, FALSE, "label",
                                 $afterField);
        }
        elseif( in_array("$method: multipleclassselection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            global $applName;
            $tableName = $applName."_".$val["class"];
            if( isset($val["query"]) ) $query=$val["query"];
            else $query = "SELECT id, $val[labelAttr] FROM $tableName";
            if( isset($val["where"]) ) $query.=" WHERE $val[where]";
            if( isset($val["ordered"]) ) $query.=" ORDER BY $val[ordered]";
            $objects = new $val["class"];
            loadObjectsSQL($objects, $query, $objects);
            $labels = array();
            $values = array();
            if( isset($val["nothing selected"]) )
            {
                $values[] = 0;
                $labels[]=$lll[$val["nothing selected"]];
            }
            foreach( $objects as $obj )
            {
                $values[]=$obj->id;
                if( $obj->id ) $labels[]=$obj->{$val["labelAttr"]};
                else $labels[]=$lll["nothingSelected"];
            }
            $s.=generMultipleSelection($attr,$txt,$expl, $labels, 
                                 $values,$base->{$attr},
                                 $size,"cell");
        }
        elseif( in_array("$method: date",$val) )
        {
            // Ha invalid form utan terunk vissza a formba:
            if( is_array($base->{$attr}) ) {
                if( $val["type"]=="INT" )
                {
                    $base->{$attr}=mktime(0,0,0,$base->{$attr}["month"],
                        $base->{$attr}["day"],$base->{$attr}["year"]);
                }
                elseif( $val["type"]=="DATE" ) {
                    $base->{$attr}=$base->{$attr}["year"]."-".
                                   $base->{$attr}["month"]."-".
                                   $base->{$attr}["day"];
                }
            }
            $s.=generSimpleDateField($attr, $txt, $expl, $base->{$attr},
                                     "cell","",TRUE,FALSE,$val);
        }
        elseif( in_array("$method: time",$val) )
        {
            // Ha invalid form utan terunk vissza a formba:
            if( is_array($base->{$attr}) )
            {
                $base->{$attr}=mktime($base->{$attr}["hour"],
                    $base->{$attr}["minute"],0,$base->{$attr}["month"],
                    $base->{$attr}["day"],$base->{$attr}["year"]);
            }
            $s.=generSimpleDateField($attr, $txt, $expl, $base->{$attr},
                                     "cell", "", TRUE, TRUE, $val);
        }
        elseif( in_array("$method: bool",$val) )
        {
            $s.=generBoolField($attr, $txt, $expl, $base->{$attr},
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("$method: radio",$val) )
        {
            $labels = array();
            foreach($val["values"] as $key=>$value) 
            {
                if( isset($lll[$class."_".$attr."_".$value]) ) {
                    $labels[$key]=$lll[$class."_".$attr."_".$value];
                }
                else $labels[$key]=$lll[$attr."_".$value];
            }
            $s.=generRadioField($attr,$txt,$expl, $labels, 
                                 $val["values"],$base->{$attr},
                                 $val["cols"],"cell", "", TRUE, 
                                 $afterField);
        }
        elseif( in_array("file",$val))
        {
            $s.=generFileField($attr,$txt, $expl,
                               $inputLength, $fieldLength,
                               "cell");
        }
        elseif( in_array("text",$val) )
        {
            $s.=generTextField("text",$attr,$txt, $expl, 
                               $base->{$attr},$inputLength,$fieldLength,
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("password",$val) )
        {
            $s.=generTextField("password",$attr,$txt, $expl, 
                               $base->{$attr},$inputLength,$fieldLength,
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("button",$val) )
        {
            $buttonText = $lll[$class."_".$attr."_buttontext"];
            $s.=generTextField("button","submit",$txt, $expl, 
                               $buttonText,$inputLength,$fieldLength,
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("textarea",$val) )
        {
            $s.=generTextAreaField($attr,$txt, $expl, $base->{$attr}, 
                                   $val["rows"], $val["cols"], 
                                   "cell", "", $afterField);
        }
        elseif( in_array("multipleselection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            $labels = array();
            foreach($val["values"] as $key=>$value) 
            {
                if( isset($lll[$class."_".$attr."_".$value]) ) {
                    $labels[$key]=$lll[$class."_".$attr."_".$value];
                }
                else $labels[$key]=$lll[$attr."_".$value];
            }
            $s.=generMultipleSelection($attr, $txt, $expl, $labels, 
                                       $val["values"], $base->{$attr}, 
                                       $size, 
                                       "cell");
        }
        elseif( in_array("selection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            $labels = array();
            if (isset($val["configvalues"])) {
                $valName=$val["configvalues"];
                global $$valName;
                foreach($$valName as $key => $value) {
                    $labels[$value]=$lll[$valName."_".$value];
                    $values[$value]=$value;
                }
                $s.=generSelectField($attr,$txt,$expl, $labels, 
                                     $values,$base->{$attr},
                                     $size,"cell", "", TRUE, FALSE, 
                                     "label",$afterField);
            }
            else {
                foreach($val["values"] as $key=>$value) {
                    if( isset($lll[$class."_".$attr."_".$value]) ) {
                        $labels[$key]=$lll[$class."_".$attr."_".$value];
                    }
                    else $labels[$key]=$lll[$attr."_".$value];
                }
                $s.=generSelectField($attr,$txt,$expl, $labels, 
                                     $val["values"],$base->{$attr},
                                     $size,"cell", "", TRUE, FALSE, 
                                     "label",$afterField);
            }
        }
        elseif( in_array("classselection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            global $applName;
            $tableName = $applName."_".$val["class"];
            if( isset($val["query"]) ) $query=$val["query"];
            else $query = "SELECT id, $val[labelAttr] FROM $tableName";
            if( isset($val["where"]) ) $query.=" WHERE $val[where]";
            if( isset($val["ordered"]) ) $query.=" ORDER BY $val[ordered]";
            $objects = new $val["class"];
            loadObjectsSQL($objects, $query, $objects);
            $labels = array();
            $values = array();
            if( isset($val["nothing selected"]) )
            {
                $values[] = 0;
                $labels[]=$lll[$val["nothing selected"]];
            }
            foreach( $objects as $obj )
            {
                $values[]=$obj->id;
                if( $obj->id ) $labels[]=$obj->{$val["labelAttr"]};
                else $labels[]=$lll["nothingSelected"];
            }
            $s.=generSelectField($attr,$txt,$expl, $labels, 
                                 $values,$base->{$attr},
                                 $size,"cell", "", TRUE, FALSE, "label",
                                 $afterField);
        }
        elseif( in_array("multipleclassselection",$val) )
        {
            $size = isset($val["size"]) ? $val["size"] : 1;
            global $applName;
            $tableName = $applName."_".$val["class"];
            if( isset($val["query"]) ) $query=$val["query"];
            else $query = "SELECT id, $val[labelAttr] FROM $tableName";
            if( isset($val["where"]) ) $query.=" WHERE $val[where]";
            if( isset($val["ordered"]) ) $query.=" ORDER BY $val[ordered]";
            $objects = new $val["class"];
            loadObjectsSQL($objects, $query, $objects);
            $labels = array();
            $values = array();
            if( isset($val["nothing selected"]) )
            {
                $values[] = 0;
                $labels[]=$lll[$val["nothing selected"]];
            }
            foreach( $objects as $obj )
            {
                $values[]=$obj->id;
                if( $obj->id ) $labels[]=$obj->{$val["labelAttr"]};
                else $labels[]=$lll["nothingSelected"];
            }
            $s.=generMultipleSelection($attr,$txt,$expl, $labels, 
                                 $values,$base->{$attr},
                                 $size,"cell");
        }
        elseif( in_array("date",$val) )
        {
            // Ha invalid form utan terunk vissza a formba:
            if( is_array($base->{$attr}) ) {
                if( $val["type"]=="INT" )
                {
                    $base->{$attr}=mktime(0,0,0,$base->{$attr}["month"],
                        $base->{$attr}["day"],$base->{$attr}["year"]);
                }
                elseif( $val["type"]=="DATE" ) {
                    $base->{$attr}=$base->{$attr}["year"]."-".
                                   $base->{$attr}["month"]."-".
                                   $base->{$attr}["day"];
                }
            }
            $s.=generSimpleDateField($attr, $txt, $expl, $base->{$attr},
                                     "cell","",TRUE,FALSE,$val);
        }
        elseif( in_array("time",$val) )
        {
            // Ha invalid form utan terunk vissza a formba:
            if( is_array($base->{$attr}) )
            {
                $base->{$attr}=mktime($base->{$attr}["hour"],
                    $base->{$attr}["minute"],0,$base->{$attr}["month"],
                    $base->{$attr}["day"],$base->{$attr}["year"]);
            }
            $s.=generSimpleDateField($attr, $txt, $expl, $base->{$attr},
                                     "cell", "", TRUE, TRUE,$val);
        }
        elseif( in_array("bool",$val) )
        {
            if( !isset($base->{$attr}) ) $base->{$attr}=FALSE;
            $s.=generBoolField($attr, $txt, $expl, $base->{$attr},
                               "cell", "", TRUE, $afterField);
        }
        elseif( in_array("radio",$val) )
        {
            $labels = array();
            foreach($val["values"] as $key=>$value) 
            {
                if( isset($lll[$class."_".$attr."_".$value]) ) {
                    $labels[$key]=$lll[$class."_".$attr."_".$value];
                }
                else $labels[$key]=$lll[$attr."_".$value];
            }
            $s.=generRadioField($attr,$txt,$expl, $labels, 
                                 $val["values"],$base->{$attr},
                                 $val["cols"],"cell", "", TRUE, 
                                 $afterField);
        }
    }
    if( !in_array("nosubmit", $typ) )
    {
        $s.="<tr><td align='center' colspan='2' class='cell'>";
        if( isset($typ["$method: submit"]) )
        {
            foreach( $typ["$method: submit"] as $button )
            {
                $s.="<input type='submit' value='".$lll[$button].
                    "' name='submit' class='button'>\n";
            }
        }
        elseif( isset($typ["submit"]) )
        {
            foreach( $typ["submit"] as $button )
            {
                $s.="<input type='submit' value='".$lll[$button].
                    "' name='submit' class='button'>\n";
            }
        }
        else
        {
            $s.="<input type='submit' value='".$lll["ok"].
                "' name='submit' class='button'>\n";
            $s.="<input type='submit' value='".$lll["cancel"].
                "' name='submit' class='button'>\n";            
            /*
            $s.="<button type='submit' value='".$lll["ok"].
                "' name='submit' class='button'>".
                "<img src='$xi/ok.gif' width='40' height='22'>".
                "</button>\n";
                */
            /*
            $s.="<input type='image' value='".$lll["ok"].
                "' name='submit' class='button' src='$xi/ok.gif'>\n";
            $s.="<input type='image' value='".$lll["cancel"].
                "' name='cancel' class='button' src='$xi/cancel.gif'>\n";
                */
        }
        $s.="</td></tr>";
    }
    $s.=generBoxDown();
    $s.=$hiddens;
    $s.="</form>\n\n";
    return ok;
}

function generDeleteForm(&$base, &$s)
{
    global $lll;
    global $gorumroll;

    $s="";
    // A kovetkezo method beallitasa:
    $tempRoll = $gorumroll;
    $tempRoll->method = str_replace("_form","",$tempRoll->method);
    
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    $typ=$base->getTypeInfo();
    // Az objektum hidden attributumait tovabb kell vinni:
    foreach( $typ["attributes"] as $attr=>$val )
    {
        if( in_array("$gorumroll->method: form hidden",$val) )
        {
            $s.=generHiddenField($attr,$base->{$attr});
        }
        elseif( in_array("form hidden",$val) )
        {
            $s.=generHiddenField($attr,$base->{$attr});
        }
    }    
    global $mainBoxWidth,$mainBoxPadding;
    if (!isset($mainBoxWidth)) $mainBoxWidth="100%";
    if (!isset($mainBoxPadding)) $mainBoxPadding="2";
    $s.=generBoxUp($mainBoxWidth,$mainBoxPadding);    
    $s.="<tr><th class='header'>\n";
    $label = sprintf($lll["beforeDelete"], $lll[$gorumroll->class]);
    if( isset($lll[$gorumroll->class."_seriousDeleteQuestion"]) )
    {
        $label.="<br>".$lll[$gorumroll->class."_seriousDeleteQuestion"];
    }
    $s.= "$label</th></tr>";
    if( isset($typ["delete_confirm"]) && isset($base->{$typ["delete_confirm"]}) && 
        $base->{$typ["delete_confirm"]} )
    {
        $s.="<tr><td class='cell' align='center'>";
        $s.=$base->showListVal($typ["delete_confirm"]);
        $s.="</td></tr>\n";
    }
    $s.= "<tr class='cell'><td align='center' class='cell'>";
    $s.="<input type=submit value='$lll[ok]' name=submit class='button'>\n";
    $s.="<input type=submit value='$lll[cancel]' name=submit class='button'>\n";
    $s.="</td></tr>";
    $s.=generBoxDown();
    $s.="</form>\n";
}

function generMultipleDeleteForm(&$base, &$s)
{
    global $lll;
    global $gorumroll, $resultList;

    $s="";
    // A kovetkezo method beallitasa:
    $tempRoll = $gorumroll;
    $tempRoll->method = str_replace("_form","",$tempRoll->method);
    
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    $typ=$base->getTypeInfo();
    foreach( $resultList as $key=>$value )
    {
        $s.=generHiddenField("resultList[$key]",$value);
    }    
    global $mainBoxWidth,$mainBoxPadding;
    if (!isset($mainBoxWidth)) $mainBoxWidth="100%";
    if (!isset($mainBoxPadding)) $mainBoxPadding="2";
    $s.=generBoxUp($mainBoxWidth,$mainBoxPadding);    
    $label = sprintf($lll["beforeMultipleDelete"], count($resultList));
    $s.="<tr><th class='header'>\n";
    $s.= $label."</th></tr>";
    $s.= "<tr class='cell'><td align='center' class='cell'>";
    $s.="<input type=submit value='$lll[ok]' name=submit class='button'>\n";
    $s.="<input type=submit value='$lll[cancel]' name=submit class='button'>\n";
    $s.="</td></tr>";
    $s.=generBoxDown();
    $s.="</form>\n";
}

function generComplexForm(&$base, &$s, &$objects)
{
    global $lll;
    global $gorumroll;

    $s="";
    $typ=$base->getTypeInfo();
    $options = $typ["complexes"][$gorumroll->method];
    $class = $gorumroll->class;
    $method = $gorumroll->method;
    // A kovetkezo method beallitasa:
    $tempRoll = $gorumroll;
    $tempRoll->method = str_replace("_form","",$tempRoll->method);
    
    $s.=$tempRoll->generFormHeaderAndHiddenFields();
    global $mainBoxWidth,$mainBoxPadding;
    if (!isset($mainBoxWidth)) $mainBoxWidth="100%";
    if (!isset($mainBoxPadding)) $mainBoxPadding="2";
    $s.=generBoxUp($mainBoxWidth,$mainBoxPadding);    
    $s.="<tr><th class='header'>";
    $s.=$lll[$class."_".$method]; // A form cime
    $s.="</th></tr>\n";
    $s.="<tr><td class='cell' align='center'>";
    $s.="<table border='0' cellpadding='10' cellspacing='0'>\n";
    $s.="<tr>";
    $s.="<td align='center' class='cell'>";
    $length = count($objects);
    $height = min($length, $options["height"]);
    $s.=$lll[$class."_".$method."_listTitle"]."<br><br>\n";
    $s.="<select name='resultList"."[]' size='$height' multiple>\n";
    if( $length )
    {
        foreach( $objects as $object )
        {
            $s.="<option value='$object->id'>";
            $txt = htmlspecialchars($object->{$options["labelAttr"]});
            if( strlen($txt)>$options["width"] )
            {
                $txt = substr($txt, 0, $options["width"]-3)."...";
            }
            $s.="$txt</option>\n";
        }
    }
    else $s.="<option value='0'>".$lll["emptyList"]."</option>\n";    
    $s.="</select>\n";
    $s.="</td>";
    $s.="<td class='cell' valign='middle'>";
    foreach( $options["buttons"] as $buttonMethod=>$selectivity )
    {
        $s.="<input type='submit' value='".
            $lll[$class."_".$method."_".$buttonMethod].
            "' name='submit' class='button'><br><br>\n";
    }    
    $s.="</td></tr></table></td></tr>";
    $s.=generBoxDown();
    $s.="</form>\n";
}

?>
