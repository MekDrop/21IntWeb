<?php
    $oLink="index.php?site=files&menu=".$HTTP_GET_VARS["menu"]."&";
	setcookie("password",$password,time()+94555556);
	require('filesconfig.php');

/*function filesHeader()
{
   // PUT YOUR HEADER STUFF IN HERE!

	return;
}
function filesFooter()
{
   // PUT YOUR FOOTER STUFF IN HERE!

	return;
}*/
$id = $_GET['id'];
$func = $_GET['func'];
$cat = $_GET['cat'];
/* ================================================================
   Download file with id
   ================================================================ */
		if ($func == "download")
		{
			if ($id)
			{
				mysql_connect($db_host, $db_user, $db_pass) 
					or die ("Negalima susiekti su doumenø baze.");
				mysql_select_db($db_database) 
					or die ("Negalima pasirinkti duomenø bazës.");
				$sql = "SELECT * FROM files WHERE id = $id";
				$result = mysql_query($sql);
				$row = mysql_fetch_array($result);
				$link = $row["url"];
				$newsql = "UPDATE files SET downloads=downloads+1 WHERE id = '$id'";
				mysql_query($newsql)
					or die("Negalima atnaujinti failø atsiuntimo skaitliuko!");
				echo "<html><head><meta http-equiv=\"Refresh\" content=\"5; URL=" . $link . "\"></head><body><CENTER><B>Atsiuntimas automatiðkai prasidës po 5 sekundþiø.<BR><BR><a href=\"" . $link . "\">Paspausk èia, jeigu failo siuntimas taip ir neprasidëjo</a></B></CENTER></body></html>";
				exit;
			} else {
				echo "Blogas id";
			}
		}

//	filesHeader();
	
/* ================================================================
   If there is no function, print the catagories that are available
   ================================================================ */
	if (!$func)
	{
		mysql_connect($db_host, $db_user, $db_pass) 
			or die ("Negalima susiekti su doumenø baze.");
		mysql_select_db($db_database) 
			or die ("Negalima pasirinkti duomenø bazës.");
		$sql = "SELECT * FROM files_catagory ORDER BY id";
		$result = mysql_query($sql);
		if ($row = mysql_fetch_array($result))
		{	
		?>
		<?php
			do
			{
					?>
					<font size="2"><B>» <a href="<?PHP echo $oLink ?>func=selectcat&cat=<?php echo $row[id]; ?>"><?php echo $row[name]; ?></font></B></a><BR><font size="1"><?php echo $row[description]; ?></font><BR><BR>
					<?php


			} while ($row = mysql_fetch_array($result));
		} else {
			echo "Nëra kategorijø<BR><BR>";
		}
		echo "<font size=1><b>Admininstratoriaus Funkcijos</b>: <a href=\"".$oLink."func=addcatagory\">Pridëti kategorijà</a></font>";
	}
