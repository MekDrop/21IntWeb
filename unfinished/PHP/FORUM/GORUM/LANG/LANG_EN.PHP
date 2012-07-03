<?php
//
$lll["inst_ch_pw_withEmailAccount"]="Administrator settings - email: admin@admin.admin, password: admin, display name: admin. Don't forget to change the password!";
$lll["admin_ok_withEmailAccount"]="Administrator user has been created, email: admin@admin.admin, password: admin, display name: admin.";
$lll["userAllreadyExistsWithEmail"]="A user already exists with this email.";
$lll["userAllreadyExistsWithName"]="A user already exists with this display name.";
$lll["user_displayName"]="Display name";
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
$lll["settings"]="Settings";
$lll["globalsettings"]="Global settings";
$lll["settings_necessaryAuthLevel"]="Necessary authentication level for write permission";
$lll["settings_necessaryAuthLevel_expl"]="Basic level means that the program remembers your password and authenticate you authomatically. With Advanced level, you have to log in first.";
$lll["settings_necessaryAuthLevel_".Loginlib_LowLevel]="Advanced level";
$lll["settings_necessaryAuthLevel_".Loginlib_BasicLevel]="Basic level";
$lll["settings_htmlTitle"]="HTML title";
$lll["settings_htmlTitle_expl"]="The content of the TITLE tag in the HTML header of the forum pages";
$lll["settings_htmlKeywords"]="HTML keywords";
$lll["settings_htmlKeywords_expl"]="The 'keywords' META tag in the HTML header of the forum pages. This is used mainly by the search engines to index the pages.";
$lll["settings_htmlDescription"]="HTML description";
$lll["settings_htmlDescription_expl"]="The 'description' META tag in the HTML header of the forum pages. This is used mainly by the search engines to index the pages.";
$lll["settings_modify_form"]="Personalize";
$lll["globalsettings_modify_form"]="Customize global settings";
$lll["settings_blockSize"]="Block size";
$lll["settings_blockSize_expl"]="The maximum number of list elements that are displayed on one page. ";
$lll["settings_rangeBlockSize"]="Range block size";
$lll["settings_rangeBlockSize_expl"]="The maximum number of blocks that are displayed in the pager tool itself. ";
$lll["settings_headTemplate"]="Head template";
$lll["settings_headTemplate_expl"]="The text you write here will appear in the header section of the generated html page";
$lll["settings_upperTemplate"]="Top template";
$lll["settings_upperTemplate_expl"]="You can define here an HTML section that will appear above the Zorum page content, or give a php filename which will be executed. This feature can be used to integrate Zorum better in an existing site appearance. ";
$lll["settings_lowerTemplate"]="Bottom template";
$lll["settings_lowerTemplate_expl"]="you can define here an HTML section that will appear below the Zorum page content, or give a php filename which will be executed. This feature can be used to integrate Zorum better in an existing site appearance. ";
$lll["settings_textAreaRows"]="Height of the message text area field";
$lll["settings_textAreaRows_expl"]="You can set the size of the message text input box that suits best to your display and browser window size. ";
$lll["settings_textAreaCols"]="Length of the message text area field";
$lll["settings_textAreaCols_expl"]="You can set the size of the message text input box that suits best to your display and browser window size. ";
$lll["settings_showExplanation"]="Show explanation texts";
$lll["settings_showExplanation_expl"]="What you are currently reading is an explanation text. You can hide them or display as text or as an icon with a short help";
$lll["settings_minPasswordLength"]="Minimum length of the user password";
$lll["settings_showExplanation_".Explanation_text]="text mode";
$lll["settings_showExplanation_".Explanation_qhelp]="quickhelp mode";
$lll["settings_showExplanation_".Explanation_no]="no help";
$lll["settings_language"]="Language";
$lll["settings_language_en"]="English";
$lll["settings_language_hu"]="Hungarian";
$lll["settings_adminEmail"]="Admin email";

// Group specific:
$lll["group"]="group";
$lll["groups"]="Groups";
$lll["group_newitem"]="Add new group";
$lll["group_name"]="Name";
$lll["group_comment"]="Comment";
$lll["group_moderator"]="Moderator";
$lll["group_modify_form"]="Modify group";
$lll["group_create_form"]="Create group";
$lll["group_editGroupTitle"]="Edit group members";

