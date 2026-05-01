<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('submit')) {
	$appkeyarr = [
		'portal' => $navs[1]['navname'],
		'forum' => $navs[2]['navname'],
		'group' => $navs[3]['navname'],
		'home' => $lang['nav_home'],
		'mobile' => $lang['mobile'],
		'default' => $lang['default']
	];
	/*search={"setting_domain":"action=domain","setting_domain_app":"domain&operation=app"}*/
	showtips('setting_domain_app_tips');
	/*search*/

	showformheader('domain&operation=app');
	showboxheader();
	showtableheader();
	showsubtitle(['name', 'setting_domain_app_domain']);
	$app = [];
	$hiddenarr = [];
	foreach($appkeyarr as $key => $desc) {
		if(in_array($key, ['portal', 'group']) && !helper_access::check_module($key) || ($key == 'home' && !helper_access::check_module('feed'))) {
			$hiddenarr["appnew[$key]"] = '';
		} else {
			showtablerow('', ['class="td25"', ''], [
				$desc,
				"<input type=\"text\" class=\"txt\" style=\"width:50%;\" name=\"appnew[$key]\" value=\"".$_G['setting']['domain']['app'][$key]."\">".($key == 'mobile' ? cplang('setting_domain_app_mobile_tips') : '')
			]);
		}
	}
	showsubmit('submit');
	showtablefooter();
	showboxfooter();
	showhiddenfields($hiddenarr);
	showformfooter();
} else {
	$olddomain = $_G['setting']['domain']['app'];
	$_G['setting']['domain']['app'] = [];
	$appset = false;
	foreach($_GET['appnew'] as $appkey => $domain) {
		$domain = strtolower($domain);
		if(!empty($domain) && !preg_match('/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/', $domain)) {
			cpmsg('setting_domain_http_error', '', 'error');
		}
		if(!empty($domain) && in_array($domain, $_G['setting']['domain']['app'])) {
			cpmsg('setting_domain_repeat_error', '', 'error');
		}
		if($appkey != 'default' && $domain) {
			$appset = true;
		}
		$_G['setting']['domain']['app'][$appkey] = $domain;
	}
	if($appset && !$_G['setting']['domain']['app']['default']) {
		cpmsg('setting_domain_need_default_error', '', 'error');
	}

	if($_GET['appnew']['mobile'] != $olddomain['mobile']) {
		table_common_nav::t()->update_by_identifier('mobile', ['url' => (!$_GET['appnew']['mobile'] ? 'forum.php?mobile=yes' : $_G['scheme'].'://'.$_GET['appnew']['mobile'])]);
	}

	table_common_setting::t()->update_setting('domain', $_G['setting']['domain']);
	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=domain&operation=app', 'succeed');
}
	