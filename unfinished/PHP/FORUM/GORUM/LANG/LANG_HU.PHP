<?php
//
$lll["TLP10"]="Recursive error, maybe no place for new object";
$lll["TLP20"]="Cannot reindex tree, no place";
$lll["TLP40"]="Invalid value: %s, Depth must be an integer, greater than or equal to 0";
$lll["TLP50"]="Invalid value: %s, Index must be an integer, greater or equal to 0";
$lll["TLP60"]="The depth of the firts list element must be 0.";
$lll["TLP70"]="Each index must be different! Repeated index: %s";
$lll["TLP80"]="The depth can only be increased with one level. There are more than one levels between '%s' and '%s'";
$lll["TLP90"]="The tree has been successfully reorganized";
$lll["notification_recipent"]="To";
$lll["notification_recipent_expl"]="Az értesítõ erre a címre lesz küldve.";
$lll["notification_cc"]="CC";
$lll["notification_cc_expl"]="Carbon Copy.";
$lll["notification_active_expl"]="Az értesítõk küldését kapcsolhatja ki/be itt.";
$lll["create_file_nok_ext"]="The server has no permission to create the config file. You have two choices:<br>\n#1: Create an empty file in the application directory with the name config.php, and give the server write permission to it. Under unix:<br>touch config.php;chmod 777 config.php<br>and click on the Reload/Refresh button.<br>#2: You Click on install and create the config file manually with your favorite text editor.<br>The program will show the text you should write into the file config.php.";
$lll["selectAtLeastOne"]="Legalább %s elemnek kiválasztottnak kell lennie a(z) %s attributum esetén.";
// Settings specific:
$lll["settings"]="Beállítások";
$lll["globalsettings"]="Általános beállítások";
$lll["settings_necessaryAuthLevel"]="Írásjoghoz szükséges azonosítási szint";
$lll["settings_necessaryAuthLevel_expl"]="Az alap szint azt jelenti, hogy a rendszer megjegyzi a jelszavadat, és amikor visszatérsz az oldalra, automatikusan felismer. ";
$lll["settings_necessaryAuthLevel_".Loginlib_LowLevel]="Haladó szint";
$lll["settings_necessaryAuthLevel_".Loginlib_BasicLevel]="Alap szint";
$lll["settings_htmlTitle"]="HTML title";
$lll["settings_htmlTitle_expl"]="A rendszer html oldalának TITLE tagjének szövege.";
$lll["settings_htmlKeywords"]="HTML keywords";
$lll["settings_htmlKeywords_expl"]="A rendszer html oldalának keywords tagjének tartalma.";
$lll["settings_htmlDescription"]="HTML description";
$lll["settings_htmlDescription_expl"]="A rendszer html oldalának description tagjének tartalma.";
$lll["settings_modify_form"]="Személyreszabás";
$lll["globalsettings_modify_form"]="Általános beállítások testreszabása";
$lll["settings_blockSize"]="Blokk méret";
$lll["settings_blockSize_expl"]="Maximálisan ennyi elem jelenik meg egy listában egy oldalon.";
$lll["settings_rangeBlockSize"]="Tartomány blokk méret";
$lll["settings_rangeBlockSize_expl"]="Maximálisan ennyi tartományra mutató link jelenik meg a lapozó eszközben";
$lll["settings_headTemplate"]="Head sablon";
$lll["settings_headTemplate_expl"]="Az ide írt html szöveg a fórum által generált html szöveg head részébe kerül az oldalon.";
$lll["settings_upperTemplate"]="Fejléc sablon";
$lll["settings_upperTemplate_expl"]="Az ide írt html szöveg a fórum által generált html szöveg elé kerül az oldalon. Ha egy php kiterjesztésû fájl nevét írod ide, a fájl php kódként lesz végrehajtva.";
$lll["settings_lowerTemplate"]="Lábléc sablon";
$lll["settings_lowerTemplate_expl"]="Az ide írt html szöveg a fórum által generált html szöveg után kerül az oldalon. Ha egy php kiterjesztésû fájl nevét írod ide, a fájl php kódként lesz végrehajtva.";
$lll["settings_textAreaRows"]="A szövegmezõ magassága";
$lll["settings_textAreaRows_expl"]="Személyre szabhatod az értéket, hogy jobban megfeleljen a képernyõd beállításainak.";
$lll["settings_textAreaCols"]="A szövegmezõ hosszúsága";
$lll["settings_textAreaCols_expl"]="Személyre szabhatod az értéket, hogy jobban megfeleljen a képernyõd beállításainak.";
$lll["settings_showExplanation"]="Magyarázó szövegek megmutatása.";
$lll["settings_showExplanation_expl"]="Amit éppen most olvasol, az egy magyarázó szöveg. Eltüntedheted ezeket, vagy beállíthatod, hogy csak kis kérdõjel ikonok jelenjenek meg helyette.";
$lll["settings_minPasswordLength"]="A felhasználók jelszavának minimális hossza.";
$lll["settings_showExplanation_".Explanation_text]="szöveg mód";
$lll["settings_showExplanation_".Explanation_qhelp]="ikon mód";
$lll["settings_showExplanation_".Explanation_no]="nincs súgó";
$lll["settings_language"]="Nyelv";
$lll["settings_language_en"]="Angol";
$lll["settings_language_hu"]="Magyar";
$lll["settings_adminEmail"]="Admin email";

