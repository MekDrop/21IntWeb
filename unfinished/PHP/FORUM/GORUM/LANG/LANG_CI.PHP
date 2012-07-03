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
$lll["settings"]="设置";
$lll["globalsettings"]="基本设置";
$lll["settings_necessaryAuthLevel"]="登陆选项";
$lll["settings_necessaryAuthLevel_expl"]="初级设置表示系统将纪录你的登陆信息，高级设置表示你必须每次登陆！";
$lll["settings_necessaryAuthLevel_".Loginlib_LowLevel]="高级设置";
$lll["settings_necessaryAuthLevel_".Loginlib_BasicLevel]="基本设置";
$lll["settings_htmlTitle"]="标题";
$lll["settings_htmlTitle_expl"]="论坛每页的标题";
$lll["settings_htmlKeywords"]="论坛页面的keywords";
$lll["settings_htmlKeywords_expl"]="能够被google或者别的搜索引擎搜索到 .";
$lll["settings_htmlDescription"]="HTML description";
$lll["settings_htmlDescription_expl"]="能够被搜索引擎搜索到.";
$lll["settings_modify_form"]="个性化设置";
$lll["globalsettings_modify_form"]="个人定制";
$lll["settings_blockSize"]="每页显示贴数";
$lll["settings_blockSize_expl"]="每页显示贴数. ";
$lll["settings_rangeBlockSize"]="Range block size";
$lll["settings_rangeBlockSize_expl"]="The maximum number of blocks that are displayed in the pager tool itself. ";
$lll["settings_headTemplate"]="页眉插入";
$lll["settings_headTemplate_expl"]="放在 <head> </head> 标签之间的代码";
$lll["settings_upperTemplate"]="页眉";
$lll["settings_upperTemplate_expl"]="放在 <body> 标签之后的代码. ";
$lll["settings_lowerTemplate"]="页脚";
$lll["settings_lowerTemplate_expl"]="放在 </body> 标签之前的代码. ";
$lll["settings_textAreaRows"]="文本区高度";
$lll["settings_textAreaRows_expl"]="文本区高度，使之最佳化. ";
$lll["settings_textAreaCols"]="文本区宽度";
$lll["settings_textAreaCols_expl"]="文本区宽度，使之最佳化. ";
$lll["settings_showExplanation"]="帮助类型";
$lll["settings_showExplanation_expl"]="你可以选择论坛帮助的类型";
$lll["settings_minPasswordLength"]="用户密码最少要";
$lll["settings_showExplanation_".Explanation_text]="文本型";
$lll["settings_showExplanation_".Explanation_qhelp]="快速帮助";
$lll["settings_showExplanation_".Explanation_no]="无";
$lll["settings_language"]="语言";
$lll["settings_language_en"]="中文";
$lll["settings_language_hu"]="匈牙利语";
$lll["settings_adminEmail"]="管理员信箱";

// Group specific:
$lll["group"]="用户组";
$lll["groups"]="用户组";
$lll["group_newitem"]="增加新组";
$lll["group_name"]="名称";
$lll["group_comment"]="备注";
$lll["group_moderator"]="板主";
$lll["group_modify_form"]="编辑组";
$lll["group_create_form"]="增加组";
$lll["group_editGroupTitle"]="编辑组成员";

// User specific:
$lll["user"]="会员";
$lll["user_newitem"]="增加新会员";
$lll["user_name"]="名称";
$lll["user_email"]="Email";
$lll["user_lastClickTime"]="最后活动时间";
$lll["user_password"]="密码";
$lll["user_passwordCopy"]="重复密码";
$lll["user_create_form"]="注册";
$lll["user_rememberPassword"]="记住密码";
$lll["user_notes"]="提醒";
$lll["user_remind_password_form"]="忘记密码";
$lll["remind_me_pw"]="我忘记了我的密码，请用电子邮件发到我的信箱!";
$lll["remindmail_subj"]="密码查询";
$lll["remindmail_text"]="你的用户名 %s\n你新的密码: %s\n点击下面的连接激活新密码,然后尝试登陆:\n%s\n\n我们建议你登陆后立即修改密码.";
$lll["remindmail_sent"]="一封电子邮件已经发到你的注册信箱，请察看";
$lll["writename_to_remind"]="请填写你的用户名和注册信箱，系统将把新的密码发送到你的电子邮箱中.";
$lll["remind_remind_password_form"]="找回密码";
$lll["remind_username"]="用户名";
$lll["invalid_email"]="邮件地址错误! .";
$lll["mistypedPassword"]="密码错误.";
$lll["passwordTooShort"]="密码至少要 %s 位.";
$lll["userAllreadyExists"]="用户名已经有人使用，请换个试试.";
$lll["cannotAcceptCookie"]="你的浏览器不支持cookies，无法注册.";
$lll["wellcomeNewlyRegistered"]="%s 恭喜你成功注册.";
$lll["loginAsNewlyRegistered"]="你已经注册了 %s.<BR>你想用 %s登陆吗？?";
$lll["permission_denied_mistyped_link"]="权限错误，请确认有没有打错网址.";
$lll["greeting"]="你好 %s!";
$lll["loginInvalid"]="密码错误.";
$lll["loginCancelled"]="取消登陆";
$lll["never"]="从不";
$lll["moc.muroz.www"]="w"."w"."w."."z"."o"."r"."u"."m"."."."c"."o"."m";
$lll["user_login_form"]="登陆";
$lll["user_modify_form"]="修改资料";
$lll["goodbye"]="%s 再见";
$lll["return_to_forum"]="点击这里返回!";
$lll["changepassword"]="修改密码";
$lll["user_change_password_form"]="修改密码";
$lll["passwordModified"]="密码修改成功.";
$lll["timeoutExpired"]="登陆超时，请重新登陆!";
$lll["youWillGetAEmailCheckEmail"]="注册成功，你的密码已经发送到你的注册信箱，请稍候察看.";

