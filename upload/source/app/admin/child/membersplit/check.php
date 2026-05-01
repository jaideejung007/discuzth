<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('founder', 'nav_membersplit');
showsubmenu('membersplit');
/*search={"nav_membersplit":"action=membersplit","nav_membersplit":"action=membersplit&operation=check"}*/
showtips('membersplit_check_tips');
/*search*/
showformheader('membersplit&operation=manage');
showtableheader('membersplit_table_orig');
$membercount = $_G['cache']['userstats']['totalmembers'];

showsubtitle(['', '', 'membersplit_count', 'membersplit_lasttime_check']);


if($membercount < 50000) {
	$msg = $lang['membersplit_without_optimization'];
} else {
	$msg = empty($_G['cache']['membersplitdata']) ? $lang['membersplit_has_no_check'] : dgmdate($_G['cache']['membersplitdata']['dateline']);
}
showtablerow('', '', ['', '', number_format($membercount), $msg]);

if($membercount >= 50000) {
	showsubmit('membersplit_check_submit', 'membersplit_check');
}
showtablefooter();
showformfooter();
	