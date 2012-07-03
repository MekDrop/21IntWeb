<?php
$flood_typ =  
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"BIGINT",
                "auto increment"
            ),
            "flood"=>array(
                "type"=>"VARCHAR(20)"
            )
        ),
        "primary_key"=>"id"
    );
class Flood extends Object
{
}
function floodCheck($flood)
{
    global $applName,$lll;

    //TODO: lehet, hogy le kellene vizsgalni, hogy submit, vagy
    // cancel jott. Cancel-re is adunk most flood errort.
    $f = new Flood;
    $f->flood = $flood;
    $ret=load($f);
    if ($ret!=not_found_in_db) {
        handleError($lll["flood_err"]);
    }
    create($f);

    $query="SELECT * FROM $applName".
           "_flood ORDER BY id ASC LIMIT 1";
    $f2 = new Flood;
    loadSql($f2,$query);
    delete($f2);
}
?>
