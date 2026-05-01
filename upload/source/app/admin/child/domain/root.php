<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$roottype = [
	'home' => $lang['domain_home'],
	'group' => $navs[3]['navname'],
	'forum' => $lang['domain_forum'],
	'topic' => $lang['domain_topic'],
	'channel' => $lang['channel'],
];
if(!submitcheck('submit')) {
	/*search={"setting_domain":"action=domain","setting_domain_root":"domain&operation=root"}*/
	showtips('setting_domain_root_tips');
	/*search*/
	showformheader('domain&operation=root');
	showboxheader();
	showtableheader();
	showsubtitle(['name', 'setting_domain_app_domain']);
	$hiddenarr = [];
	foreach($roottype as $type => $desc) {
		if(in_array($type, ['topic', 'channel']) && !helper_access::check_module('portal') || ($type == 'home' && !$_G['setting']['homepagestyle']) || ($type == 'group' && !helper_access::check_module('group'))) {
			$hiddenarr["domainnew[$type]"] = '';
		} else {
			$domainroot = $_G['setting']['domain']['root'][$type];
			showtablerow('', ['class="td25"', ''], [
				$desc,
				"<input type=\"text\" class=\"txt\" style=\"width:50%;\" name=\"domainnew[$type]\" value=\"$domainroot\">"
			]);
		}
	}
	showsubmit('submit');
	showtablefooter();
	showboxfooter();
	showhiddenfields($hiddenarr);
	showformfooter();
} else {
	$oldroot = $_G['setting']['domain']['root'];
	$_G['setting']['domain']['root'] = [];
	foreach($_GET['domainnew'] as $idtype => $domain) {
		$domain = strtolower($domain);
		if(!empty($domain) && !preg_match('/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/', $domain)) {
			cpmsg('setting_domain_http_error', '', 'error');
		}
		if($_G['setting']['domain']['root'][$idtype] != $domain) {
			$updatetype = $idtype == 'forum' ? ['forum', 'channel'] : $idtype;
			table_common_domain::t()->update_by_idtype($updatetype, ['domainroot' => $domain]);
		}
		$_G['setting']['domain']['root'][$idtype] = $domain;

	}
	table_common_setting::t()->update_setting('domain', $_G['setting']['domain']);
	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=domain&operation=root', 'succeed');
}
	