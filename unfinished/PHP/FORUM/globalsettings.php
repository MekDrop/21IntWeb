<?php
// Ha a gorum-beli globalsettings attributumok kozul, valamelyiket
// bele akarjuk tenni a modify formba, akkor a form invisible-t ki kell
// szedni:
unset($globalsettings_typ["attributes"]["settings_necessaryAuthLevel"][0]);
unset($globalsettings_typ["attributes"]["settings_blockSize"][0]);
unset($globalsettings_typ["attributes"]["settings_rangeBlockSize"][0]);
unset($globalsettings_typ["attributes"]["settings_textAreaRows"][0]);
unset($globalsettings_typ["attributes"]["settings_textAreaCols"][0]);
unset($globalsettings_typ["attributes"]["settings_showExplanation"][0]);
unset($globalsettings_typ["attributes"]["settings_language"][0]);
unset($globalsettings_typ["attributes"]["settings_headTemplate"][0]);
unset($globalsettings_typ["attributes"]["settings_upperTemplate"][0]);
unset($globalsettings_typ["attributes"]["settings_lowerTemplate"][0]);
unset($globalsettings_typ["attributes"]["settings_minPasswordLength"][0]);
unset($globalsettings_typ["attributes"]["settings_htmlTitle"][0]);
unset($globalsettings_typ["attributes"]["settings_htmlKeywords"][0]);
unset($globalsettings_typ["attributes"]["settings_htmlDescription"][0]);
//---------------------------------------------------------------------


// A gorum-beli default ertekek feluldefinialasa:
//---------------------------------------------------------------------


// Hozzarakunk meg egy de-t a moka kedveert:            
//!!! a settings.php -ban is kell modositani!!!
$globalsettings_typ["attributes"]["settings_language"]["values"]=array("en","ci","hu","it","es");
//---------------------------------------------------------------------


// Es vegul az uj attributumok listaja:
$globalsettings_typ["attributes"]["settings_timeOut"]=  
            array(
                "type"=>"INT",
                "text",
                "length"=>"4",
                "min" =>"1",
                "default"=>60,
            );   
$globalsettings_typ["attributes"]["settings_globalHotTopicNum"]=  
            array(
                "type"=>"INT",
                "text",
                "length"=>"4",
                "min" =>"1",
                "default"=>15,
            );   
$globalsettings_typ["attributes"]["settings_forumView"]=  
            array(
                "type"  =>"TINYINT",
                "values"=>array( ForumView_tree, ForumView_flat),
                "selection",
                "form invisible",//TODO
                "default"=>ForumView_flat,
            );   
$globalsettings_typ["attributes"]["settings_allowHtmlInPost"]=  
            array(
                "type"=>"TINYINT",
                "values"=>array( Group_None,Group_All,Group_OnlyAdmin,
                                 Group_OnlyAdminAndMod),
                "selection",
                "default"=>Group_OnlyAdmin,
            );   
$globalsettings_typ["attributes"]["settings_allowUbbInPost"]=  
            array(
                "type"=>"INT",
                "bool",
                "default"=>1,
            );   
$globalsettings_typ["attributes"]["settings_allowSmileyInPost"]=  
            array(
                "type"=>"INT",
                "bool",
                "default"=>1,
            );   
$globalsettings_typ["attributes"]["settings_attFileSize"]=  
            array(
                "type"=>"INT",
                "text",
                "default"=>100000,
            );   
$globalsettings_typ["attributes"]["settings_attAllowExt"]=  
            array(
                "type"=>"VARCHAR",
                "text",
                "max"=>"255",
                "default"=>"",
            );   
$globalsettings_typ["attributes"]["settings_attForbidExt"]=  
            array(
                "type"=>"VARCHAR",
                "text",
                "max"=>"255",
                "default"=>"",
            );   
$globalsettings_typ["attributes"]["settings_adminEmail"]=  
            array(
                "type"=>"VARCHAR",
                "text",
                "max"=>"255",
                "default"=>"",
            );   
$globalsettings_typ["attributes"]["settings_allowSubscriptions"]=  
            array(
                "type"=>"INT",
                "bool",
                "default"=>1,
            );                  
?>
