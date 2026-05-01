<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loaducenter();
if(UC_STANDALONE) {
	$ucsetting = uc_get_settings();
}

if(submitcheck('settingsubmit')) {
	if(!preg_match('/^[A-z]\w+?$/', $settingnew['reginput']['username'])) {
		$settingnew['reginput']['username'] = 'username';
	}
	if(!preg_match('/^[A-z]\w+?$/', $settingnew['reginput']['password'])) {
		$settingnew['reginput']['password'] = 'password';
	}
	if(!preg_match('/^[A-z]\w+?$/', $settingnew['reginput']['password2'])) {
		$settingnew['reginput']['password2'] = 'password2';
	}
	if(!preg_match('/^[A-z]\w+?$/', $settingnew['reginput']['email'])) {
		$settingnew['reginput']['email'] = 'email';
	}
	foreach($settingnew['reginput'] as $key => $val) {
		foreach($settingnew['reginput'] as $k => $v) {
			if($key == $k) continue;
			if($val == $v) {
				cpmsg('forum_name_duplicate', '', 'error');
			}
		}
	}

	if((isset($settingnew['postbanperiods']) && isset($settingnew['postmodperiods'])) || (isset($settingnew['visitbanperiods']) && isset($settingnew['attachbanperiods']) && isset($settingnew['searchbanperiods']))) {
		foreach(['visitbanperiods', 'postbanperiods', 'attachbanperiods', 'postmodperiods', 'searchbanperiods'] as $periods) {
			$periodarray = [];
			foreach(explode("\n", $settingnew[$periods]) as $period) {
				if(preg_match('/^\d{1,2}\:\d{2}\-\d{1,2}\:\d{2}$/', $period = trim($period))) {
					$periodarray[] = $period;
				}
			}
			isset($settingnew[$periods]) && $settingnew[$periods] = implode("\r\n", $periodarray);
		}
	}

	if(UC_STANDALONE) {
		uc_set_settings($_GET['ucsettingnew']);
	}
} else {
	shownav('safe', 'setting_sec');

	$_GET['anchor'] = in_array($_GET['anchor'], ['base', 'reginput', 'postperiodtime', 'pm']) ? $_GET['anchor'] : 'base';
	showsubmenuanchors('setting_sec', [
		['setting_sec_base', 'base', $_GET['anchor'] == 'base'],
		['setting_sec_reginput', 'reginput', $_GET['anchor'] == 'reginput'],
		['setting_sec_postperiodtime', 'postperiodtime', $_GET['anchor'] == 'postperiodtime'],
		UC_STANDALONE ? ['uc_setting_pm', 'pm', $_GET['anchor'] == 'pm'] : null,
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$setting['reginput'] = dunserialize($setting['reginput']);

	/*search={"setting_sec":"action=setting&operation=sec","setting_sec_base":"action=setting&operation=sec&anchor=base"}*/
	showtableheader('', 'nobottom', 'id="base"'.($_GET['anchor'] != 'base' ? ' style="display: none"' : ''));
	showsetting('setting_sec_floodctrl', 'settingnew[floodctrl]', $setting['floodctrl'], 'text');
	showsetting('setting_sec_base_need_email', 'settingnew[need_email]', $setting['need_email'], 'radio');
	showsetting('setting_sec_base_need_secmobile', 'settingnew[need_secmobile]', $setting['need_secmobile'], 'radio');
	showsetting('setting_sec_base_need_avatar', 'settingnew[need_avatar]', $setting['need_avatar'], 'radio');
	showsetting('setting_sec_base_change_email', 'settingnew[change_email]', $setting['change_email'], 'radio');
	showsetting('setting_sec_base_change_secmobile', 'settingnew[change_secmobile]', $setting['change_secmobile'], 'radio');
	showsetting('setting_sec_base_need_friendnum', 'settingnew[need_friendnum]', $setting['need_friendnum'], 'text');
	if(UC_STANDALONE) {
		showsetting('uc_setting_login_failedtime', 'ucsettingnew[login_failedtime]', $ucsetting['login_failedtime'], 'text');
	}
	showtablefooter();
	/*search*/

	/*search={"setting_sec":"action=setting&operation=sec","setting_sec_reginput":"action=setting&operation=sec&anchor=reginput"}*/
	showtableheader('setting_sec_reginput', 'nobottom', 'id="reginput"'.($_GET['anchor'] != 'reginput' ? ' style="display: none"' : ''));
	showsetting('setting_sec_reginput_username', 'settingnew[reginput][username]', $setting['reginput']['username'], 'text');
	showsetting('setting_sec_reginput_password', 'settingnew[reginput][password]', $setting['reginput']['password'], 'text');
	showsetting('setting_sec_reginput_password2', 'settingnew[reginput][password2]', $setting['reginput']['password2'], 'text');
	showsetting('setting_sec_reginput_email', 'settingnew[reginput][email]', $setting['reginput']['email'], 'text');
	showtablefooter();
	/*search*/

	/*search={"setting_sec":"action=setting&operation=sec","setting_sec_reginput":"action=setting&operation=sec&anchor=postperiodtime"}*/
	showtableheader('setting_sec_postperiodtime', 'nobottom', 'id="postperiodtime"'.($_GET['anchor'] != 'postperiodtime' ? ' style="display: none"' : ''));
	showsetting('setting_datetime_postbanperiods', 'settingnew[postbanperiods]', $setting['postbanperiods'], 'textarea');
	showsetting('setting_datetime_postmodperiods', 'settingnew[postmodperiods]', $setting['postmodperiods'], 'textarea');
	showsetting('setting_datetime_postignorearea', 'settingnew[postignorearea]', $setting['postignorearea'], 'textarea');
	showsetting('setting_datetime_postignoreip', 'settingnew[postignoreip]', $setting['postignoreip'], 'textarea');
	showtablefooter();
	/*search*/
	showtableheader();

	if(UC_STANDALONE) {
		/*search={"setting_sec":"action=setting&operation=sec","uc_setting_pm":"action=setting&operation=sec&anchor=pm"}*/
		showtableheader('', 'nobottom', 'id="pm"'.($_GET['anchor'] != 'pm' ? ' style="display: none"' : ''));
		showsetting('uc_setting_pmsendregdays', 'ucsettingnew[pmsendregdays]', $ucsetting['pmsendregdays'], 'text');
		showsetting('uc_setting_login_privatepmthreadlimit', 'ucsettingnew[privatepmthreadlimit]', $ucsetting['privatepmthreadlimit'], 'text');
		showsetting('uc_setting_chatpmthreadlimit', 'ucsettingnew[chatpmthreadlimit]', $ucsetting['chatpmthreadlimit'], 'text');
		showsetting('uc_setting_chatpmmemberlimit', 'ucsettingnew[chatpmmemberlimit]', $ucsetting['chatpmmemberlimit'], 'text');
		showsetting('uc_setting_pmfloodctrl', 'ucsettingnew[pmfloodctrl]', $ucsetting['pmfloodctrl'], 'text');
		showtablefooter();
		/*search*/
	}

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}
