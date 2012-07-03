<?php

$settings_typ =  
    array(
        "attributes"=>array(   
            "userId"=>array(
                "type"=>"INT",
                "min" =>"1",
                "form hidden"
            ),
            "settings_blockSize"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"4",
                "max" =>"1000",
                "min" =>"1",
            ),
            "settings_rangeBlockSize"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"4",
                "max" =>"1000",
                "min" =>"1",
            ),
            "settings_textAreaRows"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"3",
                "min" =>"1",
            ),
            "settings_textAreaCols"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"3",
                "min" =>"10",
            ),
            "settings_showExplanation"=>array(
                "form invisible",
                "type"  =>"TINYINT",
                "values"=>array(Explanation_text,
                                Explanation_qhelp,
                                Explanation_no),
                "selection",
            ),
            "settings_language"=>array(
                "form invisible",
                "type"  =>"VARCHAR",
                "values"=>array("en","hu"),
                "max"=>"2",
                "selection",  
            )
        ),
        "primary_key"=>"userId"
    );

class Settings extends Object
{

function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $gorumrecognised, $lll;
    
    global $gorumrecognised;
    $hasRight = ($method==Priv_modify && $gorumrecognised);
    if( !$hasRight && $giveError )
    {
        handleError($lll["permission_denied"]);
    }
}

function initGlobals()
{
    global $settings_typ;
    
    foreach( array_keys($settings_typ["attributes"]) as $attr )
    {
        if( $attr=="userId" ) continue;
        $global_name = str_replace("settings_", "", $attr );
        global ${$global_name};
        ${$global_name} = $this->{$attr};
    } 
    $this->initializeTypeInfos();
}

function load() 
{
    global $gorumuser, $settings_typ;
    

    $this->userId = $gorumuser->id;
    if( load($this) )
    {
        $globalSettings = new GlobalSettings;
        $globalSettings->load();
        foreach( array_keys($settings_typ["attributes"]) as $attr )
        {
            if( $attr!="userId" ) 
            {   // masolas:
                $this->{$attr} = $globalSettings->{$attr};
            }    
        }
    }
    return ok;
}  

function modify() 
{
    global $gorumuser;
    global $lll;
    global $zorumroll;
    global $whatHappened, $infoText;
    
    $whatHappened = "form_submitted";
    $this->hasObjectRights( $hasRight, Priv_modify, TRUE );

    $settingsCheck = new Settings;
    $settingsCheck->userId = $gorumuser->id;
    load($settingsCheck) ? create($this) : modify($this);
    if( $whatHappened=="invalid_form" )
    {
        return ok;
    }
    
    $this->initGlobals();
}   

function initializeTypeInfos()
{
}

    
}    
?>
