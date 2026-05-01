<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td28"', 'class="td23"', 'class="td23"', 'class="td23"'], [
	cplang('warn_info'),
	cplang('members_warn'),
	cplang('members_access_adminuser'),
	cplang('members_access_dateline'),
]);

$warncount = table_forum_warning::t()->count_by_author($_GET['keyword'] ? explode(',', $_GET['keyword']) : null);
table_forum_warning::t()->fetch_all_by_author(($_GET['keyword'] ? explode(',', $_GET['keyword']) : null), $start, $lpp);
foreach(table_forum_warning::t()->fetch_all_by_author(($_GET['keyword'] ? explode(',', $_GET['keyword']) : null), $start, $lpp) as $row) {
	showtablerow('', ['class="td28"', 'class=""', '', 'class="td26"', ''], [
		'<b>'.cplang('warn_url').'</b><a href="forum.php?mod=redirect&goto=findpost&pid='.$row['pid'].'" target="_blank">'.$_G['siteurl'].'forum.php?mod=redirect&goto=findpost&pid='.$row['pid'].'</a><br><b>'.cplang('warn_reason').'</b>'.$row['reason'],
		'<a href="home.php?mod=space&uid='.$row['authorid'].'">'.$row['author'].'</a>',
		'<a href="home.php?mod=space&uid='.$row['operatorid'].'">'.$row['operator'].'</a>',
		dgmdate($row['dateline'], 'y-m-d H:i'),
	]);
}
$multipage = multi($warncount, $lpp, $page, ADMINSCRIPT."?action=logs&operation=$operation&keyword=".rawurlencode($_GET['keyword'])."&lpp=$lpp", 0, 3);
	