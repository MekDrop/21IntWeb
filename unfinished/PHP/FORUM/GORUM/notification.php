<?php
// A notificationok applikacionkent fixek. Az installibben kell oket 
// letrehozni. A user nem hozhat letre ujat es nem torolhet ki egyet 
// sem, maximum modosithatja oket

$dbClasses[]="notification";

$notification_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "form hidden",
            ),    
            "fixRecipent"=>array(
                "type"=>"INT",
                "bool",
                "default"=>"0",
                "form hidden"
            ),    
            "fixCC"=>array(
                "type"=>"INT",
                "bool",
                "default"=>"1",
                "form hidden"
            ),            
            "recipent"=>array(
                "type"=>"VARCHAR",
                "max" =>"255",
                "text",
                "mandatory",
            ),            
            "cc"=>array(
                "type"=>"VARCHAR",
                "max" =>"255",
                "text",
                "details",
            ),            
            "title"=>array(
                "type"=>"VARCHAR",
                "max" =>"120",
                "list",
                "details",
                "form readonly"
            ),
            "subject"=>array(
                "type"=>"VARCHAR",
                "max" =>"120",
                "min" =>"1",
                "text",
                "mandatory",
                "details",
            ),
            "variables"=>array(
                "type"=>"TEXT",
                "details",
                "form readonly",
            ),
            "body"=>array(  
                "type"=>"TEXT",
                "textarea",
                "cols"=>50,
                "rows"=>5,
                "mandatory",
                "details",
            ),    
            "active"=>array(
                "type"=>"INT",
                "bool",
                "default"=>"1",
                "list",
                "details",
            )
        ),    
        "primary_key"=>"id",
        "sort_criteria_attr"=>"id",
        "sort_criteria_dir"=>"d"
    );

class Notification extends Object
{
    function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
    {
        global $generalRight, $lll;
        hasAdminRights($isAdm);
        $hasRight = ($isAdm && $method==Priv_modify) || $method==Priv_load;
        $generalRight = TRUE;
        if( !$hasRight && $giveError )
        {
            handleError($lll["permission_denied"]);
        }
        return ok;
    } 
     
    function showDetailsTool()
    {
        return "";
    }   
     
    function showListVal($attr)
    {
        global $gorumroll, $lll;
    
        $s="";
        if( $attr=="active" ) {
            if ($this->active) $s=$lll["yes"];
            else $s=$lll["no"];
        }
        elseif ($attr=="title") {
            if( $gorumroll->method=="showhtmllist" )
            {
                $tempRoll = new Roll;
                $tempRoll->method = "showdetails";
                $tempRoll->list = "notification";
                $tempRoll->rollid = $this->id;
                saveInFromFrom($tempRoll);
                $s.=$tempRoll->generAnchor($this->title, "itemtitle");
            }
            else $s=htmlspecialchars($this->{$attr});
        }    
        elseif ($attr=="body") {
            $s=nl2br(htmlspecialchars($this->{$attr}));
        }    
        else
        {
            $s=htmlspecialchars($this->{$attr});
        }
        return $s;
    }    
    
    function showDetails(&$s, $whereFields="", $withLoad=TRUE,$headText="")
    {
        global $notification_typ, $gorumroll;
        
        $this->id = $gorumroll->rollid;
        load($this);
        if( $this->fixRecipent )
        {
            $notification_typ["attributes"]["recipent"][]="details";
        }
        else
        {
            $notification_typ["attributes"]["recipent"][]="form invisible";
        }
        showDetails($this, $s, $whereFields, FALSE);
    }
    
    function generForm(&$s)
    {
        global $notification_typ, $gorumroll;
        
        if( $this->fixRecipent )
        {
            $notification_typ["attributes"]["recipent"][]="details";
        }
        else
        {
            $notification_typ["attributes"]["recipent"][]="form invisible";
        }
        generForm($this, $s);
    }
    
    function modify()
    {
        global $notification_typ;
        
        if( $this->fixRecipent )
        {
            $notification_typ["attributes"]["recipent"]["min"]=1;
        }
        privilegeModify($this);
    } 
    
    function send()
    {
        global $adminEmail, $htmlNotifications;
        
        $from = $adminEmail ? "From: $adminEmail" : "";
        $variableNames = explode(", ", $this->variables);
        $variables = func_get_args(); 
        if( $this->fixRecipent ) $to = $this->recipent;
        else $to = array_shift($variables); // levagjuk a to-t
        if( $this->fixCC ) $cc = $this->cc;
        else $cc = array_shift($variables); // levagjuk a cc-t
        // Ha programozott cc is van es db-bol jovo cc is van:
        if( !$this->fixCC && $this->cc ) $cc = array($cc, $this->cc);
        foreach( $variableNames as $v ) $$v=array_shift($variables);
        eval ("\$mailText = \"$this->body\";");
        $err = gmail($adminEmail, $to, $this->subject, $mailText, 
                     $htmlNotifications, "", "", $cc);             
        if( $err )
        {
        }
    }
}
?>