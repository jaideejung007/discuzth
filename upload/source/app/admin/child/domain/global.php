<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('domainsubmit')) {

	/*search={"setting_domain":"action=domain","setting_domain_base":"domain&operation=base"}*/
	showtips('setting_domain_base_tips');
	showformheader('domain');
	showtableheader();
	if($_G['setting']['homepagestyle']) {
		showsetting('setting_domain_allow_space', 'settingnew[allowspacedomain]', $_G['setting']['allowspacedomain'], 'radio');
	} else {
		showhiddenfields(['settingnew[allowspacedomain]' => 0]);
	}
	if(helper_access::check_module('group')) {
		showsetting('setting_domain_allow_group', 'settingnew[allowgroupdomain]', $_G['setting']['allowgroupdomain'], 'radio');
	} else {
		showhiddenfields(['settingnew[allowgroupdomain]' => 0]);
	}
	showsetting('setting_domain_hold_domain', 'settingnew[holddomain]', $_G['setting']['holddomain'], 'text');
	showsubmit('domainsubmit');
	showtablefooter();
	showformfooter();
	/*search*/
} else {

	$settings = $_GET['settingnew'];
	$settings['allowspacedomain'] = (float)$settings['allowspacedomain'];
	$settings['allowgroupdomain'] = (float)$settings['allowgroupdomain'];
	if($settings) {
		table_common_setting::t()->update_batch($settings);
		updatecache('setting');

	}
	cpmsg('setting_update_succeed', 'action=domain', 'succeed');
}
	