<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('user', 'nav_'.$op);
showsubmenu('nav_'.$op, [
	['nav_'.$operation, 'specialuser&operation='.$operation, 1],
	['nav_add_'.$op, 'specialuser&operation='.$op.'&suboperation=adduser', 0]]);
showtips('specialuser_'.$op.'_tips');
showformheader($url, '', 'userforum');
showtableheader();
$status ? showsubtitle(['', 'specialuser_order', 'uid', 'username', 'reason', 'operator', 'time', ''])
	: showsubtitle(['', 'specialuser_order', 'uid', 'username', 'reason', 'operator', 'time', '']);
foreach(table_home_specialuser::t()->fetch_all_by_status($status, ($page - 1) * $_G['ppp'], $_G['ppp']) as $specialuser) {

	$specialuser['dateline'] = dgmdate($specialuser['dateline']);
	$arr = [
		"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$specialuser['uid']}\">",
		"<input type=\"text\" name=\"displayorder[{$specialuser['uid']}]\" value=\"{$specialuser['displayorder']}\" size=\"8\">",
		$specialuser['uid'],
		"<a href=\"home.php?mod=space&uid={$specialuser['uid']}\" target=\"_blank\">{$specialuser['username']}</a>",
		$specialuser['reason'],
		"<a href=\"home.php?mod=space&uid={$specialuser['opuid']}\" target=\"_blank\">{$specialuser['opusername']}</a>",
		$specialuser['dateline'],
		"<a href=\"".ADMINSCRIPT."?action=specialuser&operation=$op&do=edit&uid={$specialuser['uid']}\" class=\"act\">".$lang['edit'].'</a>'
	];
	showtablerow('', '', $arr);
}
$usercount = table_home_specialuser::t()->count_by_status($status);
$multi = multi($usercount, $_G['ppp'], $page, ADMINSCRIPT."?action=specialuser&operation=$op");
showsubmit('usersubmit', 'submit', 'del', '', $multi);
showtablefooter();
showformfooter();
		