/* ========================================================================
   If the function is select cat, select the catagory with the matching id.
   Run through some of the info for that catagory, including directories
   and files.
   ======================================================================== */
	if ($func == "selectcat")
	{
		mysql_connect($db_host, $db_user, $db_pass) 
			or die ("Negalima susiekti su doumenø baze.");
		mysql_select_db($db_database) 
			or die ("Negalima pasirinkti duomenø bazës.");
		$sql2 = "SELECT * FROM files_catagory WHERE id = $cat";
		$result2 = mysql_query($sql2);
		$currentcat = mysql_fetch_array($result2);
			echo "<font size=2 color=#FFFF00><B>$currentcat[name]</B></font><BR>";
			echo "<font size=1>$currentcat[description]</font><BR><BR>";

		$sql = "SELECT * FROM files_folders WHERE catid = $cat";
		$result = mysql_query($sql);
		if ($row = mysql_fetch_array($result))
		{	
			echo "<font size=3><b>Katalogai:</b></font><BR>";
			echo "<BLOCKQUOTE>";
			do
			{

					?>
					<font size="2" color="#FFFF00"><b>» <a href="<?PHP echo $oLink ?>func=selectfolder&id=<?php echo $row[id];?>"><?php echo $row[name]; ?></a></b></font><br><font size="1"><?php echo $row[description]; ?></font><BR>
					<?php


			} while ($row = mysql_fetch_array($result));
			echo "</BLOCKQUOTE>";
		} 
		$sql = "SELECT * FROM files WHERE catid = $cat";
		$result = mysql_query($sql);
		if ($row2 = mysql_fetch_array($result))
		{	
			echo "<font size=3><b>Failai:</b></font><BR>";
			echo "<BLOCKQUOTE>";
			do
			{

					?>
					<font size="2" color="#FFFF00">
						<b>» <a href="<?PHP echo $oLink ?>func=fileinfo&id=<?php echo $row2[id];?>"><?php echo $row2[filename]; ?></a></b></font>
						<B>Dydis:</B> <?php echo $row2["filesize"]; ?></font><br><font size="1"><?php echo $row2[description]; ?></font><BR>
					<?php


			} while ($row = mysql_fetch_array($result));
			echo "</BLOCKQUOTE>";
		} 
		echo "<BR><BR><font size=1><b>Admininistratoriaus Pasirinkimai:</b> <a href=".$oLink."func=deletecat&id=$cat>Iðtrinti kategorijà</a>, <a href=".$oLink."func=addfolder&parent=catagory&id=$cat>Pridëti katalogà</a>, <a href=".$oLink."func=addfile&parent=catagory&id=$cat>Pridëti failà</a>.</font><BR>";

	}
/* ========================================================================
   File info
   ======================================================================== */
	if ($func == "fileinfo")
	{
		if (!$id) {
			echo "INVALID ID";
		} else {
			mysql_connect($db_host, $db_user, $db_pass) 
				or die ("Negalima susiekti su doumenø baze.");
			mysql_select_db($db_database) 
				or die ("Negalima pasirinkti duomenø bazës.");
			$sql = "SELECT * FROM files WHERE id = $id";
			$result = mysql_query($sql);
			
			if ($row = mysql_fetch_array($result))
			{
			?>
				<font size="2">
				<b>Failo Vardas:</b> <?php echo $row["filename"]; ?> <a href="<?PHP echo $oLink ?>func=download&id=<?php echo $row[id];?>">Atsisiøsk failà!</a><BR>
				<b>Failo Dydis:</b> <?php echo $row["filesize"]; ?><BR>
				<b>Failo Tipas:</b> <?php echo $row["filetype"]; ?><BR>
				<b>Atsiuntimø Skaièius:</b> <?php echo $row["downloads"]; ?><BR>
				<b>Apibûdinimas:</b><BR><?php echo $row["description"]; ?><BR><BR>
				
				<form>
					<input type="button" value="Atgal" onclick="history.back()">
				</form>
			<?
			} else {
			echo "INVALID ID";
				}
		}
		echo "<BR><BR><font size=1><b>Administratoriaus pasirinkimai:</b> <a href=".$oLink."func=deletefile&id=$id>Iðtrinti failà</a>.</font><BR>";
	}

