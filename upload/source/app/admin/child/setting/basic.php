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
	$nohtmlarray = ['bbname', 'regname', 'reglinkname', 'icp', 'sitemessage', 'site_qq'];
	foreach($nohtmlarray as $k) {
		if(isset($settingnew[$k])) {
			$settingnew[$k] = dhtmlspecialchars($settingnew[$k]);
		}
	}
	if(isset($settingnew['statcode'])) {
		$settingnew['statcode'] = preg_replace('/language\s*=[\s|\'|\"]*php/is', '_', $settingnew['statcode']);
		$settingnew['statcode'] = str_replace(['<?', '?>'], ['&lt;?', '?&gt;'], $settingnew['statcode']);
	}

	if(isset($settingnew['bbclosed']) && $settingnew['bbclosed'] == 0) {
		if(isset($setting['memberspliting'])) {
			table_common_member::t()->switch_keys('enable');
		}
	}
} else {
	shownav('global', 'setting_'.$operation);

	showsubmenu('setting_'.$operation);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_basic":"action=setting&operation=basic"}*/
	showtableheader('', 'nobottom');
	showsetting('setting_basic_bbname', 'settingnew[bbname]', $setting['bbname'], 'text');
	showsetting('setting_basic_sitename', 'settingnew[sitename]', $setting['sitename'], 'text');
	showsetting('setting_basic_siteurl', 'settingnew[siteurl]', $setting['siteurl'], 'text');
	showsetting('setting_basic_adminemail', 'settingnew[adminemail]', $setting['adminemail'], 'text');
	showsetting('setting_basic_site_qq', 'settingnew[site_qq]', $setting['site_qq'], 'text', $disabled = '', $hidden = 0, $comment = '', $extra = 'id="settingnew[site_qq]"');
	showsetting('setting_basic_icp', 'settingnew[icp]', $setting['icp'], 'text');
	showsetting('setting_basic_mps', 'settingnew[mps]', $setting['mps'], 'text');
	showsetting('setting_basic_boardlicensed', 'settingnew[boardlicensed]', $setting['boardlicensed'], 'radio');
	showsetting('setting_basic_stat', 'settingnew[statcode]', $setting['statcode'], 'textarea');
	showtablefooter();

	showtableheader('setting_basic_bbclosed', 'nobottom');
	showsetting('setting_basic_bbclosed', 'settingnew[bbclosed]', $setting['bbclosed'], 'radio', 0, 1);
	showsetting('setting_basic_closedreason', 'settingnew[closedreason]', $setting['closedreason'], 'textarea');
	showsetting('setting_basic_bbclosed_activation', 'settingnew[closedallowactivation]', $setting['closedallowactivation'], 'radio');
	showtagfooter('tbody');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}