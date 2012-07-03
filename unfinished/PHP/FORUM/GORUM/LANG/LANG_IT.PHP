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
$lll["notification_recipent_expl"]="The notification will be sent to this address.";
$lll["notification_cc"]="CC";
$lll["notification_cc_expl"]="Specify here the email adress the notification should be sent as a carbon copy.";
$lll["notification_active_expl"]="You can switch on and off the sending of this notification here.";
$lll["create_file_nok_ext"]="The server has no permission to create the config file. You have two choices:<br>\n#1: Create an empty file in the application directory with the name config.php, and give the server write permission to it. Under unix:<br>touch config.php;chmod 777 config.php<br>and click on the Reload/Refresh button.<br>#2: You Click on install and create the config file manually with your favorite text editor.<br>The program will show the text you should write into the file config.php.";
$lll["selectAtLeastOne"]="At least %s item must be selected from the list at %s.";
// Settings specific:
$lll["settings"]="Impostazioni";
$lll["globalsettings"]="Impostazioni generali";
$lll["settings_necessaryAuthLevel"]="Livello di autenticazione necessario per permesso in scrittura";
$lll["settings_necessaryAuthLevel_expl"]="Livello base significa che il programma memorizza la tua password ed effettua l'autenticazione in modo automatico. Il livello avanzato, invece, richede l'effettuazione del Login.";
$lll["settings_necessaryAuthLevel_".Loginlib_LowLevel]="Livello avanzato";
$lll["settings_necessaryAuthLevel_".Loginlib_BasicLevel]="Livello base";
$lll["settings_htmlTitle"]="Titolo HTML";
$lll["settings_htmlTitle_expl"]="Il contenuto del tag TITLE nell'intestazione HTML delle pagine del forum";
$lll["settings_htmlKeywords"]="Parole chiave HTML";
$lll["settings_htmlKeywords_expl"]="Il META tag 'Parole Chiave' nell'intestazione HTML delle pagine del forum. E' usato principalmente dai motori di ricerca per indicizzare le pagine.";
$lll["settings_htmlDescription"]="Descrizione HTML";
$lll["settings_htmlDescription_expl"]="Il META tag 'Descrizione' nell'intestazione HTML delle pagine del forum. E' usato principalmente dai motori di ricerca per indicizzare le pagine.";
$lll["settings_modify_form"]="Personalizza";
$lll["globalsettings_modify_form"]="Adatta Impostazioni Generali";
$lll["settings_blockSize"]="Misura del blocco";
$lll["settings_blockSize_expl"]="Il numero massimo di elementi della lista che sono visualizzati in una pagina. ";
$lll["settings_rangeBlockSize"]="Range della misura del blocco";
$lll["settings_rangeBlockSize_expl"]="Il numero massimo di blocchi visualizzati dal tool. ";
$lll["settings_headTemplate"]="Maschera dell'intestazione";
$lll["settings_headTemplate_expl"]="Il testo che inserisci qui apparirà nella sezione 'intestazione' della pagina HTML generata.";
$lll["settings_upperTemplate"]="Maschera superiore";
$lll["settings_upperTemplate_expl"]="Qui puoi definire una sezione HTML che apparirà sopra il contenuto della pagina 'Zorum', o assegna un file php che sarà eseguito. Questa funzione può essere utilizzata per integrare meglio 'Zorum' in un sito già esistente. ";
$lll["settings_lowerTemplate"]="Maschera inferiore";
$lll["settings_lowerTemplate_expl"]="Qui puoi definire una sezione HTML che apparirà al di sotto del contenuto della pagina 'Zorum', o assegna un file php che sarà eseguito. Questa funzione può essere utilizzata per integrare meglio 'Zorum' in un sito già esistente. ";
$lll["settings_textAreaRows"]="Altezza del campo riservato al testo del messaggio";
$lll["settings_textAreaRows_expl"]="Puoi definire la misura del box per l'inserimento del testo del messaggio che meglio si adatta al tuo video ed alla misura della finestra del tuo browser. ";
$lll["settings_textAreaCols"]="Lunghezza del campo riservato al testo del messaggio";
$lll["settings_textAreaCols_expl"]="Puoi definire la lunghezza del box per l'inserimento del testo del messaggio che meglio si adatta al tuo video ed alla misura della finestra del tuo browser. ";
$lll["settings_showExplanation"]="Mostra le spiegazioni";
$lll["settings_showExplanation_expl"]="Ciò che state leggendo in questo momento sono delle spiegazioni. Potete fare in modo di nasconderle o mostrarle come testo o in forma di icona  con un piccolo aiuto";
$lll["settings_minPasswordLength"]="Lunghezza minima della password";
$lll["settings_showExplanation_".Explanation_text]="modo 'testo'";
$lll["settings_showExplanation_".Explanation_qhelp]="modo 'aiuto rapido'";
$lll["settings_showExplanation_".Explanation_no]="modo 'niente aiuto'";
$lll["settings_language"]="Lingua";
$lll["settings_language_en"]="Inglese";
$lll["settings_language_hu"]="Ungherese";
$lll["settings_adminEmail"]="Email dell'Amministratore";

