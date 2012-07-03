<?php
function appFillTables()
{
    global $lll,$gorumuser;
    
    $zorumVersion="3_3";

    $g = new Group;
    $g->name = $lll["settings_allowHtmlInPost_".Group_All];
    $g->ownerId = $gorumuser->id;
    create($g);
    
    $g = new Group;
    $g->name = $lll["settings_allowHtmlInPost_".Group_OnlyAdmin];
    $g->ownerId = $gorumuser->id;
    create($g);

    $g = new Group;
    $g->name = $lll["settings_allowHtmlInPost_".Group_OnlyAdminAndMod];
    $g->ownerId = $gorumuser->id;
    create($g);

    $stat = new GlobalStat;
    $stat->forumnum=0;
    $stat->topicnum=0;
    $stat->entrynum=0;
    $stat->usernum=1;
    $stat->instver=$zorumVersion;
    create($stat);

    global $zorumglobalstat;
    $zorumglobalstat = $stat;
    
    createDefaultUbbRules();
    createDefaultSmilies();

    $forum1 = new Forum;
    $forum1->name = "Example Forum 1";
    $forum1->description = "Example Forum 1 description";
    $forum1->create();
    
    $forum2 = new Forum;
    $forum2->name = "Example Forum 2";
    $forum2->description = "Example Forum 2 description";
    $forum2->create();
    
    global $gorumroll;
    $gorumroll->submit=$lll["ok"];
    $gorumroll->list="topic";

    $topic1 = new Topic;
    $topic1->pid = $forum1->id;
    $topic1->subject = "topic 1 in forum 1";
    $topic1->txt = "topic 1 txt in forum 1";
    $topic1->coding = MessCode_no;
    $topic1->smiley = FALSE;
    $topic1->create();
    
    $topic2 = new Topic;
    $topic2->pid = $forum1->id;
    $topic2->subject = "topic 2 in forum 1";
    $topic2->txt = "topic 2 txt in forum 1";
    $topic2->coding = MessCode_no;
    $topic2->smiley = FALSE;
    $topic2->create();
    
    $topic3 = new Topic;
    $topic3->pid = $forum2->id;
    $topic3->subject = "topic 3 in forum 2";
    $topic3->txt = "topic 3 txt in forum 2";
    $topic3->coding = MessCode_no;
    $topic3->smiley = FALSE;
    $topic3->create();
    
}

