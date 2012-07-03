<?php
$search_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "form hidden"
            ),            
            "name"=>array(
                "type"=>"VARCHAR",
                "text",
                "max" =>"250",
                "mandatory",
                "create_form: form invisible",
                "list"
            ),            
            "autoNotify"=>array(
                "type"=>"INT",
                "bool",
                "create_form: form invisible",
                "list"
            ),            
            "str"=>array(
                "type"=>"VARCHAR",
                "max" =>"200",
                "min" => "1",
                "mandatory",
                "text",
                "no column",
                "modify_form: form invisible"
            ),
            "type"=>array(
                "type"=>"INT",
                "selection",
                "values"=>array(search_all,search_any),
                "no column",
                "modify_form: form invisible"
            ),            
            "query"=>array(
                "type"=>"TEXT",
                "form invisible"
            )            
        ),    
        "primary_key"=>"id, name"
    );


class Search extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $lll,$generalRight, $gorumuser, $gorumrecognised;

        $generalRight = FALSE;
        $hasRight=TRUE;
    } 
    
    function create($overrulePrivilege=FALSE)
    {
        global $gorumuser,$infoText, $whatHappened, $itemClassName;
        global $gorumroll, $searchClassName;
        
        // eloszor kitoroljuk, ha mar letezik:
        $search = new $searchClassName;
        $search->id = $gorumuser->id;
        delete($search);        
        $this->id=$gorumuser->id;
        // TODO: valid
        $this->query = $this->makeSearchQuery();
        Object::create($overrulePrivilege);
        // Csak akkor kell infoText, ha invalid a form:
        if( $whatHappened=="form_submitted" ) 
        {
            $infoText = "";
            $gorumroll->frommethod="showhtmllist";
            $gorumroll->fromlist = $itemClassName."_search";
        }
    }
    
    function makeSearchQuery()
    {
        global $gorumuser, $item_typ;
    
        $words = explode(" ",$this->str);
        $op = ($this->type==search_all) ? "AND" : "OR";
        $condition="( ";
        $first=TRUE;
        foreach($words as $word) {
            if (!$first) $condition.=" $op ";
            $condition.="( n.title LIKE '%$word%'";
            foreach( $item_typ["attributes"] as $attr=>$attrInfo )
            {
                if( in_array("searchable", $attrInfo) )
                {
                    $condition.=" OR n.$attr LIKE '%$word%'";
                }
            }
            $condition.=")";
            $first=FALSE;
        }
        $condition.=" ) ";
    
        return $condition;
    }
    
    function showSpecialSearchFunctions()
    {
        global $gorumroll, $gorumuser, $lll, $searchClassName;
        global $saveSearchSupport;
        $s="";
        if( !$saveSearchSupport )
        {
            return $s;
        }
        $s.="</td></tr>\n";
        $s.="<tr><td align='left'>";
        $s.="<table cellspacing='10' cellpadding='0' border='0' class='cell'>\n";
        $s.="<tr><td align='left' class='search'>";
        $tempRoll = $gorumroll;
        $tempRoll->list = $searchClassName;    
        $tempRoll->method = "modify_form";
        $tempRoll->rollid = 0;
        $tempRoll->autoNotify = FALSE;
        saveInFrom($tempRoll);
        $s.=$tempRoll->generAnchor($lll["clickHere"], "cell");
        $s.=$lll["saveSearch"];
        $s.="</td></tr>\n";
        $s.="<tr><td align='left' class='search'>";
        $tempRoll->autoNotify = FALSE;
        $s.=$tempRoll->generAnchor($lll["clickHere"], "cell");
        $s.=$lll["applyAutoNotify"];
        $s.="</td></tr>\n";
        $s.="<tr><td align='left' class='search'>";
        $tempRoll = $gorumroll;
        $tempRoll->list = $searchClassName;    
        $tempRoll->method = "showhtmllist";
        $tempRoll->rollid = $gorumuser->id;
        saveInFrom($tempRoll);
        $s.=$tempRoll->generAnchor($lll["clickHere"], "cell");
        $s.=$lll["viewSavedSearches"];
        $s.="</td></tr></table>\n";
        $s.="</td></tr>\n";
        return $s;
    }
    
    function modify($whereFields="", $overrulePrivilege=FALSE)
    {
        global $gorumuser, $infoText, $whatHappened, $searchClassName;
        global $lll;
        
        $this->id = $gorumuser->id;
        if( !$this->name )
        {
            $whatHappened = "invalid_form";
            $infoText = $lll["searchNameMustBeFilledOut"];
            return;
        }
        $searchCheck = new $searchClassName;
        $searchCheck = $this;
        if( !$searchCheck->load(array("id", "name")) )
        {
            $whatHappened = "invalid_form";
            $infoText = $lll["searchNameExists"];
            return;
        }
        Object::modify($whereFields,$overrulePrivilege);
    }
    
}


?>