// Group specific:
$lll["group"]="gruppo";
$lll["groups"]="Gruppi";
$lll["group_newitem"]="Aggiungi nuovo gruppo";
$lll["group_name"]="Nome";
$lll["group_comment"]="Commento";
$lll["group_moderator"]="Moderatore";
$lll["group_modify_form"]="Modifica gruppo";
$lll["group_create_form"]="Crea grupop";
$lll["group_editGroupTitle"]="Edita gruppo membri";

// User specific:
$lll["user"]="utente";
$lll["user_newitem"]="Aggiungi nuovo utente";
$lll["user_name"]="Nome";
$lll["user_email"]="Email";
$lll["user_lastClickTime"]="Ultimo click";
$lll["user_password"]="Password";
$lll["user_passwordCopy"]="Ripeti password";
$lll["user_create_form"]="Registra";
$lll["user_rememberPassword"]="Ricorda password";
$lll["user_notes"]="Note";
$lll["user_remind_password_form"]="Ricorda password";
$lll["remind_me_pw"]="Ho dimenticato la mia password, inviamene un'altra via email!";
$lll["remindmail_subj"]="Ricorda Password";
$lll["remindmail_text"]="Il tuo username è: %s\nLa tua nuova password è: %s\nClicca dul seguente link per attivare la password, quindi prova ad effettuare il Login:\n%s\n\nTi raccomandiamo di cambiare la password successivamente al primo Login.";
$lll["remindmail_sent"]="Un messaggio è stato inviato al tuo indirizzo con la comunicazione della nuova password";
$lll["writename_to_remind"]="Inseirsci il tuo username, clicca su 'ok', e ti sarà inviato un messaggio con la nuova password.";
$lll["remind_remind_password_form"]="Ricorda Password";
$lll["remind_username"]="Il tuo username";
$lll["invalid_email"]="Indirizzo email non valido! Non ci sono utenti nel sistema con questa email.";
$lll["mistypedPassword"]="Hai digitato erroneamente la tua password.";
$lll["passwordTooShort"]="La misura della password deve essere di minimo %s caratteri.";
$lll["userAllreadyExists"]="L'utente scelto già esiste.";
$lll["cannotAcceptCookie"]="Il tuo browser non accetta i cookies, non puoi effettuare la registrazione.";
$lll["wellcomeNewlyRegistered"]="L'utente %s è stato registrato con successo.";
$lll["loginAsNewlyRegistered"]="Attualmente sei autenticato come utente %s.<BR>Vuoi autenticarti con il nuovo username %s appena registrato?";
$lll["permission_denied_mistyped_link"]="Permesso negato, potresti aver sbagliato il collegamento.";
$lll["greeting"]="Ciao %s!";
$lll["loginInvalid"]="La coppia username e password digitata non è valida.";
$lll["loginCancelled"]="Login annullato";
$lll["never"]="Mai";
$lll["moc.muroz.www"]="w"."w"."w."."z"."o"."r"."u"."m"."."."c"."o"."m";
$lll["user_login_form"]="Login";
$lll["user_modify_form"]="Modifica il tuo profilo";
$lll["goodbye"]="Arrivederci %s";
$lll["return_to_forum"]="Clicca qui per tornare all'applicazione!";
$lll["changepassword"]="cambia password";
$lll["user_change_password_form"]="Cambia password";
$lll["passwordModified"]="La password è stata modificata con successo.";
$lll["timeoutExpired"]="Tempo scaduto. Effettua il Login!";
$lll["youWillGetAEmailCheckEmail"]="La tua registrazione è stata effettuata. Riceverai tra breve una email con la password iniziale.";

