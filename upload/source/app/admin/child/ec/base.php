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
	$settingnew = $_GET['settingnew'];

	if($settingnew['ec_ratio']) {
		if($settingnew['ec_ratio'] < 0) {
			cpmsg('alipay_ratio_invalid', '', 'error');
		}
	} else {
		$settingnew['ec_mincredits'] = $settingnew['ec_maxcredits'] = 0;
	}
	foreach(['ec_ratio', 'ec_mincredits', 'ec_maxcredits', 'ec_maxcreditspermonth'] as $key) {
		$settingnew[$key] = intval($settingnew[$key]);
	}

	$settings = [];
	foreach($settingnew as $key => $val) {
		$settings[$key] = $val;
	}
	if($settings) {
		table_common_setting::t()->update_batch($settings);
		updatecache('setting');
	}

	cpmsg('setting_update_succeed', 'action=ec&operation=base', 'succeed');
} else {

	$setting = table_common_setting::t()->fetch_all_setting(['ec_ratio', 'ec_mincredits', 'ec_maxcredits', 'ec_maxcreditspermonth']);

	showformheader('ec&operation=base', 'enctype');

	/*search={"nav_ec":"action=ec&operation=base","nav_ec_config":"action=ec&operation=base"}*/
	showtableheader();
	showtitle('setting_ec_credittrade');
	showsetting('setting_ec_ratio', 'settingnew[ec_ratio]', $setting['ec_ratio'], 'text', norelatedlink: true);
	showsetting('setting_ec_mincredits', 'settingnew[ec_mincredits]', $setting['ec_mincredits'], 'text');
	showsetting('setting_ec_maxcredits', 'settingnew[ec_maxcredits]', $setting['ec_maxcredits'], 'text');
	showsetting('setting_ec_maxcreditspermonth', 'settingnew[ec_maxcreditspermonth]', $setting['ec_maxcreditspermonth'], 'text');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();

}