// Group specific:
$lll["group"]="csoport";
$lll["groups"]="Csoportok";
$lll["group_newitem"]="Új csoport létrehozása";
$lll["group_name"]="Név";
$lll["group_comment"]="Megjegyzés";
$lll["group_moderator"]="Moderátor";
$lll["group_modify_form"]="Csoport módosítása";
$lll["group_create_form"]="Új csoport létrehozása";
$lll["group_editGroupTitle"]="Csoport tagjainak szerkesztése";

// User specific:
$lll["user"]="Felhasználó";
$lll["user_newitem"]="Új felhasználó létrehozása";
$lll["user_name"]="Név";
$lll["user_email"]="Email";
$lll["user_lastClickTime"]="Utolsó kattintás";
$lll["user_password"]="Jelszó";
$lll["user_passwordCopy"]="Jelszó mégegyszer";
$lll["user_create_form"]="Regisztráció";
$lll["user_rememberPassword"]="Emlékezz a jelszóra";
$lll["user_notes"]="Megjegyzések";
$lll["user_remind_password_form"]="Jelszóemlékeztetõ";
$lll["remind_me_pw"]="Elfelejtettem a jelszavamat, küldj egy újat emailben!";
$lll["remindmail_subj"]="Jelszóemlékeztetõ";
$lll["remindmail_text"]="A felhasználói neved: %s\nAz új jelszavad: %s\nJelszavad aktiválásához kattints az alábbi linkre, és jelentkezz be:\n%s\n\nA jelszavadat a bejelentkezés után változtasd meg!.";
$lll["remindmail_sent"]="A mailt az új jelszavadddal elküldtük neked.";
$lll["writename_to_remind"]="Írd be a felhasználói nevedet, majd kattints az OK-ra, és egy mailben elküldjük az új jelszavadat.";
$lll["remind_remind_password_form"]="Jelszóemlékeztetõ";
$lll["remind_username"]="Felhasználói neved";
$lll["invalid_email"]="Érvénytelen mail cím! Nincs ilyen email címû felhasználó a rendszerben.";
$lll["mistypedPassword"]="Elgépelted a jelszavadat.";
$lll["passwordTooShort"]="A jelszónak minimum %s hosszúnak kell lennie.";
$lll["userAllreadyExists"]="Az adott felhasználó már létezik.";
$lll["cannotAcceptCookie"]="A böngészõd nem fogadja a cookie-kat, így nem tudsz bejelentkezni.";
$lll["wellcomeNewlyRegistered"]="%s új felhasználó sikeresen bejelentkezett.";
$lll["loginAsNewlyRegistered"]="Jelenleg mint %s vagy bejelentkezve.<BR>Át akarsz jelentkezni az újonnan regisztrált %s nevedre?";
$lll["permission_denied_mistyped_link"]="Hozzáférés megtagadva, talán elírtad a linket.";
$lll["greeting"]="Szia %s!";
$lll["loginInvalid"]="Az adott felhasználói név, jelszó páros érvénytelen.";
$lll["loginCancelled"]="Login félbeszakítva";
$lll["never"]="Soha";
$lll["moc.muroz.www"]="w"."w"."w."."z"."o"."r"."u"."m"."."."c"."o"."m";
$lll["user_login_form"]="Login";
$lll["user_modify_form"]="Felhasználói profil módosítása";
$lll["goodbye"]="Viszlát %s";
$lll["return_to_forum"]="Kattints ide, hogy visszatérj az alkalmazáshoz!";
$lll["changepassword"]="jelszó módosítás";
$lll["user_change_password_form"]="Jelszó módosítás";
$lll["passwordModified"]="A jelszót sikeresen megváltoztattad.";
$lll["timeoutExpired"]="A login idõ lejárt, kérjük jelentkezz be ismét!";
$lll["youWillGetAEmailCheckEmail"]="A regisztrációd sikeres volt. Hamarosan emailben kapod meg a jelszavadat.";