/* ========================================================================
   If function is select folder, run through some stuff.
    -Look for sub folders
	-Look for files
   ======================================================================== */
	if ($func == "selectfolder")
	{
		if (!$id)
		{
			echo "Blogas ID";
		} else {
		mysql_connect($db_host, $db_user, $db_pass) 
			or die ("Negalima susiekti su doumenø baze.");
		mysql_select_db($db_database) 
			or die ("Negalima pasirinkti duomenø bazës.");
		$sql = "SELECT * FROM files_folders WHERE id = $id";
		$result = mysql_query($sql);
		if ($row = mysql_fetch_array($result))
			{
				echo "<font size=2 color=#FFFF00><B>$row[name]</B></font><BR>";
				echo "<font size=1>$row[description]</font><BR><BR>";

				$sql = "SELECT * FROM files_folders WHERE parentid = $id";
				$result = mysql_query($sql);
				if ($row = mysql_fetch_array($result))
				{
					echo "<font size=3><b>Katalogai:</b></font><BR>";
					echo "<BLOCKQUOTE>";
					do
					{

							?>
							<font size="2" color="#FFFF00"><b>» <a href="<?PHP echo $oLink ?>func=selectfolder&id=<?php echo $row[id];?>"><?php echo $row[name]; ?></a></b></font><br><font size="1"><?php echo $row[description]; ?></font><BR>
							<?php


					} while ($row = mysql_fetch_array($result));
					echo "</BLOCKQUOTE>";
				}
				$sql = "SELECT * FROM files WHERE folderid = $id";
				$result = mysql_query($sql);
				if ($row2 = mysql_fetch_array($result))
				{
					echo "<font size=3><b>Failai:</b></font><BR>";
					echo "<BLOCKQUOTE>";
					do
					{

							?>
							<b>» <a href="<?PHP echo $oLink ?>func=fileinfo&id=<?php echo $row2[id];?>"><?php echo $row2[filename]; ?></a></b></font>
						<B>Dydis:</B> <?php echo $row2["filesize"]; ?></font><br><font size="1"><?php echo $row2[description]; ?></font><BR>
							<?php


					} while ($row2 = mysql_fetch_array($result));
					echo "</BLOCKQUOTE>";
				}
				echo "<BR><BR><font size=1><b>Admininstratoriaus pasirinkimai:</b> <a href=".$oLink."func=deletefolder&id=$id>Iðtrinti katalogà</a>, <a href=".$oLink."func=addfolder&parent=folder&id=$id>Pridëti katalogà</a>, <a href=".$oLink."func=addfile&parent=folder&id=$id>Pridëti failà</a>.</font><BR>";
			} else {
				echo "Blogas ID!";
			}
		}

	}



/* =================================================================================
   I decided to add the admin functions down here. Should be easier to custimize
   when all the main stuff is above.
   =================================================================================
   First up we have add catagory.
   ================================================================================= */

	if ($func == "addcatagory")
	{
		if ($_POST['submit'])
		{
			if ($_POST['password'] == $adminpass AND $_POST['submit']){
				$catname = $_POST['catname'];
				$description = $_POST['description'];
				mysql_connect($db_host, $db_user, $db_pass) 
					or die ("Negalima susiekti su doumenø baze.");
				mysql_select_db($db_database) 
					or die ("Negalima pasirinkti duomenø bazës.");
				MYSQL_QUERY("INSERT INTO files_catagory (name,description) VALUES ('$catname','$description')")
					or die ("Can't Update.");
				?>
		        <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
					 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
					 <INPUT TYPE="hidden" NAME="name" value="Nauja kategorija">
					 <INPUT TYPE="hidden" NAME="text" value="Pridëta nauja kategorija <B><?=$catname;?></B> atsisiuntimø skyrelyje. Trumpas ðios kategorijos apibûdinimas: <?=$description;?>">
		 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
					 <INPUT TYPE="submit" value="Pagrindinis puslapis">
				 </FORM>
				<?
//				echo "Viskas padaryta! <a href=\"$oLink\">Pagrindinis puslapis</a>";
				
			} else {
				echo "Blogas slaptaþodis.";
			}
		} else {
		?>
				<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=addcatagory">
				<B>Administratoriaus Slaptaþodis:</B> <INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
				<B>Kategorijos Vardas:</B> <INPUT TYPE="text" NAME="catname"><BR>
				<B>Apibûdinimas:</B> <BR><TEXTAREA NAME="description" ROWS="4" COLS="35"></TEXTAREA><BR>
				Patikrink ar visi laukai gerai uþpildyti.<BR>
				<INPUT TYPE="submit" NAME="submit" VALUE="Siøsti"><INPUT TYPE="Iðvalyti">
				</FORM>
		<?php
		}
	}
