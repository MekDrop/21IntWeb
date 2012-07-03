<?php
$attach_typ = 
    array(
        "attributes"=>array(   
            "id"=>array(
                "type"=>"INT",
                "min" =>"0",
                "form hidden"
            ),
            "file"=>array(
                "type"=>"LONGBLOB"
            ),
            "mime"=>array(
                "type"=>"VARCHAR",
                "max" =>"250",
            )
        ),
        "primary_key"=>array( "id")
    );
class Attach extends Object
{
}
function storeAttachment($id)
{
    global $HTTP_POST_FILES,$attFileSize;
    global $whatHappened, $attErrTxt,$lll, $applName;
    

    if (!isset($HTTP_POST_FILES["att_file_upload"]["name"]) ||
        $HTTP_POST_FILES["att_file_upload"]["name"]=="")
    {
        return ok;
    }
    if ($HTTP_POST_FILES["att_file_upload"]["size"]>$attFileSize) {
        //direkt van ket br es nem egy p, netscape hulye!
        $attErrTxt=sprintf($lll["att_too_large"],
            $HTTP_POST_FILES["att_file_upload"]["size"],$attFileSize);
        return ok;
    }
    if ($HTTP_POST_FILES["att_file_upload"]["tmp_name"]=="none") {
        //direkt van ket br es nem egy p, netscape hulye!
        $attErrTxt=sprintf($lll["att_too_large_may"],$attFileSize);
        return ok;
    }
    if ($HTTP_POST_FILES["att_file_upload"]["size"]==0) {
        return ok;
    }
    if (!is_uploaded_file( //ONLY from php 4.02 !!!
            $HTTP_POST_FILES["att_file_upload"]["tmp_name"]))
    {
        return nok;
    }
    //vagni - TEST
    $f=fopen($HTTP_POST_FILES["att_file_upload"]["tmp_name"],"r");
    if (!$f) {
        $attErrTxt = "Error opening attached file!";
        return nok;
    }
    $file=fread($f,$HTTP_POST_FILES["att_file_upload"]["size"]);
    $file=addslashes($file);
    $query="INSERT INTO $applName"."_attach SET id=$id,file='$file'".
           ",mime='".$HTTP_POST_FILES["att_file_upload"]["type"]."'";
    $result=mysql_query($query);
    if( $result==0 ) {
        return general_mysql_error;
    }
    $query="UPDATE $applName"."_message SET downloaded=0,".
           " att_file_upload='".
           $HTTP_POST_FILES["att_file_upload"]["name"]."'".
           ",attsize='".
           $HTTP_POST_FILES["att_file_upload"]["size"]."'".
           " WHERE id=$id";
    $result=executeQuery($query);
    return ok;
}
function modAttachment($id)
{
    global $HTTP_POST_FILES,$attFileSize;
    global $whatHappened, $attErrTxt,$lll, $applName;

    if ($HTTP_POST_FILES["att_file_upload"]["name"]=="") {
        return ok;
    }
    if ($HTTP_POST_FILES["att_file_upload"]["size"]>$attFileSize) {
        //direkt van ket br es nem egy p, netscape hulye!
        $attErrTxt=sprintf($lll["att_too_large"],
            $HTTP_POST_FILES["att_file_upload"]["size"],$attFileSize);
        return ok;
    }
    if ($HTTP_POST_FILES["att_file_upload"]["tmp_name"]=="none") {
        //direkt van ket br es nem egy p, netscape hulye!
        $attErrTxt=sprintf($lll["att_too_large_may"],$attFileSize);
        return ok;
    }
    if ($HTTP_POST_FILES["att_file_upload"]["size"]==0) {
        return ok;
    }
    $f=fopen($HTTP_POST_FILES["att_file_upload"]["tmp_name"],"r");
    if (!$f) {
        $attErrTxt = "Error opening attached file!";
        return nok;
    }
    $file=fread($f,$HTTP_POST_FILES["att_file_upload"]["size"]);
    $file=addslashes($file);
    //check if exists - modify
    $query="SELECT id FROM $applName"."_attach WHERE id=$id";
    $result = executeQuery($query);
    $modify=mysql_num_rows($result);
    if ($modify) {
        $query="UPDATE $applName"."_attach SET file='$file'".
           ",mime='".$HTTP_POST_FILES["att_file_upload"]["type"]."'".
           " WHERE id=$id";
    }
    else {
        $query="INSERT INTO $applName"."_attach SET id=$id,file='$file'".
           ",mime='".$HTTP_POST_FILES["att_file_upload"]["type"]."'";
    }
    executeQuery($query);
    $query="UPDATE $applName"."_message SET downloaded=0,".
           " att_file_upload='".
           $HTTP_POST_FILES["att_file_upload"]["name"]."'".
           ",attsize='".
           $HTTP_POST_FILES["att_file_upload"]["size"]."'".
           " WHERE id=$id";
    executeQuery($query);
    return ok;
}
function showAttach()
{
    global $onlyOut,$HTTP_GET_VARS, $applName;
    if (!isset($HTTP_GET_VARS["id"])||$HTTP_GET_VARS["id"]==0) {
        $txt="id not specified for showAttach";
        handleError($txt);
    }
    $id=$HTTP_GET_VARS["id"];
    $a = new Attach;
    $a->id=$id;
    $ret=load($a);
    if ($ret!=ok) {
        handleError("Can not load attach, id:$id");
    }    
    $m = new Message;
    $m->id=$id;
    $ret=load($m);
    if ($ret!=ok) {
        handleError("Can not load message in showattach, id:$id");
    }    
    $m->downloaded++;
    $query="UPDATE $applName"."_message SET downloaded=$m->downloaded".
           " WHERE id=$id";
    executeQuery($query);
    if ($a->mime=="") {
        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"".
               "$m->att_file_upload\"");
    }
    else {
        header( "Content-type: $a->mime");
        header("Content-Disposition: inline; filename=\"".
               "$m->att_file_upload\"");
    }
    echo $a->file;
    die();
}
function checkAttachExtension($name,&$ext)
{
    global $attAllowExt,$attForbidExt;
    global $TEST_VAR;

    $ext="";
    $pos=strrpos($name,".");
    if (!$pos) {
        $TEST_VAR=1;
        return TRUE;
    }
    $ext=substr($name,$pos+1);
    if ($attAllowExt!="") {
        $allowed=explode(",",$attAllowExt);
        for($i=0;isset($allowed[$i]);$i++) {
            $allowed[$i]=trim($allowed[$i]);
        }
        return (in_array($ext,$allowed));
    }
    if ($attForbidExt!="") {
        $forbidden=explode(",",$attForbidExt);
        for($i=0;isset($forbidden[$i]);$i++) {
            $forbidden[$i]=trim($forbidden[$i]);
        }
        return (!in_array($ext,$forbidden));
    }
    return TRUE;
}
?>
