<html>
<head>
<title>21 Int</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1257">
<meta http-equiv="Page-Enter" CONTENT="RevealTrans(Duration=1,Transition=16)">
<script language="JavaScript">
function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_changeProp(objName,x,theProp,theValue) { //v3.0
  var obj = MM_findObj(objName);
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)) eval("obj."+theProp+"='"+theValue+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function GoTo(URL){
	location.href=URL;
}

</script>

<?
  $MeniuNormaliSpalva="rgb(235,235,235)";
  $MeniuUzvestaSpalva="rgb(205,210,200)";
?>
<style type="text/css">
<!--
  .meniusritis{background: #333300; width: 125px; color: #CCCCCC; font: Arial, Helvetica, sans-serif; font-size: 14px; text-align: center}
  .meniufx-normalus{background: <?=$MeniuNormaliSpalva?>;color: #627262;width: 134px; cursor: pointer;font-size:14px;font-name:times new roman}
  .mygtukas{border: 1px black solid; background-color: <?=$MeniuNormaliSpalva?>; font-family: Arial, Helvetica, san-serif; font-size: 10px; font-style: normal; line-height: normal; font-weight: bolder; font-variant: normal; text-transform: none; color: #000000; text-decoration: none}
  .ivedimolaukelis{width: 100%;border: 1px black solid; background-color: #FFFFFF; font-family: 'Times New Roman', Times, serif; font-size: 14px; font-style: normal; line-height: normal; font-weight: normal; font-variant: normal; text-transform: none; color: #000000; text-decoration: none}
  .meniufonas{background: <?=$MeniuNormaliSpalva?>}
  .meniu{width:134px}
  .dokumentas{width:700px}
  .nuorodanepakeista{text-decoration:none}
-->
</style>

</head>



<body bgcolor="#000000" text="#333300">

<table border="0" cellspacing="0" cellpadding="0" width="750" align="center">
  <tr>

    <td height="84" colspan="2" align="center" valign="bottom" nowrap> 

	 <table border=0  cellspacing="0" cellpadding="0" width="750" height="100%">
        <tr>
          <td background="images/index_C2_R2.jpg"><table width="100%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="165">&nbsp;</td>
                <td width="480"><? include("./PHP/banner.php");?> </td>
                <td>&nbsp;</td>
              </tr>
            </table> </td>

	  </tr>

	  </table>

    </td>

</tr><tr> 

    <td align="left" valign="top" class="meniufonas">
<table border="0" cellspacing="0" cellpadding="0" class="meniufonas meniu">
        <tr> 
          <td class="meniusritis">Meniu</td>
        </tr>
        <tr> 
          <td> 
            <? include("./PHP/coolmenu.php");?>
          </td>
        </tr>
        <tr> 
          <td class="meniusritis">Paieška</td>
        </tr>
        <tr> 
          <td width="125" align="center" valign="top"> <form action="index.php" method="get" name="search">
              <table border="0" cellspacing="0" cellpadding="2" align="center" width="100%">
                <tr> 
                  <td class="meniufx-normalus">Ieškomą frazė:</td>
                </tr>
                <tr valign="middle" align="center"> 
                  <td>
                      <input name="site" type="hidden" value="search">
                      <input type="text" name="action" size="16" tabindex="0" class="ivedimolaukelis">
                    </td>
                </tr>
                <tr> 
                  <td align="right" valign="middle"> <input type="submit" value="Ieškoti" tabindex="1" border="0" class="mygtukas" name="submit"></td>
                </tr>
              </table>
            </td></form>
        </tr>
        <tr> 
          <td class="meniusritis">Nuorodos</td>
        </tr>
        <tr> 
          <td> 
            <? include("PHP/coollinks.php")?>
          </td>
        </tr>
      </table> 
    </td>

    <td align="left" valign="top" bgcolor="#FFFFFF" class="dokumentas"> 
      <table width="100%" border="0" cellspacing="4" cellpadding="4">
        <tr>
          <td>
            <? include("PHP/docparser.php");?>
          </td>
        </tr>
      </table>
      </table>
    </td>
</tr> 
</body>

</html>