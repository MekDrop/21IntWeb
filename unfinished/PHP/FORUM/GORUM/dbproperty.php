<?php
function executeQuery($query) 
{   
    $result=mysql_query($query);
    if( $result==0 ) {
        handleError("mysql");
    }
    return $result;
}

function loadSQL( &$base, $query="") 
{   
    global $applName;
    
    if( $query=="" ) {
        $query = "SELECT * FROM $applName"."_".get_class($base);
        //TODO: default order by nem kellene?
    }
    $row=mysql_fetch_array(executeQuery($query), MYSQL_ASSOC);
    if( $row==0 ) {
        return not_found_in_db;
    }
    $base->init( $row );
    return ok;
}

function loadObjectsSQL( $base, $query="",&$objArr) 
{   
    global $applName;
    
    $objArr = array();
    $className=get_class($base);
    if( $query=="" ) {
        $query = "SELECT * FROM $applName"."_$className";
        //TODO: order by?
    }
    $result=executeQuery($query);
    $num = mysql_num_rows($result);
    if ($num==0) {
        return not_found_in_db;
    }
    for($i=0;$i<$num;$i++) {
        $row=mysql_fetch_array($result, MYSQL_ASSOC);
        $objArr[$i] = new $className;
        $objArr[$i]->init($row);
    }
    return ok;
}

function load( &$base, $whereFields="", $whatFields="*" ) 
{   
    global $applName;
    $query = $base->getSelect();
    if( !$query )
    {
        $typ = $base->getTypeInfo();
        $tableName = $applName."_".get_class($base);
        $firstField = TRUE;
        $query = "SELECT $whatFields FROM $tableName";
        if( $whereFields=="" ) $whereFields=getPrimaryKey( $typ );
        foreach( $whereFields as $index=>$attribute ) {
            if( !isset($base->{$attribute}) ) unset($whereFields[$index]);
        }
        if( sizeof($whereFields)==0 ) {
            $whereFields = array_keys( $typ["attributes"] );
            foreach( $whereFields as $index=>$attribute ) {
                if(!isset($base->{$attribute})) unset($whereFields[$index]);
            }
        }
        foreach( $whereFields as $key ) {
            if( isset($base->{$key}) ) {
                $value = $base->{$key};
                $value=addcslashes($value,"'\\");
                if( $firstField ) {
                    $query .= " WHERE $tableName.$key='$value'";
                    $firstField = FALSE;
                }
                else $query .= " AND $tableName.$key='$value'";
            }
        }
    }
    $ret = loadSQL( $base, $query );
    return $ret;
}

function create(&$base)
{
    global $whatHappened,$applName,$globQuery;
    $base->valid();
    if( $whatHappened=="invalid_form" )
    {
        return ;
    }
    
    $typ = $base->getTypeInfo();
    $className = get_class( $base );
    $query =  "INSERT INTO $applName"."_$className";
    $aia = getAutoIncrementAttribute( $typ );
    $s = getCreateSetStr($base, $typ);
    if( $s!="" ) $query.=" SET $s";
    $globQuery=$query;
    executeQuery($query);
    // Setting the auto increment key:
    if( $aia )  $base->{$aia} = mysql_insert_id();
}

function getCreateSetStr($base, $typ , $create=TRUE)
{
    $object_vars = get_object_vars($base);
    $firstField = TRUE;
    $query = "";
    $typ = $base->getTypeInfo();
    foreach( $object_vars as $attribute=>$value )
    {
        if( !in_array("no column", $typ["attributes"][$attribute]) && 
            ($attribute!="creationtime" || !$create) && 
            $attribute!="modificationtime")
        {
            if( !$firstField ) $query .= ", ";
            if( is_array($value) ) // vagy multipleselection, vagy date
            {
                if( ereg("INT", $typ["attributes"][$attribute]["type"])
                    && isset($value["month"]))  // date
                {
                    $h = isset($value["hour"]) ? $value["hour"] : 0;
                    $m = isset($value["minute"]) ? $value["minute"] : 0;
                    $value = mktime($h,$m,0,$value["month"],$value["day"],
                                    $value["year"]);
                    
                }
                elseif(ereg("DATE",
                       $typ["attributes"][$attribute]["type"]))
                {
                    $value = "$value[year]-$value[month]-$value[day]";
                }
                else // multipleselection
                {
                    $value = join( ",", $value );
                }
            }    
            $value=addcslashes($value,"'\\");
            $query .= "$attribute='$value'";
            $firstField = FALSE;
        }
    }
    if (isset($typ["attributes"]["creationdatetime"])&&$create)
    {
        if( !$firstField ) $query .= ", ";
        $query.="creationdatetime=NOW()";
        $firstField = FALSE;
    }
    if (isset($typ["attributes"]["creationtime"])&&$create)
    {
        if( !$firstField ) $query .= ", ";
        $query.="creationtime='".time()."' ";
        $firstField = FALSE;
    }
    if (isset($typ["attributes"]["modificationtime"]))
    {
        if( !$firstField ) $query .= ", ";
        $query.="modificationtime='".time()."'";
        $firstField = FALSE;
    }
    return $query;
} 
 
