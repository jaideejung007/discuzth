<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET['id'] = intval($_GET['id']);
if(!submitcheck('editsubmit')) {
	$info = table_home_specialuser::t()->fetch_by_uid_status($_GET['uid'], $status);
	shownav('user', 'nav_defaultuser');
	showchildmenu([!$status ? ['nav_follow', 'specialuser&operation=follow'] : ['nav_defaultuser', 'specialuser&operation=defaultuser']], $info['username']);
	showformheader('specialuser&operation='.$op.'&do=edit&uid='.$info['uid'], '', 'userforum');
	showtableheader();
	showsetting('reason', 'reason', $info['reason'], 'text');
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();
} else {

	if(!$_GET['reason']) {
		cpmsg('specialuser_'.$op.'_noreason_invalid', 'action=specialuser&operation='.$op, 'error');
	}
	$updatearr = ['reason' => $_GET['reason']];
	table_home_specialuser::t()->update_by_uid_status($_GET['uid'], $status, $updatearr);
	cpmsg('specialuser_defaultuser_edit_succeed', 'action=specialuser&operation='.$op, 'succeed');
}
		