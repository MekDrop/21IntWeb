<?php
// Ha a gorum-beli settings attributumok kozul, valamelyiket
// bele akarjuk tenni a personalize-ba, akkor a form invisible-t ki kell
// szedni:
unset($settings_typ["attributes"]["settings_blockSize"][0]);
unset($settings_typ["attributes"]["settings_rangeBlockSize"][0]);
unset($settings_typ["attributes"]["settings_textAreaRows"][0]);
unset($settings_typ["attributes"]["settings_textAreaCols"][0]);
unset($settings_typ["attributes"]["settings_showExplanation"][0]);
unset($settings_typ["attributes"]["settings_language"][0]);
//---------------------------------------------------------------------


$settings_typ["attributes"]["settings_language"]["values"]=array("en","ci","hu","it","es");


$settings_typ["attributes"]["settings_timeOut"]=  
            array(
                "type"=>"INT",
                "text",
                "length"=>"4",
                "min" =>"1",
            );   
$settings_typ["attributes"]["settings_globalHotTopicNum"]=  
            array(
                "type"=>"INT",
                "text",
                "length"=>"4",
                "min" =>"1",
            );   
?>
