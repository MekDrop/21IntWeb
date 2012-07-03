<?php
$groupmember_typ =
    array(
        "attributes"=>array(   
            "groupId"=>array(
                "type"=>"INT",
                "min" =>"0",
            ),            
            "userId"=>array(
                "type"=>"INT",
                "min" =>"1",
            )
        ),
        "primary_key"=>array("groupId","userId")
    );

class GroupMember extends Object
{

}

?>