function modify( $base, $whereFields="" )
{
    global $whatHappened,$infoText,$applName,$globQuery;
    
        //TODO: mi van a szopaccsal?
    $ret = $base->valid();
    if( $ret || $whatHappened=="invalid_form" )
    {
        return $ret;
    }
    
    $typ = $base->getTypeInfo();
    $query="UPDATE $applName"."_".get_class($base)." SET ";
    $query .= getCreateSetStr($base, $typ, FALSE);
    $whereExists = FALSE;
    if( $whereFields=="" ) $whereFields=getPrimaryKey( $typ );
    if( $whereFields )
    {
        $firstField = TRUE;
        foreach( $whereFields as $key )
        {
            if( isset($base->{$key}) )
            {
                $value = $base->{$key};
                $value=addcslashes($value,"'\\");
                if( $firstField )
                {
                    $whereOK=TRUE;
                    $query .= " WHERE $key='$value'";
                    $firstField = FALSE;
                    $whereExists = TRUE;
                }
                else $query .= " AND $key='$value'";
            }
        }
    }
    if (stristr($query,"globalsettings SET")) $whereExists=TRUE;
    $globQuery=$query;
    if( $whereExists ) executeQuery($query);
    else reportError($query);
    return ok;
}

function delete( $base, $whereFields="" )
{
    global $applName,$globQuery;
    
    if (isset($base->up)) {//recursive delete
        // Ha veletlenul a whereFields nem az id, akkor az id-t be kell
        // tolteni, mert az kell a rekurziv delete-ben:
        if (!isset($base->id)) {
            $ret = $base->load($whereFields);
            if ($ret==not_found_in_db) {
                return $ret;
            }
        }
        getChildrenFromDb($base,$children);
        foreach($children as $child) $child->delete($whereFields);
    }    
    $typ = $base->getTypeInfo();
    $query="DELETE FROM $applName"."_".get_class($base);
    $whereExists = FALSE;
    if( $whereFields=="" ) $whereFields=getPrimaryKey( $typ );
    if( $whereFields )
    {
        $firstField = TRUE;
        foreach( $whereFields as $key )
        {
            if( isset($base->{$key}) )
            {
                $value = $base->{$key};
                $value=addcslashes($value,"'\\");
                if( $firstField )
                {
                    $query .= " WHERE $key='$value'";
                    $firstField = FALSE;
                    $whereExists = TRUE;
                }
                else $query .= " AND $key='$value'";
            }
        }
    }
    $globQuery=$query;
    if( $whereExists ) executeQuery($query);
    else reportError($query);
    return ok;
} 

function getPrimaryKey( $typ )
{
    
    if( isset($typ["primary_key"]) )
    {
        $primaryKey = &$typ["primary_key"];
        if( is_array($primaryKey) )
        {
            return $primaryKey;
        }
        elseif( strstr($primaryKey, ",") )
        {
            return split(", *", $primaryKey);
        }
        else
        {
            return array($primaryKey);
        }
    }
    return ok;
}

function getAutoIncrementAttribute( $typ )
{
    foreach( $typ["attributes"] as $attribute=>$attrInfo )
    {
        if( in_array("auto increment", $attrInfo))
        {
            return $attribute;
        }
    }
    return ok;
}
function connectDb($host="",$user="",$pw="",$db="")
{
    $ret = mysql_connect($host, $user, $pw);
    if (!$ret) {
        $txt="Mysql connection failed. Host: $host, Username: $user";
        handleError($txt);
    }
    $ret=mysql_select_db($db);
    if (!$ret) {
        $txt="Mysql select database failed. Database name: $db";
        handleError($txt);
    }
}

function getDbCount( &$count, $query)
{
    $row=mysql_fetch_row(executeQuery($query));
    $count=$row[0];
}
    
function lock($tables)
{
    $query="LOCK TABLES ";
    if (is_string($tables)) $query.=$tables." WRITE";
    else {
        $notfirst=FALSE;
        foreach($tables as $table) {
           if ($notfirst) $query.=",";
           $query.=$table." WRITE";
           $notfirst=TRUE;
        }
    }
    executeQuery($query);
}  
  
function unlock()
{
    executeQuery("UNLOCK TABLES");
} 
   
function getAllColumns( $typ, $tableAlias )
{
    $s = "";
    foreach( $typ["attributes"] as $attr=>$attrInfo )
    {
        if( !in_array("no column", $attrInfo) )
        {
            $s.="$tableAlias.$attr AS $attr, ";
        }
    }
    return $s;
}

function reportError($query)
{
    global $HTTP_POST_VARS,$HTTP_GET_VARS;
    $txt="";
    $txt.="query = $query\n\n";
    $txt.="POST VARS\n";
    foreach($HTTP_POST_VARS as $key=>$value) {
        $txt.="$key = $value\n";
    }
    $txt.="\n\nGET VARS\n";
    foreach($HTTP_GET_VARS as $key=>$value) {
        $txt.="$key = $value\n";
    }
    mail("contact@phpoutsourcing.com","Serious error",$txt);
    handleError("A serious error is occured. If you want to help the developers to find this error, send a mail to <a href='mailto:contact@phpoutsourcing.com'>contact@phpoutsourcing.com</a> and describe the steps you have made before this error occured. The better your description is, the easier we can find the bug. Thanks.");
}

// A masik applikacio adatbazisaban vegrehajt egy query-t:
function alignApplications($appDirName, $query)
{
    global $hostName, $dbUser, $dbUserPw, $dbName;
    
    $dbUser_orig=$dbUser;
    $dbUserPw_orig=$dbUserPw;
    $dbName_orig=$dbName;
    $hostName_orig=$hostName;
    include("../$appDirName/config.php");
    mysql_close();
    connectDb($hostName,$dbUser,$dbUserPw,$dbName);
    
    executeQuery($query);
    
    $dbUser=$dbUser_orig;
    $dbUserPw=$dbUserPw_orig;
    $dbName=$dbName_orig;
    $hostName=$hostName_orig;
    mysql_close();
    connectDb($hostName,$dbUser,$dbUserPw,$dbName);
}
?>