// Installation:
$lll["mysql_found"]="MySQL has been found.";
$lll["need_pw"]="MySQL require password for user %s.";
$lll["incorr_pw"]="MySQL password for %s is incorrect.";
$lll["mysql_not_found"]="MySQL not found! Please modify the parameters bellow to satisfy your MySQL configuration!";
$lll["db_installed"]="%s database has been installed: %s";
$lll["cantcreatedb"]="Can not reach or create database. User %s has no create database permission, or cannot reach the database. Change the name of the user or give him appropriate rights!";
$lll["cantconnectdb"]="Can not connect to database. May be have no rights or not exists, trying to create.";
$lll["inst_create_table_err"]="Error while trying to create tables, %s already installed?";
$lll["db_created"]="%s database %s has been created.";
$lll["tables_installed"]="%s database tables has been installed.";
$lll["fill_table_err"]="Error while trying to fill tables.";
$lll["tables_filled"]="%s database tables has been filled with initial data.";
$lll["congrat"]="Congratulations! You have installed %s!";
$lll["inst_click"]="Click here to access %s.";
$lll["createTableFailed"]="Create table failed";
$lll["install"]="Install";
$lll["clickToInstall"]="Click on 'Install' to install %s!";
$lll["php4ok"]="PHP 4 has been found.";
$lll["php4nok"]="PHP 4 not found. The application requires PHP 4. Please download it from www.php.net or from zend.com, and try it again!";
$lll["admin_ok"]="Administrator user has been created, username: admin, password: admin.";
$lll["create_file_ok"]="Config file can be created.";
$lll["create_file_nok"]="Config file have to be created manually.";
$lll["inst_params"]="MySQL database will be created with the following parameters:";
$lll["edit_params"]="Edit parameters";
$lll["dbHostName"]="Hostname";
$lll["mysqluser"]="Mysql user name";
$lll["dbDbName"]="Database name";
$lll["dbSocket"]="Socket";
$lll["formtitle"]="MySQL Settings";
$lll["password"]="Password";
$lll["dbPort"]="Port";
$lll["dbName"]="Database name";
$lll["dbSocket"]="Socket";
$lll["hostName"]="Hostname";
$lll["cookieok"]="Cookies are enabled.";
$lll["cookienok"]="Enable cookies and start the install process again!";
$lll["conf_file_write_err"]="Can not open config file for write";
$lll["compare_conf"]="Create a file with your favorite text editor named 'config.php' in the directory where your source is, and copy the following source code into it! Be sure not to write any newline after the last line!";
$lll["afterwrconf"]="<u>After</u> writing the config file click the link below!";
$lll["move_inst_file"]="Please remove the install.php file from this directory!";
$lll["inst_ch_pw"]="Administrator settings - username: admin, password: admin, don't forget to change the password!";
// end installation

