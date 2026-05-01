<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$step = intval($_GET['step']);
$splitnum = max(10, intval($_GET['splitnum']));
$ret = table_common_member_archive::t()->rebuild_table($step);
if($ret === false) {
	cpmsg('membersplit_split_check_table_done', 'action=membersplit&operation=manage&membersplit_split_submit=1&nocheck=1&splitnum='.$splitnum, 'loadingform');
} else if($ret === true) {
	cpmsg('membersplit_split_checking_table', 'action=membersplit&operation=rebuildtable&splitnum='.$splitnum.'&step='.($step + 1), 'loadingform', ['step' => $step + 1]);
} else {
	cpmsg('membersplit_split_check_table_fail', 'action=membersplit&operation=manage&splitnum='.$splitnum, 'error', ['tablename' => $ret]);
}
	