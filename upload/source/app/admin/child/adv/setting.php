<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('advsubmit')) {
	$_GET['advexpirationnew']['allow'] = $_GET['advexpirationnew']['allow'] && $_GET['advexpirationnew']['day'] > 0 && $_GET['advexpirationnew']['method'] && $_GET['advexpirationnew']['users'];
	table_common_setting::t()->update_setting('advexpiration', $_GET['advexpirationnew']);
	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=adv&operation=setting', 'succeed');
} else {
	shownav('extended', 'adv_admin');
	showsubmenu('adv_admin', [
		['adv_admin_list', 'adv&operation=list', 0],
		['adv_admin_listall', 'adv&operation=ad', 0],
		['adv_admin_setting', 'adv&operation=setting', 1],
	]);

	$advexpiration = table_common_setting::t()->fetch_setting('advexpiration', true);
	showformheader('adv&operation=setting');
	showtableheader();
	showsetting('adv_setting_advexpiration', 'advexpirationnew[allow]', $advexpiration['allow'], 'radio', 0, 1);
	showsetting('adv_setting_advexpiration_day', 'advexpirationnew[day]', $advexpiration['day'], 'text');
	showsetting('adv_setting_advexpiration_method', ['advexpirationnew[method]', [
		['email', cplang('adv_setting_advexpiration_method_email')],
		['notice', cplang('adv_setting_advexpiration_method_notice')],
	]], $advexpiration['method'], 'mcheckbox');
	showsetting('adv_setting_advexpiration_users', 'advexpirationnew[users]', $advexpiration['users'], 'textarea');
	showtagfooter('tbody');
	showsubmit('advsubmit');
	showtablefooter();
	showformfooter();
}
	