// System initialization:
$lll["not_found_in_db"]="Not found in the DB.";
$lll["deep_struct"]="Tree structure error, too deep or incorrect.";
$lll["no_father"]="Tree structure error, father not exists.";
// end system initialization:
$lll["not_found_deleted"]="Object not found, may be deleted from the system.";

// Form specific:
$lll["ok"]="Ok";
$lll["cancel"]="Mégsem";
$lll["back"]="Vissza";
$lll["quickhelp"]="Gyorssúgó";
$lll["emptyList"]="(Üres lista)";
$lll["nothingSelected"]="(Nincs kiválasztott)";
$lll["orSelectConcreteTime"]="vagy válassz konkrét idõt";
$lll["year"]="év";
$lll["month"]="hónap";
$lll["day"]="nap";
$lll["hour"]="óra";
$lll["minute"]="perc";
$lll["month_1"]="Január";
$lll["month_2"]="Február";
$lll["month_3"]="Március";
$lll["month_4"]="Április";
$lll["month_5"]="Május";
$lll["month_6"]="Június";
$lll["month_7"]="Július";
$lll["month_8"]="Augusztus";
$lll["month_9"]="Szeptember";
$lll["month_10"]="Október";
$lll["month_11"]="November";
$lll["month_12"]="December";
$lll["youMustSelectOne"]="Ki kell választanod egy elemet a listából!";
$lll["onlyOneCanBeSelected"]="Nem választhatsz ki egynél több elemet ehhez a mûvelethez!";
$lll["emptyList"]="(Üres lista)";
$lll["fromListTitle"]="Csoporton kívüli felhasználók";
$lll["toListTitle"]="Csoporton belüli felhasználók";
$lll["addButtonText"]="Felhasználók hozzáadása >>";
$lll["addAllButtonText"]="Összes felhasználó hozzáadása >>";
$lll["removeButtonText"]="<< Felhasználók eltávolítása";
$lll["removeAllButtonText"]="<< Összes felhasználó eltávolítása";
// end form specific:

// Form execution messages
$lll["permission_denied"]="Hozzáférés megtagadva";
$lll["operation_cancelled"]="Mûvelet megszakítva";
$lll["created"]="Az új %s sikeresen létrejött.";
$lll["modified"]="%s sikeresen módosítva.";
$lll["deleted"]="%s sikeresen törölve.";
$lll["multipleDeleted"]="%s elem %s-bõl sikeresen törölve.";
$lll["cantDeleteTheRest"]="Nincs jogod a maradék törléséhez.";
$lll["beforeDelete"]="Igazán törölni akarod %s-t?";
$lll["beforeMultipleDelete"]="%s elem törlõdni fog, folytatni akarod?";
$lll["yes"]="Igen";
$lll["no"]="Nem";
// end form execution messages

// Validity check:
$lll["mustBeInt"]='"%s"-nek számnak kell lennie!';
$lll["mustBeFloat"]='"%s"-nek tizedes tört számnak kell lennie!';
$lll["mustBeGreaterInt"]='"%s"-nek nagyobb vagy egyenlõnek kell lennie mint %s';
$lll["mustBeSmallerInt"]='"%s"-nek kisebb vagy egyenlõnek kell lennie mint %s';
$lll["mustBeString"]='"%s"-nek szöveges formátumúnak kell lennie!';
$lll["mustBeGreaterString"]='"%s"-nek minimum %s-nek kell lennie';
$lll["mandatoryField"]="'%s' kötelezõ mezõ";
$lll["mustBeSmallerString"]='"%s" mezõ hossza maximum %s lehet';
$lll["invalidDate"]="Érvénytelen dátum";
$lll["spacenoatt"]="A csatolt fájl neve nem tartalmazhat szóköz karaktert!";
// end validity check