// Installation:
$lll["mysql_found"]="MySQL 发现.";
$lll["need_pw"]="MySQL 用户 %s 需要密码.";
$lll["incorr_pw"]="MySQL 用户 %s 密码错误.";
$lll["mysql_not_found"]="没有发现MySQL,请修正你的参数!";
$lll["db_installed"]="%s 数据库被正确安装: %s";
$lll["cantcreatedb"]="不能连接数据库. 用户 %s 没有连接该数据库的权限. 请修正你的参数";
$lll["cantconnectdb"]="不能正确连接数据库. 数据库不存在，请创建.";
$lll["inst_create_table_err"]="创建数据库失败, %s 已经被安装?";
$lll["db_created"]="%s 数据库 %s 创建成功.";
$lll["tables_installed"]="%s 数据库表创建成功.";
$lll["fill_table_err"]="尝试写入数据库失败.";
$lll["tables_filled"]="%s 数据库表成功写入.";
$lll["congrat"]="恭喜你!  %s 已经安装成功!";
$lll["inst_click"]="点击这里进入 %s.";
$lll["createTableFailed"]="创建表单失败";
$lll["install"]="安装";
$lll["clickToInstall"]="点击“安装”，将安装 %s!";
$lll["php4ok"]="PHP 4 被发现.";
$lll["php4nok"]="PHP 4 没有发现. 本程序需要 PHP 4. 请重新下载安装php 4!";
$lll["admin_ok"]="系统创建了管理员, 用户名: admin, 密码: admin.";
$lll["create_file_ok"]="config文件能被正确创建.";
$lll["create_file_nok"]="Config必须手动创建.";
$lll["inst_params"]="将用以下参数创建MySQL数据库:";
$lll["edit_params"]="编辑参数";
$lll["dbHostName"]="服务器";
$lll["mysqluser"]="Mysql用户名";
$lll["dbDbName"]="数据库名";
$lll["dbSocket"]="Socket";
$lll["formtitle"]="MySQL 设置";
$lll["password"]="密码";
$lll["dbPort"]="端口";
$lll["dbName"]="数据库名";
$lll["dbSocket"]="Socket";
$lll["hostName"]="服务器";
$lll["cookieok"]="允许Cookies.";
$lll["cookienok"]="请设置允许cookies然后重新执行安装程序！";
$lll["conf_file_write_err"]="config文件出现写错误！";
$lll["compare_conf"]="用你喜欢的文本编辑软件创建'config.php'，把下面的内容复制到该文件中，请别随意修改!然后复制到你的安装目录下";
$lll["afterwrconf"]="<u>然后</u> 点击下面的连接!";
$lll["move_inst_file"]="请从你的安装目录下删除install.php!";
$lll["inst_ch_pw"]="管理员选项 - 用户名: admin, 密码: admin, 别忘了立即修改密码！";
// end installation

// System initialization:
$lll["not_found_in_db"]="数据库中没有发现.";
$lll["deep_struct"]="树状结构出错, 深度太深或者错误.";
$lll["no_father"]="树状结构出错, 父目录不存在.";
// end system initialization:
$lll["not_found_deleted"]="对象没有发现, 可能已经删除.";