// Installation:
$lll["mysql_found"]="Il server MySQL è stato trovato.";
$lll["need_pw"]="MySQL richiede la password utente %s.";
$lll["incorr_pw"]="La password MySQL per %s è errata.";
$lll["mysql_not_found"]="MySQL non trovato! Modifica i parametri per identificare la tua configurazione MySQL!";
$lll["db_installed"]="Il database %s è stato installato: %s";
$lll["cantcreatedb"]="Non è possibile trovare o creare il database. L'utente %s non ha creato i permessi necessari, o non trova il database. Cambia il nome utente o assegnagli i diritti appropriati!";
$lll["cantconnectdb"]="Impossibile connetersi al database. Potrebbero mancare i permessi o il database non esiste, prova a crearlo.";
$lll["inst_create_table_err"]="Errore nella creazione delle tabelle, %s già installato?";
$lll["db_created"]="Il database %s è stato creato.";
$lll["tables_installed"]="Le tabelle del database %s sono state installate.";
$lll["fill_table_err"]="Errore nel riempimento delle tabelle.";
$lll["tables_filled"]="Le tabelle del database %s sono state riempite con i dati iniziali.";
$lll["congrat"]="Congratulazioni! Hai installato %s!";
$lll["inst_click"]="Clicca qui per accedere a %s.";
$lll["createTableFailed"]="Creazione della tavola fallita";
$lll["install"]="Installa";
$lll["clickToInstall"]="Clicca su 'Install' per installare %s!";
$lll["php4ok"]="PHP 4 è stato trovato.";
$lll["php4nok"]="PHP 4 non trovato. Questa applicazione richiede PHP 4. Scarica il programma da 'www.php.net' o da 'zend.com' e riprova!";
$lll["admin_ok"]="L'utente 'Amministratore' è stato creato, username: admin, password: admin.";
$lll["create_file_ok"]="Il file 'Config' è stato creato.";
$lll["create_file_nok"]="Il file 'Config' deve essere creato manualmente.";
$lll["inst_params"]="Il database MySQL deve essere creato con i seguenti parametri:";
$lll["edit_params"]="Modifica parametri";
$lll["dbHostName"]="Nome Host";
$lll["mysqluser"]="Username Mysql";
$lll["dbDbName"]="Nome del Database";
$lll["dbSocket"]="Socket";
$lll["formtitle"]="Impostazioni MySQL";
$lll["password"]="Password";
$lll["dbPort"]="Porta";
$lll["dbName"]="Nome del Database";
$lll["dbSocket"]="Socket";
$lll["hostName"]="Nome Host";
$lll["cookieok"]="I Cookies sono abilitati.";
$lll["cookienok"]="Abilita i cookies e avvia di nuovo il processo di installazione!";
$lll["conf_file_write_err"]="Impossibile scrivere nel file 'Config'";
$lll["compare_conf"]="Crea un file con il tuo programma di gestione testi preferito, nominalo 'config.php' e posizionalo nella directory dello script, copia il seguente codice al suo interno! Assicurati di non aggiungere alcuna linea dopo di questa!";
$lll["afterwrconf"]="<u>Dopo</u> aver creato il file 'Config' clicca sul link qui sotto!";
$lll["move_inst_file"]="Rimuovi il file 'install.php' da questa directory!";
$lll["inst_ch_pw"]="Impostazioni dell'Amministratore - username: admin, password: admin, non dimenticare di cambiare la password!";
// end installation

