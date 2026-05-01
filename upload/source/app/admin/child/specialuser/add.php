<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('addsubmit')) {

	shownav('user', 'nav_'.$op);
	showsubmenu('nav_'.$op, [
		['nav_'.$operation, 'specialuser&operation='.$operation, 0],
		['nav_add_'.$op, 'specialuser&operation='.$op.'&suboperation=adduser', 1]]);
	showtips('specialuser_defaultuser_add_tips');
	showformheader('specialuser&operation='.$op.'&suboperation=adduser', '', 'userforum');
	showtableheader();
	showsetting('username', 'username', '', 'text');
	showsetting('reason', 'reason', '', 'text');
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();

} else {

	$username = trim($_GET['username']);
	$reason = trim($_GET['reason']);

	if(!$username || !$reason) {
		cpmsg('specialuser_defaultuser_add_invaild', '', 'error');
	}

	if(table_home_specialuser::t()->count_by_status($status, $username)) {
		cpmsg('specialuser_defaultuser_added_invalid', '', 'error');
	}

	$member = table_common_member::t()->fetch_by_username($username);
	if(empty($member)) {
		cpmsg('specialuser_defaultuser_nouser_invalid', '', 'error');
	}

	$data = [
		'status' => $status,
		'uid' => $member['uid'],
		'username' => $member['username'],
		'reason' => $reason,
		'dateline' => $_G['timestamp'],
		'opuid' => $_G['member']['uid'],
		'opusername' => $_G['member']['username']
	];

	if(table_home_specialuser::t()->insert($data)) {
		cpmsg('specialuser_'.$op.'_add_succeed', 'action='.$url, 'succeed');
	}
}
	