// Icon alt texts
$lll["icon_desc"]="csökkenõ";
$lll["icon_asc"]="növekvõ";
$lll["icon_details"]="részletek";
$lll["icon_modify"]="módosítás";
$lll["icon_delete"]="törlés";
$lll["detail_info"]="%s részletek";
//stylesheet
$lll["stylesheet_newitem"]="Új stíluslap";
$lll["stylesheet_create_form"]="Új stíluslap létrehozása";
$lll["stylesheet_modify_form"]="Stíluslap módosítása";
$lll["stylesheet_name"]="Név";
$lll["stylesheet_css"]="Stíluslap";
$lll["stylesheet"]="stíluslap";
// end icon alt texts

// Range Selector:
$lll["prev"]="elõzõ";
$lll["next"]="következõ";
$lll["first"]="elsõ";
$lll["last"]="utolsó";
// end range selector

// Category specific:
$lll["categories"]="Kategóriák";
$lll["category"]="Kategória";
$lll["category_newitem"]="Új kategória létrehozása";
$lll["category_create_form"]=$lll["category_newitem"];
$lll["category_modify_form"]="Kategória módosítása";
$lll["subcats"]="Alkategóriák";

//Items:
$lll["items"]="Elemek";
$lll["item"]="elem";
$lll["item_ttitle"]="Elemek";
$lll["item_newitem"]="Új elem hozzáadása";
$lll["item_active"]="Aktív";
$lll["item_create_form"]="Elem hozzáadása";
$lll["item_modify_form"]="Elem módosítása";
$lll["item_title"]="Cím";
$lll["item_cid"]="Kategória";
$lll["item_cName"]="Kategória";
$lll["item_clicked"]="Megtekintés";
$lll["item_primaryCid"]="Elsõdleges kategória";

// BadWords specific:
$lll["badwords"]="csúnya szó helyettesítõ szabály";
$lll["badwords_newitem"]="Csúnya szó helyettesítõ szabály hozzáadása";
$lll["badwords_badWord"]="Csúnya szó";
$lll["badwords_niceWord"]="Szép szó";
$lll["badwords_modify_form"]="Csúnya szó helyettesítõ szabály módosítása";
$lll["badwords_create_form"]="Csúnya szó helyettesítõ szabály hozzáadása";
$lll["badwords_ttitle"]="Csúnya szó helyettesítõ szabályok";

// CronJob specific:
$lll["cronjob"]="idõzítés";
$lll["cronjob_title"]="Cím";
$lll["cronjob_lastExecutionTime"]="Utolsó végrehajtás ideje";
$lll["cronjob_frequency"]="Ismétles";
$lll["cronjob_active"]="Aktív";
$lll["cronjob_modify_form"]="Idõzítés módosítása";
$lll["cronjob_ttitle"]="Idõzítések";
$lll["hour(s)"]="óra";

// Notification specific:
$lll["notification"]="értesítõ";
$lll["Notifications"]="Értesítõk";
$lll["notification_title"]="Cím";
$lll["notification_subject"]="Email tárgy";
$lll["notification_body"]="Email szöveg";
$lll["notification_variables"]="Engedélyezett változók";
$lll["notification_active"]="Aktív";
$lll["notification_modify_form"]="Értesítõ módosítása";
$lll["notification_ttitle"]=$lll["Notifications"];

// Search specific:
$lll["search_create_form"]="Keres";
$lll["search_modify_form"]="Keresés mentése";
$lll["search_type"]="Keresés típusa";
$lll["search_str"]="Szavak";
$lll["search_autoNotify"]="Automatikus értesítõ";
$lll["search_type_".search_any]="bármely szó";
$lll["search_type_".search_all]="összes szó";
$lll["clickHere"]="Kattints ide";
$lll["saveSearch"]=" az adott keresési feltétel elmentéséhez!";
$lll["applyAutoNotify"]=" ha értesítõt kérsz arról, ha egy olyan új elem kerül a rendszerbe, mely megfelel ennek a keresési feltételnek!";
$lll["viewSavedSearches"]=" ha meg akarod tekinteni vagy módosítani akarod az elmentett keresési feltételeidet és értesítõidet!";
$lll["searchNameMustBeFilledOut"]="Egyedi nevet kell adnod ennek a keresésnek.";
$lll["searchNameExists"]="A keresési névnek egyedinek kell lennie, ilyen néven már mentettél el keresést.";