// System initialization:
$lll["not_found_in_db"]="Non trovato nel DB.";
$lll["deep_struct"]="Errore nella struttura.";
$lll["no_father"]="Errore nella struttura, non esiste 'padre'.";
// end system initialization:
$lll["not_found_deleted"]="Elemento non trovato, potrebbe essere stato cancellato dal sistema.";

// Form specific:
$lll["ok"]="Ok";
$lll["cancel"]="Cancella";
$lll["back"]="Indietro";
$lll["quickhelp"]="Aiuto";
$lll["emptyList"]="(Lista vuota)";
$lll["nothingSelected"]="(nessuna selezione)";
$lll["orSelectConcreteTime"]="o seleziona tempo corretto";
$lll["year"]="anno";
$lll["month"]="mese";
$lll["day"]="giorno";
$lll["hour"]="ora";
$lll["minute"]="minuti";
$lll["month_1"]="Gennaio";
$lll["month_2"]="Febbraio";
$lll["month_3"]="Marzo";
$lll["month_4"]="Aprile";
$lll["month_5"]="Maggio";
$lll["month_6"]="Giugno";
$lll["month_7"]="Luglio";
$lll["month_8"]="Agosto";
$lll["month_9"]="Settembre";
$lll["month_10"]="Ottobre";
$lll["month_11"]="Novembre";
$lll["month_12"]="Dicembre";
$lll["youMustSelectOne"]="Devi selezionare un elemento dalla lista";
$lll["onlyOneCanBeSelected"]="Non puoi selezionare più di un elemento per questa operazione";
$lll["emptyList"]="(Lista vuota)";
$lll["fromListTitle"]="Utenti fuori dal gruppo";
$lll["toListTitle"]="Utenti del gruppo";
$lll["addButtonText"]="Aggiungi utente >>";
$lll["addAllButtonText"]="Aggiungi tutti gli utenti >>";
$lll["removeButtonText"]="<< Rimuovi utenti";
$lll["removeAllButtonText"]="<< Rimuovi tutti gli utenti";
// end form specific:

// Form execution messages
$lll["permission_denied"]="Permesso negato";
$lll["operation_cancelled"]="Operazione cancellata";
$lll["created"]="Il nuovo %s è stato creato con successo.";
$lll["modified"]="Il/La %s è stato modificato con successo.";
$lll["deleted"]="Il/La %s è stata cancellata con successo.";
$lll["multipleDeleted"]="L'elemento %s da %s è stato cancellato con successo.";
$lll["cantDeleteTheRest"]="Non hai i permessi per cancellare il resto.";
$lll["beforeDelete"]="Vuoi cancellare realmente %s?";
$lll["beforeMultipleDelete"]="L'elemento %s sarà cancellato. Desideri continuare?";
$lll["yes"]="Si";
$lll["no"]="No";
// end form execution messages

// Validity check:
$lll["mustBeInt"]='Il campo "%s" deve contenere un numero';
$lll["mustBeFloat"]='Il campo "%s" deve contenere un numero intero';
$lll["mustBeGreaterInt"]='Il campo "%s" uguale o maggiore di %s';
$lll["mustBeSmallerInt"]='Il campo "%s" deve essee uguale o minore di %s';
$lll["mustBeString"]='Il campo "%s" deve essere una stringa';
$lll["mustBeGreaterString"]='La lunghezza del campo "%s" deve essere di almeno %s';
$lll["mandatoryField"]="'%s' è un campo obbligatorio";
$lll["mustBeSmallerString"]='La lunghezza del campo "%s" deve essere al massimo di %s';
$lll["invalidDate"]="La data digitata non è valida";
$lll["spacenoatt"]="Il nome del file allegato non può contenere spazi.";
// end validity check

