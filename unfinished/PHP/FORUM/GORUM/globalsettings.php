<?php

$globalsettings_typ =  
    array(
        "attributes"=>array(   
            "settings_necessaryAuthLevel"=>array(
                "form invisible",
                "type"  =>"TINYINT",
                "values"=>array(Loginlib_BasicLevel,Loginlib_LowLevel),
                "selection",
                "default"=>"$necessaryAuthLevel",
            ),
            "settings_blockSize"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"3",
                "min" =>"1",
                "default"=>"$blockSize",
            ),
            "settings_rangeBlockSize"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"3",
                "min" =>"1",
                "default"=>"$rangeBlockSize",
            ),
            "settings_textAreaRows"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"3",
                "min" =>"1",
                "default"=>"$textAreaRows",
            ),
            "settings_textAreaCols"=>array(
                "form invisible",
                "type"=>"INT",
                "text",
                "length"=>"3",
                "min" =>"10",
                "default"=>"$textAreaCols",
            ),
            "settings_showExplanation"=>array(
                "form invisible",
                "type"  =>"TINYINT",
                "values"=>array(Explanation_text,
                                Explanation_qhelp,
                                Explanation_no),
                "selection",
                "default"=>"$showExplanation",
            ),
            "settings_language"=>array(
                "form invisible",
                "type"  =>"VARCHAR",
                "values"=>array("en","hu"),
                "selection",  
                "max"=>"2",
                "default"=>$language,
            ),
            "settings_headTemplate"=>array(
                "form invisible",
                "type"  =>"TEXT",
                "textarea",
                "rows" => 5,
                "cols" => 50,
                "default"=>$headTemplate,
            ),
            "settings_upperTemplate"=>array(
                "form invisible",
                "type"  =>"TEXT",
                "textarea",
                "rows" => 10,
                "cols" => 50,
                "default"=>$upperTemplate,
            ),
            "settings_lowerTemplate"=>array(
                "form invisible",
                "type"  =>"TEXT",
                "textarea",
                "rows" => 10,
                "cols" => 50,
                "default"=>$lowerTemplate,
            ),
            "settings_minPasswordLength"=>array(
                "form invisible",
                "type"  =>"TINYINT",
                "length"=>"2",
                "text",
                "default"=>"$minPasswordLength",
            ),
            "settings_htmlTitle"=>array(
                "form invisible",
                "type"  =>"VARCHAR",
                "text",
                "max"  =>"255",
                "default"=>$htmlTitle,
            ),
            "settings_htmlKeywords"=>array(
                "form invisible",
                "type"  =>"VARCHAR",
                "text",
                "max"  =>"255",
                "default"=>$htmlKeywords,
            ),
            "settings_htmlDescription"=>array(
                "form invisible",
                "type"  =>"VARCHAR",
                "text",
                "max"  =>"255",
                "default"=>$htmlDescription,
            ),
            "settings_adminEmail"=>array(
                "form invisible",
                "type"  =>"VARCHAR",
                "text",
                "max"  =>"255",
                "default"=>$adminEmail,
            )
        )
    );

class GlobalSettings extends Object
{

function hasObjectRights(&$hasRight, $method, $giveError=FALSE)
{
    global $gorumrecognised, $lll;
    
    hasAdminRights($isAdm);
    $hasRight = ($method==Priv_modify && $isAdm);
    if( !$hasRight && $giveError )
    {
        handleError($lll["permission_denied"]);
    }
}

function initGlobals()
{
    global $globalsettings_typ;
    
    $typ = & $globalsettings_typ;
    foreach( array_keys($typ["attributes"]) as $attr )
    {
        if( isset($this->{$attr}) )
        {
            $global_name = str_replace("settings_", "", $attr );
            global ${$global_name};
            ${$global_name} = $this->{$attr};
        }
    } 
    $this->initializeTypeInfos();
    if( class_exists("settings") )
    {
        $settings = new Settings;
        $settings->load();
        $settings->initGlobals();
        $settings->initializeTypeInfos();
    }
}

function load() 
{

    if( loadSql($this) ) $this->resetObject();

}  

function resetObject()
{
    global $globalsettings_typ;
    foreach( array_keys($globalsettings_typ["attributes"]) as $attr )
    {
        $this->{$attr} = $this->getDefault($globalsettings_typ, $attr);
    }    
}

function modify() 
{
    global $gorumuser;
    global $lll;
    global $zorumroll;
    global $whatHappened, $infoText;
    
    $whatHappened = "form_submitted";
    $this->hasObjectRights( $hasRight, Priv_modify, TRUE );

    $settingsCheck = new GlobalSettings;
    loadSql($settingsCheck) ? create($this) : modify($this);
    // Ha az uj settingek ervenybe lepese elott az applikacioban meg 
    // valamit akciozni kell:
    if( function_exists("appGlobalSettingsAction") ) appGlobalSettingsAction($this);
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