/* ================================================================================
   Delete Catagory
   ================================================================================ */

	if ($func == "deletecat")
	{
		if ($_POST['submit'])
		{
			if ($_POST['password'] == $adminpass AND $_POST['submit']){
				$id = $_POST['id'];
				mysql_connect($db_host, $db_user, $db_pass) 
					or die ("Negalima susiekti su duomenø baze.");
				mysql_select_db($db_database) 
					or die ("Negalima pasirinkti duomenø bazës.");
				$sql = "DELETE FROM files_catagory WHERE id = '$id'";
				mysql_query($sql)
					or die ("Negalima atnaujinti.");
				$sql = "DELETE FROM files WHERE catid = '$id'";
				mysql_query($sql)
					or die ("Negalima iðtrinti failø");
				?><!--
		        <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
					 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
					 <INPUT TYPE="hidden" NAME="name" value="Iðtrinta kategorija">
					 <INPUT TYPE="hidden" NAME="text" value="Iðtrinta kategorija, kurios id yra  <?=$id;?> atsisiuntimø skyrelyje.">
		 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
					 <INPUT TYPE="submit" value="Pagrindinis puslapis">
				 </FORM>-->
				<?
				echo "Viskas padaryta! <a href=\"$oLink\">Pagrindinis puslapis</a>";
			} else {
				echo "Blogas slaptaþodis.";
			}
		} else {
		?>
				<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=deletecat&id=<?php echo $id; ?>">
				<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
				<INPUT TYPE="submit" NAME="submit" VALUE="Trinti kategorijà">
				</FORM>
		<?php
		}
	}
/* ================================================================================
   Add Folder
   ================================================================================ */
	if ($func == "addfolder")
	{
		if ($_GET['parent'] == "catagory")
		{
			if ($_POST['submit'])
			{
				if ($_POST['password'] == $adminpass AND $_POST['submit']){
					$name = $_POST['name'];
					$description = $_POST['description'];
					mysql_connect($db_host, $db_user, $db_pass) 
						or die ("Negalima susiekti su doumenø baze.");
					mysql_select_db($db_database) 
						or die ("Negalima pasirinkti duomenø bazës.");
					$sql = "INSERT INTO files_folders (catid,name,description) VALUES ('$id','$name','$description')";
					mysql_query($sql)
						or die ("Negalima atnaujinti.");
					?>
  			         <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
						 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
						 <INPUT TYPE="hidden" NAME="name" value="Naujas katalogas">
						 <INPUT TYPE="hidden" NAME="text" value="Pridëtas naujas katalogas <B><?=$name;?></B> atsisiuntimø skyrelyje. Trumpas ðio katalogo apibûdinimas: <?=$description;?>">
			 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
						 <INPUT TYPE="submit" value="Pagrindinis puslapis">
					 </FORM>
					<?
//					echo "Viskas padaryta! <a href=\"$oLink\">Pagrindinis puslapis</a>";
				} else {
					echo "Blogas slaptaþodis.";
				}
			} else {
			?>
					<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=addfolder&parent=catagory&id=<?php echo $id; ?>">
					<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
					<B>Pavadinimas: </B><INPUT TYPE="text" name="name"><BR>
					<B>Apibûdinimas: </b><BR><TEXTAREA NAME="description" ROWS="4" COLS="35"></TEXTAREA><BR>
					<INPUT TYPE="submit" NAME="submit" VALUE="Pridëti katalogà">
					</FORM>
			<?php
			}
		}
		if ($_GET['parent'] == "folder")
		{
		if ($_POST['submit'])
			{
				if ($_POST['password'] == $adminpass AND $_POST['submit']){
					$name = $_POST['name'];
					$description = $_POST['description'];
					mysql_connect($db_host, $db_user, $db_pass) 
						or die ("Negalima susiekti su doumenø baze.");
					mysql_select_db($db_database) 
						or die ("Negalima pasirinkti duomenø bazës.");
					$sql = "INSERT INTO files_folders (parentid,name,description) VALUES ('$id','$name','$description')";
					mysql_query($sql)
						or die ("Negalima atnaujinti.");
					?>
  			         <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
						 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
						 <INPUT TYPE="hidden" NAME="name" value="Naujas katalogas">
						 <INPUT TYPE="hidden" NAME="text" value="Pridëtas naujas katalogas <B><?=$name;?></B> atsisiuntimø skyrelyje. Trumpas ðio katalogo apibûdinimas: <?=$description;?>">
			 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
						 <INPUT TYPE="submit" value="Pagrindinis puslapis">
					 </FORM>
					<?
//					echo "Viskas padaryta! <a href=\"$oLink\">Pagrindinis puslapis</a>";
				} else {
					echo "Blogas slaptaþodis.";
				}
			} else {
			?>
					<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=addfolder&parent=folder&id=<?php echo $id; ?>">
					<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
					<B>Pavadinimas: </B><INPUT TYPE="text" name="name"><BR>
					<B>Apibûdinimas: </b><BR><TEXTAREA NAME="description" ROWS="4" COLS="35"></TEXTAREA><BR>
					<INPUT TYPE="submit" NAME="submit" VALUE="Pridëti katalogà">
					</FORM>
			<?php
			}
		}
	}