// User specific:
$lll["user"]="user";
$lll["user_newitem"]="Add new user";
$lll["user_name"]="Name";
$lll["user_email"]="Email";
$lll["user_lastClickTime"]="Last click";
$lll["user_password"]="Password";
$lll["user_passwordCopy"]="Repeat password";
$lll["user_create_form"]="Register";
$lll["user_rememberPassword"]="Remember password";
$lll["user_notes"]="Notes";
$lll["user_remind_password_form"]="Remind password";
$lll["remind_me_pw"]="I have forgotten my password, send me a new one per email!";
$lll["remindmail_subj"]="Password reminder";
$lll["remindmail_text"]="Your username is: %s\nYour new password is: %s\nClick on the following link to activate the password, than try to log in:\n%s\n\nIt is recommended that you change this password after the login.";
$lll["remindmail_sent"]="A mail has been sent to you with a new password";
$lll["writename_to_remind"]="Write in your username, click on ok, and an email with a new password will be sent to you.";
$lll["remind_remind_password_form"]="Password reminder";
$lll["remind_username"]="Your user name";
$lll["invalid_email"]="Invalid email address! There is no user with this email in the system.";
$lll["mistypedPassword"]="You have mistyped your password.";
$lll["passwordTooShort"]="The length of the password must be minimum %s characters.";
$lll["userAllreadyExists"]="The given user already exists.";
$lll["cannotAcceptCookie"]="Your browser can not accept cookies, you can't register yourself.";
$lll["wellcomeNewlyRegistered"]="New user %s has been successfully registered.";
$lll["loginAsNewlyRegistered"]="You are currently loged in as user %s.<BR>Do You want to log in with your new registered user name %s?";
$lll["permission_denied_mistyped_link"]="Permission denied, you might mistype the link.";
$lll["greeting"]="Hello %s!";
$lll["loginInvalid"]="The given username password pair is invalid.";
$lll["loginCancelled"]="Login cancelled";
$lll["never"]="Never";
$lll["moc.muroz.www"]="w"."w"."w."."z"."o"."r"."u"."m"."."."c"."o"."m";
$lll["user_login_form"]="Login";
$lll["user_modify_form"]="Modify user profile";
$lll["goodbye"]="Goodbye %s";
$lll["return_to_forum"]="Click here to return to the application!";
$lll["changepassword"]="change password";
$lll["user_change_password_form"]="Change password";
$lll["passwordModified"]="The password is successfully modified.";
$lll["timeoutExpired"]="Timeout expired. Please, log in!";
$lll["youWillGetAEmailCheckEmail"]="Your registration was successfull. You will soon get an email with your initial password.";

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
$lll["cancel"]="Cancel";
$lll["back"]="Back";
$lll["quickhelp"]="Quickhelp";
$lll["emptyList"]="(Empty list)";
$lll["nothingSelected"]="(Nothing selected)";
$lll["orSelectConcreteTime"]="or select concrete time";
$lll["year"]="year";
$lll["month"]="month";
$lll["day"]="day";
$lll["hour"]="hour";
$lll["minute"]="minute";
$lll["month_1"]="January";
$lll["month_2"]="February";
$lll["month_3"]="March";
$lll["month_4"]="April";
$lll["month_5"]="May";
$lll["month_6"]="June";
$lll["month_7"]="July";
$lll["month_8"]="August";
$lll["month_9"]="September";
$lll["month_10"]="October";
$lll["month_11"]="November";
$lll["month_12"]="December";
$lll["youMustSelectOne"]="You must select an item in the list";
$lll["onlyOneCanBeSelected"]="You can't select more than one items for this operation";
$lll["emptyList"]="(Empty list)";
$lll["fromListTitle"]="Users outside the group";
$lll["toListTitle"]="Users inside the group";
$lll["addButtonText"]="Add users >>";
$lll["addAllButtonText"]="Add all users >>";
$lll["removeButtonText"]="<< Remove users";
$lll["removeAllButtonText"]="<< Remove all users";
// end form specific:

// Form execution messages
$lll["permission_denied"]="Permission denied";
$lll["operation_cancelled"]="Operation cancelled";
$lll["created"]="The new %s is successfully created.";
$lll["modified"]="The %s is successfully modified.";
$lll["deleted"]="The %s is successfully deleted.";
$lll["multipleDeleted"]="%s items from %s have been successfully deleted.";
$lll["cantDeleteTheRest"]="You don't have permission to delete the rest.";
$lll["beforeDelete"]="Do you really want to delete the %s?";
$lll["beforeMultipleDelete"]="%s items will be deleted. Do you really want to continue?";
$lll["yes"]="Yes";
$lll["no"]="No";
// end form execution messages

// Validity check:
$lll["mustBeInt"]='The form field "%s" must be a number';
$lll["mustBeFloat"]='The form field "%s" must be a float number';
$lll["mustBeGreaterInt"]='The form field "%s" must be greater than or equal to %s';
$lll["mustBeSmallerInt"]='The form field "%s" must be smaller than or equal to %s';
$lll["mustBeString"]='The form field "%s" must be a string';
$lll["mustBeGreaterString"]='The length of the form field "%s" must be minimum %s';
$lll["mandatoryField"]="'%s' is a mandatory field";
$lll["mustBeSmallerString"]='The length of the form field "%s" must be maximum %s';
$lll["invalidDate"]="The given date was invalid";
$lll["spacenoatt"]="The name of the attached file can't contain spaces.";
// end validity check

// Icon alt texts
$lll["icon_desc"]="descending";
$lll["icon_asc"]="ascending";
$lll["icon_details"]="details";
$lll["icon_modify"]="modify";
$lll["icon_delete"]="delete";
$lll["detail_info"]="%s Details";
//stylesheet
$lll["stylesheet_newitem"]="New Stylesheet";
$lll["stylesheet_create_form"]="Create New Stylesheet";
$lll["stylesheet_modify_form"]="Modify Stylesheet";
$lll["stylesheet_name"]="Name";
$lll["stylesheet_css"]="Stylesheet";
$lll["stylesheet"]="stylesheet";
// end icon alt texts

// Range Selector:
$lll["prev"]="prev";
$lll["next"]="next";
$lll["first"]="first";
$lll["last"]="last";
// end range selector

// Category specific:
$lll["categories"]="Categories";
$lll["category"]="Category";
$lll["category_newitem"]="Create New Category";
$lll["category_create_form"]=$lll["category_newitem"];
$lll["category_modify_form"]="Modify Category";
$lll["subcats"]="Subcategories";

//Items:
$lll["items"]="Items";
$lll["item"]="item";
$lll["item_ttitle"]="Items";
$lll["item_newitem"]="Add new item";
$lll["item_active"]="Active";
$lll["item_create_form"]="Create item";
$lll["item_modify_form"]="Modify item";
$lll["item_title"]="Title";
$lll["item_cid"]="Category";
$lll["item_cName"]="Category";
$lll["item_clicked"]="Views";
$lll["item_primaryCid"]="Primary category";

// BadWords specific:
$lll["badwords"]="bad word replacement rule";
$lll["badwords_newitem"]="Add new bad word replacement rule";
$lll["badwords_badWord"]="Bad word";
$lll["badwords_niceWord"]="Nice word";
$lll["badwords_modify_form"]="Modify bad word replacement rule";
$lll["badwords_create_form"]="Create bad word replacement rule";
$lll["badwords_ttitle"]="Bad word replacement rules";

// CronJob specific:
$lll["cronjob"]="cron job";
$lll["cronjob_title"]="Title";
$lll["cronjob_lastExecutionTime"]="Last execution time";
$lll["cronjob_frequency"]="Frequency";
$lll["cronjob_active"]="Active";
$lll["cronjob_modify_form"]="Modify cron job";
$lll["cronjob_ttitle"]="Cron jobs";
$lll["hour(s)"]="hour(s)";

// Notification specific:
$lll["notification"]="notification";
$lll["Notifications"]="Notifications";
$lll["notification_title"]="Title";
$lll["notification_subject"]="Email subject";
$lll["notification_body"]="Email body";
$lll["notification_variables"]="Allowed variables";
$lll["notification_active"]="Active";
$lll["notification_modify_form"]="Modify notification";
$lll["notification_ttitle"]=$lll["Notifications"];