// Form specific:
$lll["ok"]="确定";
$lll["cancel"]="取消";
$lll["back"]="后退";
$lll["quickhelp"]="快速帮助";
$lll["emptyList"]="(没有纪录)";
$lll["nothingSelected"]="(没有选择)";
$lll["orSelectConcreteTime"]="或者选择具体时间";
$lll["year"]="年";
$lll["month"]="月";
$lll["day"]="日";
$lll["hour"]="时";
$lll["minute"]="分";
$lll["month_1"]="一月";
$lll["month_2"]="二月";
$lll["month_3"]="三月";
$lll["month_4"]="四月";
$lll["month_5"]="五月";
$lll["month_6"]="六月";
$lll["month_7"]="七月";
$lll["month_8"]="八月";
$lll["month_9"]="九月";
$lll["month_10"]="十月";
$lll["month_11"]="十一月";
$lll["month_12"]="十二月";
$lll["youMustSelectOne"]="你必须在列表中选择";
$lll["onlyOneCanBeSelected"]="你不能选多项";
$lll["emptyList"]="(没有纪录)";
$lll["fromListTitle"]="非本组成员";
$lll["toListTitle"]="本组成员";
$lll["addButtonText"]="增加成员 >>";
$lll["addAllButtonText"]="增加全体成员 >>";
$lll["removeButtonText"]="<< 删除成员";
$lll["removeAllButtonText"]="<< 删除所有成员";
// end form specific:

// Form execution messages
$lll["permission_denied"]="权限不够";
$lll["operation_cancelled"]="取消操作";
$lll["created"]=" %s 创建成功.";
$lll["modified"]="%s 成功编辑.";
$lll["deleted"]="%s 成功删除.";
$lll["multipleDeleted"]="从%s  到 %s 已经全部删除.";
$lll["cantDeleteTheRest"]="你没有权限删除.";
$lll["beforeDelete"]="你确定删除 %s?";
$lll["beforeMultipleDelete"]="%s 将被完全删除，你要继续吗？";
$lll["yes"]="是";
$lll["no"]="否";
// end form execution messages

// Validity check:
$lll["mustBeInt"]=' "%s" 必须是数字';
$lll["mustBeFloat"]='"%s" 必须是整数';
$lll["mustBeGreaterInt"]='"%s" 必须大于等于 %s';
$lll["mustBeSmallerInt"]='"%s" 必须小于等于 %s';
$lll["mustBeString"]='"%s" 必须是字符';
$lll["mustBeGreaterString"]='"%s" 长度必须在 %s 以上';
$lll["mandatoryField"]="'%s' 是必填项目";
$lll["mustBeSmallerString"]='"%s" 不能超过 %s';
$lll["invalidDate"]="填写日期错误";
$lll["spacenoatt"]="附件名称不能带空格.";
// end validity check

// Icon alt texts
$lll["icon_desc"]="升";
$lll["icon_asc"]="降";
$lll["icon_details"]="细节";
$lll["icon_modify"]="编辑";
$lll["icon_delete"]="删除";
$lll["detail_info"]="%s 细节";
//stylesheet
$lll["stylesheet_newitem"]="新的Stylesheet";
$lll["stylesheet_create_form"]="生成Stylesheet";
$lll["stylesheet_modify_form"]="编辑Stylesheet";
$lll["stylesheet_name"]="名称";
$lll["stylesheet_css"]="Stylesheet";
$lll["stylesheet"]="stylesheet";
// end icon alt texts

// Range Selector:
$lll["prev"]="后页";
$lll["next"]="前页";
$lll["first"]="首页";
$lll["last"]="尾页";
// end range selector

// Category specific:
$lll["categories"]="类别";
$lll["category"]="类别";
$lll["category_newitem"]="创建新类别";
$lll["category_create_form"]=$lll["category_newitem"];
$lll["category_modify_form"]="编辑类别";
$lll["subcats"]="子类别";

//Items:
$lll["items"]="Items";
$lll["item"]="item";
$lll["item_ttitle"]="Items";
$lll["item_newitem"]="Add new item";
$lll["item_active"]="Active";
$lll["item_create_form"]="Create item";
$lll["item_modify_form"]="Modify item";
$lll["item_title"]="标题";
$lll["item_cid"]="类别";
$lll["item_cName"]="类别";
$lll["item_clicked"]="人气";
$lll["item_primaryCid"]="Primary category";

// BadWords specific:
$lll["badwords"]="不良词语过滤规则";
$lll["badwords_newitem"]="增加不良词语过滤规则";
$lll["badwords_badWord"]="不良词语";
$lll["badwords_niceWord"]="良好词语";
$lll["badwords_modify_form"]="修改不良词语过滤规则";
$lll["badwords_create_form"]="创建不良词语过滤规则";
$lll["badwords_ttitle"]="不良词语过滤规则";

// CronJob specific:
$lll["cronjob"]="cron job";
$lll["cronjob_title"]="Title";
$lll["cronjob_lastExecutionTime"]="最后活跃";
$lll["cronjob_frequency"]="频率";
$lll["cronjob_active"]="活跃";
$lll["cronjob_modify_form"]="Modify cron job";
$lll["cronjob_ttitle"]="Cron jobs";
$lll["hour(s)"]="小时";