/* ================================================================================
   Add File
   ================================================================================ */
   if ($func == "addfile")
   {
		if ($_GET['parent'] == "catagory")
		{
			if ($_POST['submit'])
			{
				if ($_POST['password'] == $adminpass AND $_POST['submit']) {
					$url = $_POST['url'];
					$description = $_POST['description'];
					$name = $_POST['name'];
					$size = $_POST['size'];
					$filetype = $_POST['filetype'];
					mysql_connect($db_host, $db_user, $db_pass) 
						or die ("Negalima susiekti su doumenø baze.");
					mysql_select_db($db_database) 
						or die ("Negalima pasirinkti duomenø bazës.");
					MYSQL_QUERY("INSERT INTO files (catid,url,description,filename,filesize,filetype) VALUES ('$id','$url','$description','$name','$size','$filetype')")
						or die ("Can't Add File");
					?>
  			         <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
						 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
						 <INPUT TYPE="hidden" NAME="name" value="Pridëtas failas á atsiuntimø skyrelá">
						 <INPUT TYPE="hidden" NAME="text" value="Pridëtas naujas failas <B><?=$name;?></B> atsisiuntimø skyrelyje. Trumpas ðio failo apibûdinimas: <?=$description;?>">
			 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
						 <INPUT TYPE="submit" value="Pagrindinis puslapis">
					 </FORM>
					<?
//					echo "Failas pridëtas!<BR>";
				} else {
					echo "Blogas slaptaþodis.";
				}
			} else {
			?>
				<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=addfile&parent=catagory&id=<?php echo $id; ?>">
				<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
				<B>Apibûdinimas: </b><BR><TEXTAREA NAME="description" ROWS="4" COLS="35"></TEXTAREA><BR>
				<B>URL:</B> <input type="text" name="url" size="50"><BR>
				<B>Failo Vardas:</B> <input type="text" name="name"><BR>
				<B>Failo Dydis:</B> <input type="text" name="size" size="50"><BR>
				<B>Failo Tipas:</b> <input type="text" name="filetype" size="50"><BR>
				<INPUT TYPE="submit" NAME="submit" VALUE="Pridëti failà"><BR>
				Patikrink ar visi laukai teisingai uþpildyti.<BR>
					</FORM>
			<?php
			}
	   } elseif ($_GET['parent'] == "folder") {
			if ($_POST['submit'])
			{
				if ($_POST['password'] == $adminpass AND $_POST['submit'])
				{
					$url = $_POST['url'];
					$description = $_POST['description'];
					$name = $_POST['name'];
					$size = $_POST['size'];
					$filetype = $_POST['filetype'];
					mysql_connect($db_host, $db_user, $db_pass) 
						or die ("Negalima susiekti su doumenø baze.");
					mysql_select_db($db_database) 
						or die ("Negalima pasirinkti duomenø bazës.");
					MYSQL_QUERY("INSERT INTO files (folderid,url,description,filename,filesize,filetype) VALUES ('$id','$url','$description','$name','$size','$filetype')")
						or die ("Can't Add File");
					?>
  			         <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
						 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
						 <INPUT TYPE="hidden" NAME="name" value="Pridëtas failas á atsiuntimø skyrelá">
						 <INPUT TYPE="hidden" NAME="text" value="Pridëtas naujas failas <B><?=$name;?></B> atsisiuntimø skyrelyje. Trumpas ðio failo apibûdinimas: <?=$description;?>">
			 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
						 <INPUT TYPE="submit" value="Pagrindinis puslapis">
					 </FORM>
					<?
//					echo "Failas pridëtas!<BR>";
				} else {
					echo "Blogas slaptaþodis.";
				}
			} else {
			?>
				<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=addfile&parent=folder&id=<?php echo $id; ?>">
				<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
				<B>Apibûdinimas: </b><BR><TEXTAREA NAME="description" ROWS="4" COLS="35"></TEXTAREA><BR>
				<B>URL:</B> <input type="text" name="url" size="50"><BR>
				<B>Failo Vardas:</B> <input type="text" name="name"><BR>
				<B>Failo Dydis:</B> <input type="text" name="size" size="50"><BR>
				<B>Failo Tipas:</b> <input type="text" name="filetype" size="50"><BR>
				<INPUT TYPE="submit" NAME="submit" VALUE="add file"><BR>
				Patikrink, ar gerai uþpildei visus laukus.<BR>
					</FORM>
			<?php
			}
	   }

   }