// Search specific:
$lll["search_create_form"]="Search";
$lll["search_modify_form"]="Save search";
$lll["search_type"]="Search type";
$lll["search_str"]="Words";
$lll["search_autoNotify"]="Auto notify";
$lll["search_type_".search_any]="any words";
$lll["search_type_".search_all]="all words";
$lll["clickHere"]="Click here";
$lll["saveSearch"]=" to save this search query for later use!";
$lll["applyAutoNotify"]=" if you want to be notified if a new ad that mathes this search condition will be submitted!";
$lll["viewSavedSearches"]=" to view and manage the list of your saved searches and notifications!";
$lll["searchNameMustBeFilledOut"]="You must supply a unique name for this search.";
$lll["searchNameExists"]="The search name must be unique. You have already defined a search with this name.";

//Statistics
$lll["st_week"]="Week";
$lll["stat_tit_allusers"]="Number of visitors";
$lll["stat_tit_users"]="Number of new visitors";
$lll["stat_tit_reg"]="Number of registrations";
$lll["monday"]="Monday";
$lll["tuesday"]="Tuesday";
$lll["wednesday"]="Wednesday";
$lll["thursday"]="Thursday";
$lll["friday"]="Friday";
$lll["saturday"]="Saturday";
$lll["sunday"]="Sunday";
$lll["stat_main"]="Main";
$lll["stat_users"]="New visitors";
$lll["stat_allusers"]="Visitors";
$lll["stat_reg"]="Registrations";
$lll["st_daily"]="Daily";
$lll["st_weekly"]="Weekly";
$lll["st_monthly"]="Monthly";
$lll["st_yearly"]="Yearly";
$lll["stat_next"]="Next";
$lll["stat_prev"]="Previous";
$lll["highprio"]="High priority";
$lll["style_linkcol"]="Link color";
$lll["style_bg"]="Background color";
$lll["stati"]="Statistics";
$lll["stati_menuexpl"]="You can see the statistics of your system here.";
$lll["main_stat_tit"]="Main statistics";
$lll["stat_allguest"]="Number of visitors";
$lll["stat_allreg"]="Number of registered users";
$lll["stat_all_24"]="Number of new visitors in the last 24 hours";
$lll["stat_reg_24"]="Number of registrations in the last 24 hours";
$lll["stat_all_168"]="Number of new visitors in the last 168 hours (one week)";
$lll["stat_reg_168"]="Number of registrations in the last 168 hours (one week)";
$lll["st_all"]="All";
$lll["stat_refpage"]="Referer pages";
$lll["stat_refsite"]="Referer sites";

// Menu points:
$lll["category_new"]="Add new category";
$lll["category_del"]="Delete category";
$lll["category_mod"]="Modify category";
$lll["item_my"]="My items";
$lll["item_recent"]="Recent added items";
$lll["item_popular"]="Most popular items";
$lll["item_Active"]="Approved items";
$lll["item_Inctive"]="Pending items";
$lll["home"]="Home";
$lll["my_profile"]="My profile";
$lll["register"]="Register";
$lll["loginDifferrent"]="Login different";
$lll["login"]="Login";
$lll["logout"]="Logout";
$lll["loggedinas"]="You are logged in as %s.";
$lll["help"]="Help";
$lll["changePassword"]="Change password";
$lll["search"]="Search";
$lll["adminsett"]="Settings";
$lll["modcss"]="Style";
$lll["users"]="Users";
$lll["BadWords"]="Bad word replacement rules";
$lll["Cronjobs"]="Cron jobs";
$lll["Notifications"]="Notifications";

// Other:
$lll["emptylist"]="The list is empty";
$lll["flood_err"]="Flooding error. The data already has been sent.<br>\nIt seems that you have clicked the reload/refresh button after sending some data to the system.<br>\n<a href=''>Click here to return to the system!</a>";
$lll["popuphelp_tit"]="Popup Help";
$lll["regorlog"]="Please register or log in.";
$lll["removeAdmRights"]="Remove admin privileges";
$lll["giveAdmRights"]="Give admin privileges";
$lll["admstatchanged"]="Admin privileges has been changed";
$lll["showcsv"]="CSV export";
// end other
?>