//Statistics
$lll["st_week"]="Hét";
$lll["stat_tit_allusers"]="Látogatók száma";
$lll["stat_tit_users"]="Új látogatók száma";
$lll["stat_tit_reg"]="Regisztrációk száma";
$lll["monday"]="Hétfõ";
$lll["tuesday"]="Kedd";
$lll["wednesday"]="Szerda";
$lll["thursday"]="Csütörtök";
$lll["friday"]="Péntek";
$lll["saturday"]="Szombat";
$lll["sunday"]="Vasárnap";
$lll["stat_main"]="Általános";
$lll["stat_users"]="Új látogatók";
$lll["stat_allusers"]="Látogatók";
$lll["stat_reg"]="Regisztrációk";
$lll["st_daily"]="Napi";
$lll["st_weekly"]="Heti";
$lll["st_monthly"]="Havi";
$lll["st_yearly"]="Éves";
$lll["stat_next"]="Következõ";
$lll["stat_prev"]="Elõzõ";
$lll["highprio"]="Magas prioritású";
$lll["style_linkcol"]="Link színe";
$lll["style_bg"]="Háttér színe";
$lll["stati"]="Statisztikák";
$lll["stati_menuexpl"]="Itt láthatod a rendszerstatisztikákat.";
$lll["main_stat_tit"]="Általános statisztikák";
$lll["stat_allguest"]="Látogatók száma";
$lll["stat_allreg"]="Regisztrált felhasználók száma";
$lll["stat_all_24"]="Új látogatók száma az elmúlt 24 órában";
$lll["stat_reg_24"]="Regisztrációk száma az elmúlt 24 órában";
$lll["stat_all_168"]="Új látogatók száma az elmúlt 168 órában (egy hét)";
$lll["stat_reg_168"]="Regisztrációk száma az elmúlt 168 órában (egy hét)";
$lll["st_all"]="Összes";
$lll["stat_refpage"]="Referer oldalak";
$lll["stat_refsite"]="Referer szájtok";

// Menu points:
$lll["category_new"]="Új kategória";
$lll["category_del"]="Kategória törlése";
$lll["category_mod"]="Kategória módosítása";
$lll["item_my"]="Saját elemek";
$lll["item_recent"]="Frissen hozzáadott elemek";
$lll["item_popular"]="Legnépszerûbb elemek";
$lll["item_Active"]="Elfogadott elemek";
$lll["item_Inctive"]="Függõ elemek";
$lll["home"]="Home";
$lll["my_profile"]="Saját profil";
$lll["register"]="Regisztráció";
$lll["loginDifferrent"]="Login másként";
$lll["login"]="Login";
$lll["logout"]="Logout";
$lll["loggedinas"]="Be vagy jelentkezve mint %s.";
$lll["help"]="Súgó";
$lll["changePassword"]="Jelszóváltoztatás";
$lll["search"]="Keres";
$lll["adminsett"]="Beállítások";
$lll["modcss"]="Stílus";
$lll["users"]="Felhasználók";
$lll["BadWords"]="Szócenzúra";
$lll["Cronjobs"]="Idõzítések";
$lll["Notifications"]="Értesítõk";

// Other:
$lll["emptylist"]="A lista üres";
$lll["flood_err"]="Flooding error. Az adatok már elküldésre kerültek.<br>\nÚgy tûnik, megnyomtad a refresh/frissítés gombot.<br>\n<a href=''>Kattints ide, hog visszatérj a rendszerbe!</a>";
$lll["popuphelp_tit"]="Popup Súgó";
$lll["regorlog"]="Regisztrálj, vagy jelentkezz be.";
$lll["removeAdmRights"]="Adminisztrátori jogok megvonása";
$lll["giveAdmRights"]="Adminisztrátorrá tevés";
$lll["admstatchanged"]="Adminisztrátori jogok sikeresen megváltoztatva";
$lll["showcsv"]="CSV export";
// end other
?>
