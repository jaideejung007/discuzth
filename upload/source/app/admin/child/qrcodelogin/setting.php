<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!isfounder()) {
	cpmsg('undefined_action');
}

if(!submitcheck('submit')) {
	showformheader('qrcodelogin&operation=setting&do=submit');
	showtableheader();
	showsetting('qrcodelogin_close', 'admin_qrlogin_close', $_G['setting']['admin_qrlogin_close'], 'radio');
	showsetting('qrcodelogin_clear', 'admin_qrlogin_clear', 0, 'radio');
	showsubmit('submit');
	showtablefooter();
	showformfooter();
} else {
	if(!empty($_GET['admin_qrlogin_clear'])) {
		$data = requestLoginApi('clear');
		if($data['errCode'] != 0) {
			cpmsg(sprintf(cplang('qrcodelogin_api_error'), $data['errCode']));
		}
	}
	$settings = ['admin_qrlogin_close' => $_GET['admin_qrlogin_close']];
	table_common_setting::t()->update_batch($settings);
	updatecache('setting');

	cpmsg('setting_update_succeed', 'action=qrcodelogin&operation=setting', 'succeed');
}