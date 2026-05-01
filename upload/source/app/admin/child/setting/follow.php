<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('settingsubmit')) {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = 'base';
	showsubmenuanchors('setting_follow', [
		['setting_follow_base', 'base', true]
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	require_once libfile('function/forumlist');
	/*search={"setting_follow":"action=setting&operation=follow","setting_follow_base":"action=setting&operation=follow&anchor=base"}*/
	showtableheader('', 'nobottom', 'id="base"'.($_GET['anchor'] != 'base' ? ' style="display: none"' : ''));
	showsetting('setting_follow_base_default_follow_retain_day', 'settingnew[followretainday]', $setting['followretainday'], 'text');
	showsetting('setting_follow_base_default_follow_add_notice', 'settingnew[followaddnotice]', $setting['followaddnotice'], 'radio');
	showsetting('setting_follow_base_default_view_profile', 'settingnew[allowquickviewprofile]', $setting['allowquickviewprofile'], 'radio');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}