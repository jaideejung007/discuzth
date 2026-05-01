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
if(!submitcheck('membersplit_split_submit', 1)) {
	showsubmenu('membersplit');
	
	showtips('membersplit_tips');
	
	showformheader('membersplit&operation=manage');
	showtableheader('membersplit_table_orig');

	if($_G['cache']['membersplitdata'] && $_G['cache']['membersplitdata']['dateline'] > TIMESTAMP - 86400) {
		$zombiecount = $_G['cache']['membersplitdata']['zombiecount'];
	} else {
		$zombiecount = table_common_member::t()->count_zombie();
		if($zombiecount >= 1) {
			$zombiecount--;
		}
		savecache('membersplitdata', ['zombiecount' => $zombiecount, 'dateline' => TIMESTAMP]);
	}
	$membercount = $_G['cache']['userstats']['totalmembers'];
	$percentage = round($zombiecount / $membercount, 4) * 100;

	showsubtitle(['', '', 'membersplit_count', 'membersplit_combie_count', 'membersplit_splitnum']);
	showtablerow('', '',
		['', '', number_format($membercount), number_format($zombiecount).'('.$percentage.'%) ', '<input name="splitnum" value="200" type="text" class="txt"/>']);

	if($percentage > 0) {
		showsubmit('membersplit_split_submit', 'membersplit_archive');
	}
	showtablefooter();
	showformfooter();

} else {
	$step = intval($_GET['step']) + 1;
	$splitnum = max(10, intval($_GET['splitnum']));
	if(!$_GET['nocheck'] && $step == 1 && !table_common_member_archive::t()->check_table()) {
		cpmsg('membersplit_split_check_table', 'action=membersplit&operation=rebuildtable&splitnum='.$splitnum, 'loadingform', []);
		cpmsg('', 'action=membersplit&operation=manage', 'error');
	}
	if(!table_common_member::t()->split($splitnum)) {
		cpmsg('membersplit_split_succeed', 'action=membersplit&operation=manage', 'succeed');
	}
	cpmsg('membersplit_split_doing', 'action=membersplit&operation=manage&membersplit_split_submit=1&step='.$step.'&splitnum='.$splitnum, 'loadingform', ['num' => $step * $splitnum]);
}
	