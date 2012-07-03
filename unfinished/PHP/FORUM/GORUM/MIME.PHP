<?php
/*
$head = <<< EOF
From: zorum@zorum.com
Subject: mi va?
MIME-Version: 1.0
Content-Type: image/jpg; name=new.gif
Content-Transfer-Encoding: base64
Content-Description: Take a look at him yourself


EOF;
$body = <<< EOF
duma

EOF;
*/
//$body = join ('', file ('new.gif'));
//$body = base64_encode($body);
//$head.=$body;

$head = <<< EOF
From: zorum@zorum.com
Subject: mi va?
MIME-Version: 1.0
Content-Transfer-Encoding: base64
Content-Type: text/html; charset=ISO-8859-1


EOF;
$body = <<< EOF
This the <b>HTML</b> version!
<img src='http://www.zend.com/images/zend_logo.gif'><br>
<i>Zolka</i>

EOF;
$head.= chunk_split(base64_encode($body));

$to="test1@phpoutsourcing.com";
mail($to,"","",$head);
echo "<br>END";
?>
