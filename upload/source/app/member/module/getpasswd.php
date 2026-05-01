<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

const NOROBOT = true;

$paramexist = preg_match_all('/(\?|&(amp)?(;)?)(.+?)=([^&?]*)/i', $_SERVER['QUERY_STRING'], $parammatchs);
if($paramexist) {
	foreach($parammatchs[5] as $paramk => $paramv) {
		$param[$parammatchs[4][$paramk]] = $paramv;
	}
}
$uid = $_GET['uid'] ?? $param['uid'];
$id = $_GET['id'] ?? $param['id'];
$sign = $_GET['sign'] ?? $param['sign'];

if($uid && $id && $sign === make_getpws_sign($uid, $id)) {

	$discuz_action = 141;


	$member = getuserbyuid($uid, 1);
	$table_ext = isset($member['_inarchive']) ? '_archive' : '';
	$member = array_merge(C::t('common_member_field_forum'.$table_ext)->fetch($uid), $member);
	[$dateline, $operation, $idstring] = explode("\t", $member['authstr']);

	if($dateline < TIMESTAMP - 86400 * 3 || $operation != 1 || $idstring != $id) {
		showmessage('getpasswd_illegal', NULL);
	}

	if(!submitcheck('getpwsubmit') || $_GET['newpasswd1'] != $_GET['newpasswd2']) {
		$navtitle = lang('core', 'title_getpasswd');
		$hashid = $id;
		$uid = $_GET['uid'];
		include template('member/getpasswd');
	} else {
		if($_GET['newpasswd1'] != addslashes($_GET['newpasswd1'])) {
			showmessage('profile_passwd_illegal');
		}
		if($_G['setting']['pwlength']) {
			if(strlen($_GET['newpasswd1']) < $_G['setting']['pwlength']) {
				showmessage('profile_password_tooshort', '', ['pwlength' => $_G['setting']['pwlength']]);
			}
		}
		if($_G['setting']['strongpw']) {
			$strongpw_str = [];
			if(in_array(1, $_G['setting']['strongpw']) && !preg_match('/\d+/', $_GET['newpasswd1'])) {
				$strongpw_str[] = lang('member/template', 'strongpw_1');
			}
			if(in_array(2, $_G['setting']['strongpw']) && !preg_match('/[a-z]+/', $_GET['newpasswd1'])) {
				$strongpw_str[] = lang('member/template', 'strongpw_2');
			}
			if(in_array(3, $_G['setting']['strongpw']) && !preg_match('/[A-Z]+/', $_GET['newpasswd1'])) {
				$strongpw_str[] = lang('member/template', 'strongpw_3');
			}
			if(in_array(4, $_G['setting']['strongpw']) && !preg_match('/[^a-zA-z0-9]+/', $_GET['newpasswd1'])) {
				$strongpw_str[] = lang('member/template', 'strongpw_4');
			}
			if($strongpw_str) {
				showmessage(lang('member/template', 'password_weak').implode(',', $strongpw_str));
			}
		}
		loaducenter();
		uc_user_edit(addslashes($member['loginname']), $_GET['newpasswd1'], $_GET['newpasswd1'], addslashes($member['email']), 1, 0);
		$password = md5(random(10));

		if(isset($member['_inarchive'])) {
			table_common_member_archive::t()->move_to_master($member['uid']);
		}
		table_common_member::t()->update($uid, ['password' => $password]);
		table_common_member_field_forum::t()->update($uid, ['authstr' => '']);

		
		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}
		$reset_password_subject = [
			'tpl' => 'password_reset',
			'var' => [
				'username' => $member['username'],
				'bbname' => $_G['setting']['bbname'],
				'siteurl' => $_G['setting']['securesiteurl'],
				'datetime' => dgmdate(time(), 'Y-m-d H:i:s'),
				'clientip' => $_G['clientip']
			]
		];
		if(!sendmail("{$member['username']} <{$member['email']}>", $reset_password_subject)) {
			runlog('sendmail', "{$member['email']} sendmail failed.");
		}

		showmessage('getpasswd_succeed', 'index.php', [], ['login' => 1]);
	}

} else {
	showmessage('parameters_error');
}
