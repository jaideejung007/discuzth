<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$uid = 0;
$email = '';
$_GET['hash'] = empty($_GET['hash']) ? '' : $_GET['hash'];
if($_GET['hash']) {
	list($uid, $email, $time) = explode("\t", authcode($_GET['hash'], 'DECODE', md5(substr(md5($_G['config']['security']['authkey']), 0, 16))));
	$uid = intval($uid);
}

if($uid && isemail($email) && $time > TIMESTAMP - 86400) {
	$member = getuserbyuid($uid);
	
	$member = array_merge(table_common_member_field_forum::t()->fetch($uid), $member);
	list($dateline, $operation, $idstring) = explode("\t", $member['authstr']);
	if($dateline != $time || $operation != 3 || $idstring != substr(md5($email), 0, 6)) {
		showmessage('email_check_error', 'index.php');
	}

	$setarr = ['email' => $email, 'emailstatus' => '1'];
	if($member['freeze'] == 2) {
		$setarr['freeze'] = 0;
	}
	loaducenter();
	$ucresult = uc_user_edit(addslashes($member['loginname']), '', '', $email, 1);
	if($ucresult == -8) {
		showmessage('email_check_account_invalid', '', [], ['return' => true]);
	} elseif($ucresult == -4) {
		showmessage('profile_email_illegal', '', [], ['return' => true]);
	} elseif($ucresult == -5) {
		showmessage('profile_email_domain_illegal', '', [], ['return' => true]);
	} elseif($ucresult == -6) {
		showmessage('profile_email_duplicate', '', [], ['return' => true]);
	}
	if($_G['setting']['regverify'] == 1 && $member['groupid'] == 8) {
		$membergroup = table_common_usergroup::t()->fetch_by_credits($member['credits']);
		$setarr['groupid'] = $membergroup['groupid'];
	}
	$oldemail = $member['email'];
	updatecreditbyaction('realemail', $uid);
	table_common_member::t()->update($uid, $setarr);
	
	table_common_member_field_forum::t()->update($uid, ['authstr' => '']);
	table_common_member_validate::t()->delete($uid);
	dsetcookie('newemail', '', -1);

	
	if(!function_exists('sendmail')) {
		include libfile('function/mail');
	}
	$reset_email_subject = [
		'tpl' => 'email_reset',
		'var' => [
			'username' => $member['username'],
			'bbname' => $_G['setting']['bbname'],
			'siteurl' => $_G['setting']['securesiteurl'],
			'datetime' => dgmdate(time(), 'Y-m-d H:i:s'),
			'request_datetime' => dgmdate($time, 'Y-m-d H:i:s'),
			'email' => $email,
			'clientip' => $_G['clientip']
		]
	];
	if(!sendmail("{$member['username']} <$oldemail>", $reset_email_subject)) {
		runlog('sendmail', "$oldemail sendmail failed.");
	}

	showmessage('email_check_sucess', 'home.php?mod=spacecp&ac=account', ['email' => $email]);
} else {
	showmessage('email_check_error', 'index.php');
}

