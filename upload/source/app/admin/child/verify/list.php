<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('user', 'nav_members_verify');
showsubmenu('members_verify_setting');
if(!submitcheck('verifysubmit')) {
	showformheader('verify');
	showtableheader('members_verify_setting', 'fixpadding');
	showsubtitle(['members_verify_available', 'members_verify_id', 'members_verify_title', ''], 'header');
	for($i = 1; $i < 7; $i++) {
		$readonly = $i == 6;
		$url = parse_url($_G['setting']['verify'][$i]['icon']);
		if(!$url['host'] && $_G['setting']['verify'][$i]['icon'] && !str_contains($_G['setting']['verify'][$i]['icon'], $_G['setting']['attachurl'].'common/')) {
			$_G['setting']['verify'][$i]['icon'] = $_G['setting']['attachurl'].'common/'.$_G['setting']['verify'][$i]['icon'];
		}
		showtablerow('', ['class="td25"', '', '', 'class="td25"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[verify][$i][available]\" value=\"1\" ".($_G['setting']['verify'][$i]['available'] ? 'checked' : '').' />',
			'verify'.$i,
			($readonly ? $_G['setting']['verify'][$i]['title']."<input type=\"hidden\" name=\"settingnew[verify][$i][title]\" value=\"{$_G['setting']['verify'][$i]['title']}\" readonly>&nbsp;" : "<input type=\"text\" class=\"txt\" size=\"8\" name=\"settingnew[verify][$i][title]\" value=\"{$_G['setting']['verify'][$i]['title']}\">").
			($_G['setting']['verify'][$i]['icon'] ? '<img src="'.$_G['setting']['verify'][$i]['icon'].'" />' : ''),
			"<a href=\"".ADMINSCRIPT."?action=verify&operation=edit&anchor=base&vid=$i\">".$lang['edit'].'</a>'
		]);
	}
	showsubmit('verifysubmit');
	showtablefooter();
	showformfooter();

} else {
	$settingnew = getgpc('settingnew');
	$enabled = false;
	foreach($settingnew['verify'] as $key => $value) {
		if($value['available'] && !$value['title']) {
			cpmsg('members_verify_title_invalid', '', 'error');
		}
		if($value['available']) {
			$enabled = true;
		}
		$_G['setting']['verify'][$key]['available'] = intval($value['available']);
		$_G['setting']['verify'][$key]['title'] = $value['title'];
	}
	$_G['setting']['verify']['enabled'] = $enabled;
	table_common_setting::t()->update_setting('verify', $_G['setting']['verify']);
	updatecache(['setting']);
	updatemenu('user');
	cpmsg('members_verify_update_succeed', 'action=verify', 'succeed');
}
	