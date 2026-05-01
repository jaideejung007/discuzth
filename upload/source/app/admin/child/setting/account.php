<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	if(empty($settingnew['security_verify'])) {
		$settingnew['security_verify'] = [];
	}
} else {
	shownav('safe', 'setting_account');

	$_GET['anchor'] = in_array($_GET['anchor'], ['base', 'chgusername']) ? $_GET['anchor'] : 'base';
	showsubmenuanchors('setting_account', [
		['setting_account_base', 'base', $_GET['anchor'] == 'base'],
		['setting_account_chgusername', 'chgusername', $_GET['anchor'] == 'chgusername'],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_account":"action=setting&operation=account","setting_account_base":"action=setting&operation=account&anchor=base"}*/
	showtableheader('', 'nobottom', 'id="base"'.($_GET['anchor'] != 'base' ? ' style="display: none"' : ''));
	$security_verify = ['settingnew[security_verify]', [
		['secmobile', $lang['security_verify_mobile']],
		['email', $lang['security_verify_email']],
		['password', $lang['security_verify_password']],
		//array('appeal', $lang['security_verify_appeal']),
	]];
	$setting['security_verify'] = dunserialize($setting['security_verify']);
	showsetting('setting_sec_base_security_verify', $security_verify, $setting['security_verify'], 'mcheckbox', norelatedlink: true);
	showsetting('setting_sec_base_security_mobile', 'settingnew[security_mobile]', $setting['security_mobile'], 'radio');
	showsetting('setting_sec_base_security_email', 'settingnew[security_email]', $setting['security_email'], 'radio');
	showsetting('setting_sec_base_security_password', 'settingnew[security_password]', $setting['security_password'], 'radio');
	showsetting('setting_sec_base_security_rename', 'settingnew[security_rename]', $setting['security_rename'], 'radio');
	showsetting('setting_sec_base_security_question', 'settingnew[security_question]', $setting['security_question'], 'radio');
	showtablefooter();
	/*search*/

	/*search={"setting_account":"action=setting&operation=chgusername","setting_account_chgusername":"action=setting&operation=account&anchor=chgusername"}*/
	$groups_chgusername = [0 => 'settingnew[chgusername][credits_unlimit_group][]'];
	foreach(table_common_usergroup::t()->fetch_all_by_type() as $group) {
		$groups_chgusername[1][] = [$group['groupid'], $group['grouptitle']];
	}
	$setting['chgusername'] = dunserialize($setting['chgusername']);
	showtableheader('setting_account_chgusername', 'nobottom', 'id="chgusername"'.($_GET['anchor'] != 'chgusername' ? ' style="display: none"' : ''));
	showsetting('chgusername_max_times', 'settingnew[chgusername][max_times]', $setting['chgusername']['max_times'] ? $setting['chgusername']['max_times'] : 0, 'text');
	showsetting('chgusername_credits_threshold', 'settingnew[chgusername][credits_threshold]', $setting['chgusername']['credits_threshold'] ? $setting['chgusername']['credits_threshold'] : 0, 'text');
	showsetting('chgusername_credits_unlimit_group', $groups_chgusername, $setting['chgusername']['credits_unlimit_group'], 'mselect');
	showsetting('chgusername_credits_pay', 'settingnew[chgusername][credits_pay]', $setting['chgusername']['credits_pay'] ? $setting['chgusername']['credits_pay'] : 0, 'text');
	showtablefooter();
	/*search*/

	showtableheader();
	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}