function createDefaultUbbRules()
{
    global $lll, $gorumroll;

    $gorumroll->class = "ubb";
    $u = new Ubb;
    $u->ubbBegin="\[url=([^[']+)\]";
    $u->replacementBegin="<a href='\\1'>";
    $u->ubbEnd="\[/url\]";
    $u->replacementEnd="</a>";
    $u->comment=$lll["Link text"]."[code][url=...]...[/url][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[b\]";
    $u->replacementBegin="<b>";
    $u->ubbEnd="\[/b\]";
    $u->replacementEnd="</b>";
    $u->comment=$lll["Bold text"]."[code][b]...[/b][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[i\]";
    $u->replacementBegin="<i>";
    $u->ubbEnd="\[/i\]";
    $u->replacementEnd="</i>";
    $u->comment=$lll["Italics text"]."[code][i]...[/i][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[s\]";
    $u->replacementBegin="<s>";
    $u->ubbEnd="\[/s\]";
    $u->replacementEnd="</s>";
    $u->comment=$lll["Strikethrough text"]."[code][s]...[/s][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[colo[u]?r=([^[']+)\]";
    $u->replacementBegin="<font color='\\1'>";
    $u->ubbEnd="\[/colo[u]?r\]";
    $u->replacementEnd="</font>";
    $u->comment=$lll["Color text"]."[code][color=...]...[/color][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[email=([^[']+)\]([^\[]+)\[/email\]";
    $u->replacementBegin="<a href='mailto:\\1'>\\2</a>";
    $u->comment=$lll["Email link"]."[code][email=...]...[/email][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[email\]([^\[]+)\[/email\]";
    $u->replacementBegin="<a href='mailto:\\1'>\\1</a>";
    $u->comment=$lll["Email link"]."[code][email]...[/email][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[u\]";
    $u->replacementBegin="<u>";
    $u->ubbEnd="\[/u\]";
    $u->replacementEnd="</u>";
    $u->comment=$lll["Underline text"]."[code][u]...[/u][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[center\]";
    $u->replacementBegin="<center>";
    $u->ubbEnd="\[/center\]";
    $u->replacementEnd="</center>";
    $u->comment=$lll["Center text"]."[code][center]...[/center][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[font=([^[']+)\]";
    $u->replacementBegin="<font face='\\1'>";
    $u->ubbEnd="\[/font\]";
    $u->replacementEnd="</font>";
    $u->comment=$lll["Font face"]."[code][font=...]...[/font][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[size=([[:digit:]]+)\]";
    $u->replacementBegin="<font size='\\1'>";
    $u->ubbEnd="\[/size\]";
    $u->replacementEnd="</font>";
    $u->comment=$lll["Font size"]."[code][size=...]...[/size][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[list\]";
    $u->replacementBegin="<ul>";
    $u->ubbEnd="\[/list\]";
    $u->replacementEnd="</ul>";
    $u->comment=$lll["Unordered list"]."[code][list]...[/list][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[list=([[:alnum:]]+)\]";
    $u->replacementBegin="<ol type='\\1'>";
    $u->ubbEnd="\[/list=[[:alnum:]]*\]";
    $u->replacementEnd="</ol>";
    $u->comment=$lll["Ordered list"]."[code][list=...]...[/list][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[\*\]";
    $u->replacementBegin="<li>";
    $u->comment=$lll["List item"]."[code][*]...[/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[img\]http:/"."/([^\[]+)\[/img\]";
    $u->replacementBegin="<img src='http:/"."/\\1'>";
    $u->comment=$lll["Inline image"]."[code][img]http:/"."/...[/img][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[quote\]";
    $u->replacementBegin="<blockquote><hr>";
    $u->ubbEnd="\[/quote\]";
    $u->replacementEnd="<hr></blockquote>";
    $u->comment=$lll["Quote text"]."[code][quote]...[/quote][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="\[code\]";
    $u->replacementBegin="<pre>";
    $u->ubbEnd="\[/code\]";
    $u->replacementEnd="</pre>";
    $u->comment=$lll["Inline code"]."[code][code]...[/code][/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="(http:/"."/[^[:space:]',]+)([[:space:]]|([[:punct:]][[:space:]])|[,])";
    $u->replacementBegin="<a href='\\1'>\\1</a>\\2";
    $u->comment=$lll["Link text"]."[code]http:/"."/...[/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Ubb;
    $u->ubbBegin="(www\.[^[:space:]',<[]+)([[:space:]]|([[:punct:]][[:space:]])|[,<[])";
    $u->replacementBegin="<a href='http:/"."/\\1'>\\1</a>\\2";
    $u->comment=$lll["Link text"]."[code]www...[/code]";
    $u->enabled=TRUE;
    $u->create(TRUE);
}
function createDefaultSmilies()
{
    global $lll, $gorumroll;

    $gorumroll->class = "ubb";
    $u = new Smiley;
    $u->ubbBegin=":)";
    $u->replacementBegin="<img src='i/smile.gif' width='15'".
                         " height='15'>";
    $u->comment=":) ".$lll["Smile"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=":-)";
    $u->replacementBegin="<img src='i/smile.gif' width='15'".
                         " height='15'>";
    $u->comment=":-) ".$lll["Smile"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=";)";
    $u->replacementBegin="<img src='i/wink.gif' width='15'".
                         " height='15'>";
    $u->comment=";) ".$lll["Wink"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=";-)";
    $u->replacementBegin="<img src='i/wink.gif' width='15'".
                         " height='15'>";
    $u->comment=";-) ".$lll["Wink"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=":\(";
    $u->replacementBegin="<img src='i/sad.gif' width='15'".
                         " height='15'>";
    $u->comment=":\( ".$lll["Sad"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=":-\(";
    $u->replacementBegin="<img src='i/sad.gif' width='15'".
                         " height='15'>";
    $u->comment=":-\( ".$lll["Sad"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=":o";
    $u->replacementBegin="<img src='i/surprised.gif' width='15'".
                         " height='15'>";
    $u->comment=":o ".$lll["Surprised"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin=":-o";
    $u->replacementBegin="<img src='i/surprised.gif' width='15'".
                         " height='15'>";
    $u->comment=":-o ".$lll["Surprised"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin="8)";
    $u->replacementBegin="<img src='i/cool.gif' width='15'".
                         " height='15'>";
    $u->comment="8) ".$lll["Cool"];
    $u->enabled=TRUE;
    $u->create(TRUE);
    $u = new Smiley;
    $u->ubbBegin="8-)";
    $u->replacementBegin="<img src='i/cool.gif' width='15'".
                         " height='15'>";
    $u->comment="8-) ".$lll["Cool"];
    $u->enabled=TRUE;
    $u->create(TRUE);
}
?>
