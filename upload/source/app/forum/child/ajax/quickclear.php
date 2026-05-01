<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$uid = intval($_GET['uid']);
if($_G['adminid'] != 1) {
	showmessage('quickclear_noperm');
}
include_once libfile('function/misc');
include_once libfile('function/member');

if(!submitcheck('qclearsubmit')) {
	$crimenum_avatar = crime('getcount', $uid, 'crime_avatar');
	$crimenum_sightml = crime('getcount', $uid, 'crime_sightml');
	$crimenum_customstatus = crime('getcount', $uid, 'crime_customstatus');
	$crimeauthor = getuserbyuid($uid);
	$crimeauthor = $crimeauthor['username'];

	include template('forum/ajax');
} else {
	if(empty($_GET['operations'])) {
		showmessage('quickclear_need_operation');
	}
	$reason = checkreasonpm();
	$allowop = ['avatar', 'sightml', 'customstatus'];
	$cleartype = [];
	if(in_array('avatar', $_GET['operations'])) {
		table_common_member::t()->update($uid, ['avatarstatus' => 0]);
		loaducenter();
		uc_user_deleteavatar($uid);
		$cleartype[] = lang('forum/misc', 'avatar');
		crime('recordaction', $uid, 'crime_avatar', lang('forum/misc', 'crime_reason', ['reason' => $reason]));
	}
	if(in_array('sightml', $_GET['operations'])) {
		table_common_member_field_forum::t()->update($uid, ['sightml' => ''], 'UNBUFFERED');
		$cleartype[] = lang('forum/misc', 'signature');
		crime('recordaction', $uid, 'crime_sightml', lang('forum/misc', 'crime_reason', ['reason' => $reason]));
	}
	if(in_array('customstatus', $_GET['operations'])) {
		table_common_member_field_forum::t()->update($uid, ['customstatus' => ''], 'UNBUFFERED');
		$cleartype[] = lang('forum/misc', 'custom_title');
		crime('recordaction', $uid, 'crime_customstatus', lang('forum/misc', 'crime_reason', ['reason' => $reason]));
	}
	if(($_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
		sendreasonpm(['authorid' => $uid], 'reason_quickclear', [
			'cleartype' => implode(',', $cleartype),
			'reason' => $reason,
			'from_id' => 0,
			'from_idtype' => 'quickclear'
		]);
	}
	showmessage('quickclear_success', $_POST['redirect'], [], ['showdialog' => 1, 'closetime' => true, 'msgtype' => 2, 'locationtime' => 1]);
}
	