// Notification specific:
$lll["notification"]="公告";
$lll["Notifications"]="公告";
$lll["notification_title"]="标题";
$lll["notification_subject"]="邮件主题";
$lll["notification_body"]="邮件内容";
$lll["notification_variables"]="允许参与讨论";
$lll["notification_active"]="激活";
$lll["notification_modify_form"]="修改公告";
$lll["notification_ttitle"]=$lll["Notifications"];

// Search specific:
$lll["search_create_form"]="搜索";
$lll["search_modify_form"]="Save search";
$lll["search_type"]="搜索类型";
$lll["search_str"]="关键词";
$lll["search_autoNotify"]="Auto notify";
$lll["search_type_".search_any]="模糊搜索";
$lll["search_type_".search_all]="精确搜索";
$lll["clickHere"]="Click here";
$lll["saveSearch"]=" to save this search query for later use!";
$lll["applyAutoNotify"]=" if you want to be notified if a new ad that mathes this search condition will be submitted!";
$lll["viewSavedSearches"]=" to view and manage the list of your saved searches and notifications!";
$lll["searchNameMustBeFilledOut"]="You must supply a unique name for this search.";
$lll["searchNameExists"]="The search name must be unique. You have already defined a search with this name.";

//Statistics
$lll["st_week"]="周";
$lll["stat_tit_allusers"]="访问者人数";
$lll["stat_tit_users"]="新会员";
$lll["stat_tit_reg"]="所有会员";
$lll["monday"]="星期一";
$lll["tuesday"]="星期二";
$lll["wednesday"]="星期三";
$lll["thursday"]="星期四";
$lll["friday"]="星期五";
$lll["saturday"]="星期六";
$lll["sunday"]="星期天";
$lll["stat_main"]="基本情况";
$lll["stat_users"]="首次来访";
$lll["stat_allusers"]="访问者情况";
$lll["stat_reg"]="注册情况";
$lll["st_daily"]="每天";
$lll["st_weekly"]="每周";
$lll["st_monthly"]="每月";
$lll["st_yearly"]="每年";
$lll["stat_next"]="后页";
$lll["stat_prev"]="前页";
$lll["highprio"]="置定";
$lll["style_linkcol"]="超级连接";
$lll["style_bg"]="背景色";
$lll["stati"]="状态";
$lll["stati_menuexpl"]="你能在这里看到你的系统情况.";
$lll["main_stat_tit"]="基本情况";
$lll["stat_allguest"]="访问人数";
$lll["stat_allreg"]="注册会员";
$lll["stat_all_24"]="最近24小时的新访问人数";
$lll["stat_reg_24"]="最近24小时的注册会员";
$lll["stat_all_168"]="最近一周的访问人数";
$lll["stat_reg_168"]="最近一周的注册会员";
$lll["st_all"]="全部";
$lll["stat_refpage"]="来自的页面";
$lll["stat_refsite"]="来自的站点";

// Menu points:
$lll["category_new"]="增加新类别";
$lll["category_del"]="删除类别";
$lll["category_mod"]="编辑类别";
$lll["item_my"]="My items";
$lll["item_recent"]="Recent added items";
$lll["item_popular"]="Most popular items";
$lll["item_Active"]="Approved items";
$lll["item_Inctive"]="Pending items";
$lll["home"]="首页";
$lll["my_profile"]="我的资料";
$lll["register"]="注册";
$lll["loginDifferrent"]="注册另外用户名";
$lll["login"]="登陆";
$lll["logout"]="登出";
$lll["loggedinas"]="欢迎 %s 再次登陆.";
$lll["help"]="帮助";
$lll["changePassword"]="修改密码";
$lll["search"]="搜索";
$lll["adminsett"]="设置";
$lll["modcss"]="风格";
$lll["users"]="会员";
$lll["BadWords"]="不良词语过滤规则";
$lll["Cronjobs"]="Cron jobs";
$lll["Notifications"]="资料";

// Other:
$lll["emptylist"]="没有内容";
$lll["flood_err"]="你是在恶意灌水吗？. 贴子已经送出了.<br>\n你是不是点了刷新阿？.<br>\n<a href=''>点这里返回!</a>";
$lll["popuphelp_tit"]="Popup Help";
$lll["regorlog"]="请先注册或者登陆.";
$lll["removeAdmRights"]="Remove admin privileges";
$lll["giveAdmRights"]="Give admin privileges";
$lll["admstatchanged"]="Admin privileges has been changed";
$lll["showcsv"]="CSV export";
// end other
?>
