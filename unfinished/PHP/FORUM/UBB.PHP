<?php

$ubb_typ =
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "auto increment",
                "form hidden"
            ),
            "ubb"=>array(
                "type"=>"VARCHAR",
                "no column",
                "form invisible"
            ),
            "ubbBegin"=>array(
                "type"=>"VARCHAR",
                "text",
                "list",
                "details",
                "max" =>"255",
                "min" =>"1",
                "mandatory"
            ),
            "replacementBegin"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
                "mandatory"
            ),
            "ubbEnd"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "replacementEnd"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"255",
            ),
            "enabled"=>array(
                "type"  =>"INT",
                "bool",
                "default"=>"0",
            ),            
            "comment"=>array(
                "type"=>"TEXT",
                "textarea",
                "rows" => 10,
                "cols" => 50,
                "details",
                "list"
            )
        ),
        "primary_key"=>"id",
        "delete_confirm"=>"comment",
        "sort_criteria_attr"=>"comment",
        "sort_criteria_dir"=>"a"
    );
    
class Ubb extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $lll;
        global $generalRight;
        hasAdminRights($isAdm);
        $hasRight = ($method==Priv_load || $isAdm);
        $generalRight = TRUE;
        if( !$hasRight && $giveError )
        {
            handleError($lll["permission_denied"]);
        }
        return ok;
    }
          
    function loadObjectsSQL( $query="",&$objArr) 
    {
        global $includeUbbCodeTag;
        $includeUbbCodeTag = FALSE;
        $ret = loadObjectsSQL( $this, $query, $objArr);
        if( $ret ) 
        {
            return $ret;
        }
        $length = sizeof($objArr);
        for( $i=0; $i<$length; $i++ )
        {
            if( $objArr[$i]->enabled )
            {
                if( isset($objArr[$i]->ubbEnd) && $objArr[$i]->ubbEnd!="" ) 
                {            
                    ereg( "(\[[[:alpha:]]+)", $objArr[$i]->ubbBegin, $regs );
                    $objArr[$i]->ubb = $regs[1];
                } 
                if($objArr[$i]->ubbBegin=="\[code\]") $includeUbbCodeTag=TRUE;
            }
        }
    }        
    
    function showListVal($attr)
    {    
        global $lll;
        $s = $this->{$attr};
        if( $attr=="comment" ) applyUbbDecoding($this, $s);
        elseif( $attr=="enabled" ) $s = $s ? $lll["yes"] : $lll["no"];
        else $s = htmlspecialchars($s);
        return $s;
    }
    
    function showHtmlList(&$s)
    {
        hasAdminRights($isAdm);
        $typ = & $this->getTypeInfo();
        if( $isAdm )
        {
            $typ["attributes"]["replacementBegin"][]="list";
            if( isset($typ["attributes"]["ubbEnd"]) ) {
                $typ["attributes"]["ubbEnd"][]="list";
            }
            if( isset($typ["attributes"]["replacementEnd"]) ) {
                $typ["attributes"]["replacementEnd"][]="list";
            }
            $typ["attributes"]["enabled"][]="list";
        }
        showHtmlList($this, $s);
    }
    
    function showDetails(&$s, $whereFields, $withLoad=TRUE)
    {
        $typ = & $this->getTypeInfo();
        hasAdminRights($isAdm);
        if( $isAdm )
        {
            $typ["attributes"]["replacementBegin"][]="details";
            if( isset($typ["attributes"]["ubbEnd"]) ) {
                $typ["attributes"]["ubbEnd"][]="details";
            }
            if( isset($typ["attributes"]["replacementEnd"]) ) {
                $typ["attributes"]["replacementEnd"][]="details";
            }
            $typ["attributes"]["enabled"][]="details";
        }
        showDetails($this, $s, $whereFields, $withLoad);
    }
} 
   
function applyUbbDecoding( $base, &$s , $noSmilies=FALSE)
{
    global $includeUbbCodeTag;
      
    if( !isset($base->coding) || !isset($base->smiley) )
    {
        // Nem message, nem topic, hanem valoszinuleg az ubbHandler
        // comment attributuma van teriteken
        $base->coding=MessCode_ubb;
        $base->smiley=TRUE;
    }
    if( $noSmilies ) $base->smiley=FALSE;
    $type = ($base->smiley) ? 
            array("smiley") : array();
    if( $base->coding==MessCode_no )
    {
        $s=htmlspecialchars($s);
        $s=nl2br($s);
        $s=decodeTxt( $s, $type );
    }
    elseif( $base->coding==MessCode_html )
    {
        $s=decodeTxt( $s, $type );
    }
    elseif( $base->coding==MessCode_ubb )
    {
        $type[]="ubb";
        if( !$includeUbbCodeTag )
        {
            $s=htmlspecialchars($s);
            $s=nl2br($s);
            $s = decodeTxt( $s, $type );
            return;
        }
        $splittedTxt = array();
        $splittedTxt[1] = $s;
        $s = "";
        while( isset($splittedTxt[1]) && $splittedTxt[1]!="" )
        {
            $splittedTxt = explode( "[code]", $splittedTxt[1], 2 );
            $splittedTxt[0]=htmlspecialchars($splittedTxt[0]);
            $splittedTxt[0]=nl2br($splittedTxt[0]);
            $s .= decodeTxt( $splittedTxt[0], $type );
            if( isset($splittedTxt[1]) && $splittedTxt[1]!="" )
            {
                $s .= "<pre>";
                $splittedTxt = explode( "[/code]", $splittedTxt[1], 2 );
                $s .= $splittedTxt[0];
                $s .= "</pre>";
            }
        }
    }
    return;
}

function decodeTxt( $s , $type )
{
    global $gorumreplacementrules;
    foreach( $gorumreplacementrules as $replacement )
    {
        if( !$replacement->enabled || 
            !in_array(get_class($replacement), $type)) continue;
        if( isset($replacement->ubbEnd) && $replacement->ubbEnd!="" )
        {
            $count = substr_count($s, $replacement->ubb);
            // Ez most nagyon csunya, de mit mast lehetne tenni:
            if( $replacement->replacementEnd=="<hr></blockquote>" )
            {
                for( $i=0; $i<$count; $i++ ) 
                    $s.="</blockquote>";
            }
            else
            {
                for( $i=0; $i<$count; $i++ ) 
                    $s.=$replacement->replacementEnd;
            }        
        }  
        $s = @eregi_replace( $replacement->ubbBegin, 
                            $replacement->replacementBegin, $s );
       if( isset($replacement->ubbEnd) && $replacement->ubbEnd!="" ) 
       {    
                            
            $s = @eregi_replace( $replacement->ubbEnd, 
                                $replacement->replacementEnd, $s );
       }                          
    }
    return $s;
}
  
?>