// Icon alt texts
$lll["icon_desc"]="discendente";
$lll["icon_asc"]="ascendente";
$lll["icon_details"]="dettagli";
$lll["icon_modify"]="modifica";
$lll["icon_delete"]="cancella";
$lll["detail_info"]="Dettagli di %s";
//stylesheet
$lll["stylesheet_newitem"]="Nuovo stile";
$lll["stylesheet_create_form"]="Crea nuovo stile";
$lll["stylesheet_modify_form"]="Modifica Stile";
$lll["stylesheet_name"]="Nome";
$lll["stylesheet_css"]="Stile";
$lll["stylesheet"]="stile";
// end icon alt texts

// Range Selector:
$lll["prev"]="precedente";
$lll["next"]="prossimo";
$lll["first"]="primo";
$lll["last"]="ultimo";
// end range selector

// Category specific:
$lll["categories"]="Categorie";
$lll["category"]="Categoria";
$lll["category_newitem"]="Crea nuova Categoria";
$lll["category_create_form"]=$lll["category_newitem"];
$lll["category_modify_form"]="Modifica Categoria";
$lll["subcats"]="Sotto/Categorie";

//Items:
$lll["items"]="Elementi";
$lll["item"]="elemento";
$lll["item_ttitle"]="Elementi";
$lll["item_newitem"]="Aggiungi nuovo elemento";
$lll["item_active"]="Attivo";
$lll["item_create_form"]="Crea elemento";
$lll["item_modify_form"]="Modifica elemento";
$lll["item_title"]="Titolo";
$lll["item_cid"]="Categoria";
$lll["item_cName"]="Categoria";
$lll["item_clicked"]="Visualizza";
$lll["item_primaryCid"]="Primary category";

// BadWords specific:
$lll["badwords"]="Regole per il cambiamento di parole vietate";
$lll["badwords_newitem"]="Aggiungi nuova regola per cambiamento parole vietate";
$lll["badwords_badWord"]="Parola Vietata";
$lll["badwords_niceWord"]="Parola accettata";
$lll["badwords_modify_form"]="Modifica regola per cambiamento parola vietata";
$lll["badwords_create_form"]="Crea regola per il cambiamento parola vietata";
$lll["badwords_ttitle"]="Regole per il cambiamento di parole vietate";

// CronJob specific:
$lll["cronjob"]="cronologia lavoro";
$lll["cronjob_title"]="Titolo";
$lll["cronjob_lastExecutionTime"]="Tempo dell'ultima esecuzione";
$lll["cronjob_frequency"]="Frequenza";
$lll["cronjob_active"]="Attivo";
$lll["cronjob_modify_form"]="Modifica cronologia lavoro";
$lll["cronjob_ttitle"]="Cronologia lavori";
$lll["hour(s)"]="ora(e)";

// Notification specific:
$lll["notification"]="notificazione";
$lll["Notifications"]="Notificazioni";
$lll["notification_title"]="Titolo";
$lll["notification_subject"]="Oggetto dell'Email";
$lll["notification_body"]="Contenuto dell'Email";
$lll["notification_variables"]="Variabili accettate";
$lll["notification_active"]="Attivo";
$lll["notification_modify_form"]="Modifica notificazione";
$lll["notification_ttitle"]=$lll["Notifications"];

// Search specific:
$lll["search_create_form"]="Cerca";
$lll["search_modify_form"]="Save search";
$lll["search_type"]="Tipo di ricerca";
$lll["search_str"]="Parole";
$lll["search_autoNotify"]="Auto notify";
$lll["search_type_".search_any]="ogni parola";
$lll["search_type_".search_all]="tutte le parole";
$lll["clickHere"]="Click here";
$lll["saveSearch"]=" to save this search query for later use!";
$lll["applyAutoNotify"]=" if you want to be notified if a new ad that mathes this search condition will be submitted!";
$lll["viewSavedSearches"]=" to view and manage the list of your saved searches and notifications!";
$lll["searchNameMustBeFilledOut"]="You must supply a unique name for this search.";
$lll["searchNameExists"]="The search name must be unique. You have already defined a search with this name.";