/* ================================================================================
   Delete Catagory
   ================================================================================ */

	if ($func == "deletefolder")
	{
		if ($_POST['submit'])
		{
			if ($_POST['password'] == $adminpass AND $_POST['submit']){
				mysql_connect($db_host, $db_user, $db_pass) 
					or die ("Negalima susiekti su duomenø baze.");
				mysql_select_db($db_database) 
					or die ("Negalima pasirinkti duomenø bazës.");
				$sql = "DELETE FROM files_folders WHERE id = '$id'";
				mysql_query($sql)
					or die ("Negalima atnaujinti.");
				$sql = "DELETE FROM files WHERE folderid = '$id'";
				?><!--
  			     <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
					 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
					 <INPUT TYPE="hidden" NAME="name" value="Paðalintas katalogas">
					 <INPUT TYPE="hidden" NAME="text" value="Paðalintas katalogas, kurio id yra <?=$id;?> atsisiuntimø skyrelyje.">
		 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
					 <INPUT TYPE="submit" value="Pagrindinis puslapis">
				 </FORM>-->
				<?
				echo "Viskas padaryta! <a href=\"$oLink\">Pagrindinis puslapis</a>";
			} else {
				echo "Blogas slaptaþodis.";
			}
		} else {
		?>
				<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=deletefolder&id=<?php echo $id; ?>">
				<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
				<INPUT TYPE="submit" NAME="submit" VALUE="Iðtrinti ðità katalogà">
				</FORM>
		<?php
		}
	}
/* ================================================================================
   Delete File
   ================================================================================ */

	if ($func == "deletefile")
	{
		if ($_POST['submit'])
		{
			if ($_POST['password'] == $adminpass AND $_POST['submit']){
				mysql_connect($db_host, $db_user, $db_pass) 
					or die ("Negalima susiekti su doumenø baze.");
				mysql_select_db($db_database) 
					or die ("Negalima pasirinkti duomenø bazës.");
				$sql = "DELETE FROM files WHERE id = '$id'";
				mysql_query($sql)
					or die ("Negalima atnaujinti.");
				?>
  			     <FORM METHOD=post ACTION="index.php?site=news&action=additem&menu=<?=$HTTP_GET_VARS["menu"];?>">
					 <INPUT TYPE="hidden" NAME="nick" value="Administratorius">
					 <INPUT TYPE="hidden" NAME="name" value="Paðalintas failas">
					 <INPUT TYPE="hidden" NAME="text" value="Paðalintas failas, kurio id yra <?=$id;?> atsisiuntimø skyrelyje.">
		 			 <INPUT TYPE="hidden" NAME="refresh" value="<?=$oLink;?>">
					 <INPUT TYPE="submit" value="Pagrindinis puslapis">
				 </FORM>
				<?
//				echo "Viskas padaryta! <a href=\"$oLink\">Pagrindinis puslapis</a>";
			} else {
				echo "Blogas slaptaþodis.";
			}
		} else {
		?>
				<FORM METHOD=POST ACTION="<?PHP echo $oLink ?>func=deletefile&id=<?php echo $id; ?>">
				<B>Administratoriaus Slaptaþodis: </B><INPUT TYPE="text" NAME="password" VALUE="<?php echo $password;?>"><BR>
				<INPUT TYPE="submit" NAME="submit" VALUE="Iðtrinti ðità failà">
				</FORM>
		<?php
		}
	}
		
	echo "<p align=right>(<a href=mailto:$adminemail>Praneðti problemà administratoriui</a>)</p>";
//	filesFooter();
?>