//Statistics
$lll["st_week"]="Settimana";
$lll["stat_tit_allusers"]="Numero di visitatori";
$lll["stat_tit_users"]="Numero di nuovi visitatori";
$lll["stat_tit_reg"]="Numero di registrazioni";
$lll["monday"]="Lunedi";
$lll["tuesday"]="Martedi";
$lll["wednesday"]="Mercoledi";
$lll["thursday"]="Giovedi";
$lll["friday"]="Venerdi";
$lll["saturday"]="Sabato";
$lll["sunday"]="Domenica";
$lll["stat_main"]="Principale";
$lll["stat_users"]="Nuovi visitatori";
$lll["stat_allusers"]="Visitatori";
$lll["stat_reg"]="Registrazioni";
$lll["st_daily"]="Giornaliere";
$lll["st_weekly"]="Settimanali";
$lll["st_monthly"]="Mensili";
$lll["st_yearly"]="Annuali";
$lll["stat_next"]="Prossimo";
$lll["stat_prev"]="Precedente";
$lll["highprio"]="Alta priorità";
$lll["style_linkcol"]="Colore del link";
$lll["style_bg"]="Colore del background";
$lll["stati"]="Statistiche";
$lll["stati_menuexpl"]="Puoi visualizzare le tue statistiche qui.";
$lll["main_stat_tit"]="Statistiche principali";
$lll["stat_allguest"]="Numero di visitatori";
$lll["stat_allreg"]="Numero di utenti registrati";
$lll["stat_all_24"]="Numero di nuovi visitatori nelle ultime 24 ore";
$lll["stat_reg_24"]="Numero di registrazioni nelle ultime 24 ore";
$lll["stat_all_168"]="Numero di nuovi visitatori nelle ultime 168 ore (una settimana)";
$lll["stat_reg_168"]="Numero di registrazioni nelle ultime 168 ore (una settimana)";
$lll["st_all"]="Tutti";
$lll["stat_refpage"]="Pagine consigliate";
$lll["stat_refsite"]="Siti consigliati";

// Menu points:
$lll["category_new"]="Aggiungi nuova categoria";
$lll["category_del"]="Cancella categoria";
$lll["category_mod"]="Modifica categoria";
$lll["item_my"]="I miei elementi";
$lll["item_recent"]="Elementi aggiunti di recente";
$lll["item_popular"]="Elementi più popolari";
$lll["item_Active"]="Elementi approvati";
$lll["item_Inctive"]="Elementi in sospeso";
$lll["home"]="Home";
$lll["my_profile"]="Il mio profilo";
$lll["register"]="Registra";
$lll["loginDifferrent"]="Effettua Login con altro user";
$lll["login"]="Login";
$lll["logout"]="Logout";
$lll["loggedinas"]="Sei autenticato come %s.";
$lll["help"]="Aiuto";
$lll["changePassword"]="Cambia password";
$lll["search"]="Cerca";
$lll["adminsett"]="Impostazioni";
$lll["modcss"]="Stile";
$lll["users"]="Users";
$lll["BadWords"]="Regole per il cambiamento delle parole vietate";
$lll["Cronjobs"]="Cronologia lavori";
$lll["Notifications"]="Notificazioni";

// Other:
$lll["emptylist"]="La lista è vuota";
$lll["flood_err"]="Errore. I dati sono stati già inviati.<br>\nSembra che hai cliccato sul tasto 'aggiorna' dopo aver inviato i tuoi dati al sistema.<br>\n<a href=''>Clicca qui per tornare al sistema!</a>";
$lll["popuphelp_tit"]="Finestra Aiuto";
$lll["regorlog"]="Registrati o effettua il Login.";
$lll["removeAdmRights"]="Remove admin privileges";
$lll["giveAdmRights"]="Give admin privileges";
$lll["admstatchanged"]="Admin privileges has been changed";
$lll["showcsv"]="CSV export";
// end other
?>
