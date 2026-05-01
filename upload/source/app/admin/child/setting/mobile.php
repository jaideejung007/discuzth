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
	$settingnew['mobile_arr']['allowmobile'] = intval($settingnew['mobile']['allowmobile']);
	if(!$settingnew['mobile_arr']['allowmobile']) {
		table_common_nav::t()->update_by_navtype_type_identifier(1, 0, 'mobile', ['available' => 0]);
	} else {
		table_common_nav::t()->update_by_navtype_type_identifier(1, 0, 'mobile', ['available' => 1]);
	}
	$settingnew['mobile_arr']['allowmnew'] = 0;
	$settingnew['mobile_arr']['mobileforward'] = intval($settingnew['mobile']['mobileforward']);
	$settingnew['mobile_arr']['mobileregister'] = intval($settingnew['mobile']['mobileregister']);
	$settingnew['mobile_arr']['mobileseccode'] = intval($settingnew['mobile']['mobileseccode']);
	$settingnew['mobile_arr']['mobilesimpletype'] = intval($settingnew['mobile']['mobilesimpletype']);
	$settingnew['mobile_arr']['mobilecachetime'] = intval($settingnew['mobile']['mobilecachetime']);
	$settingnew['mobile_arr']['mobilecomefrom'] = preg_replace(["/\son(.*)=[\'\"](.*?)[\'\"]/i"], '', strip_tags($settingnew['mobile']['mobilecomefrom'], '<a><font><img><span><strong><b>'));
	$settingnew['mobile_arr']['mobilepreview'] = intval($settingnew['mobile']['mobilepreview']);
	$settingnew['mobile_arr']['legacy'] = 0;
	$settingnew['mobile_arr']['wml'] = 0;

	$settingnew['mobile_arr']['portal']['catnav'] = intval($settingnew['mobile']['portal']['catnav']);
	$settingnew['mobile_arr']['portal']['wzpicture'] = intval($settingnew['mobile']['portal']['wzpicture']);
	$settingnew['mobile_arr']['portal']['wzlist'] = intval($settingnew['mobile']['portal']['wzlist']);

	$settingnew['mobile_arr']['forum']['index'] = intval($settingnew['mobile']['forum']['index']);
	$settingnew['mobile_arr']['forum']['statshow'] = intval($settingnew['mobile']['forum']['statshow']);
	$settingnew['mobile_arr']['forum']['displayorder3'] = intval($settingnew['mobile']['forum']['displayorder3']);
	$settingnew['mobile_arr']['forum']['topicperpage'] = intval($settingnew['mobile']['forum']['topicperpage']) > 0 ? intval($settingnew['mobile']['forum']['topicperpage']) : 1;
	$settingnew['mobile_arr']['forum']['postperpage'] = intval($settingnew['mobile']['forum']['postperpage']) > 0 ? intval($settingnew['mobile']['forum']['postperpage']) : 1;
	$settingnew['mobile_arr']['forum']['forumview'] = intval($settingnew['mobile']['forum']['forumview']);
	$settingnew['mobile_arr']['forum']['iconautowidth'] = intval($settingnew['mobile']['forum']['iconautowidth']);

	$settingnew['mobile'] = $settingnew['mobile_arr'];
	unset($settingnew['mobile_arr']);
} else {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['status', 'portal', 'forum']) ? $_GET['anchor'] : 'status';
	showsubmenuanchors('setting_mobile', [
		['setting_mobile_status', 'status', $_GET['anchor'] == 'status'],
		['setting_mobile_portal', 'portal', $_GET['anchor'] == 'portal'],
		['setting_mobile_forum', 'forum', $_GET['anchor'] == 'forum']
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_mobile":"action=setting&operation=mobile","setting_mobile_status":"action=setting&operation=mobile&anchor=status"}*/
	$setting['mobile'] = dunserialize($setting['mobile']);
	showtips('setting_mobile_status_tips');
	showtableheader('setting_mobile_status', '', 'id="status"'.($_GET['anchor'] != 'status' ? ' style="display: none"' : ''));
	showsetting('setting_mobile_allowmobile', ['settingnew[mobile][allowmobile]', [
		[1, $lang['yes'], ['mobileext' => '']],
		[0, $lang['no'], ['mobileext' => 'none']]
	], TRUE], $setting['mobile']['allowmobile'] ? $setting['mobile']['allowmobile'] : 0, 'mradio');
	showtagheader('tbody', 'mobileext', $setting['mobile']['allowmobile'], 'sub');
	showsetting('setting_mobile_mobileforward', 'settingnew[mobile][mobileforward]', $setting['mobile']['mobileforward'], 'radio');
	showsetting('setting_mobile_register', 'settingnew[mobile][mobileregister]', $setting['mobile']['mobileregister'], 'radio');
	showsetting('setting_mobile_simpletype', 'settingnew[mobile][mobilesimpletype]', $setting['mobile']['mobilesimpletype'], 'radio');
	showsetting('setting_mobile_cachetime', 'settingnew[mobile][mobilecachetime]', $setting['mobile']['mobilecachetime'] ? $setting['mobile']['mobilecachetime'] : 0, 'text');
	showsetting('setting_mobile_come_from', 'settingnew[mobile][mobilecomefrom]', $setting['mobile']['mobilecomefrom'], 'textarea');
	showtagfooter('tbody');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	/*search={"setting_mobile":"action=setting&operation=mobile","setting_mobile_portal":"action=setting&operation=mobile&anchor=portal"}*/
	showtableheader('setting_mobile_portal', '', 'id="portal"'.($_GET['anchor'] != 'portal' ? ' style="display: none"' : ''));
	showsetting('setting_mobile_portal_catnav', 'settingnew[mobile][portal][catnav]', $setting['mobile']['portal']['catnav'], 'radio');
	showsetting('setting_mobile_portal_wzpicture', 'settingnew[mobile][portal][wzpicture]', $setting['mobile']['portal']['wzpicture'], 'radio');
	showsetting('setting_mobile_portal_wzlist', 'settingnew[mobile][portal][wzlist]', $setting['mobile']['portal']['wzlist'], 'radio');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	/*search={"setting_mobile":"action=setting&operation=mobile","setting_mobile_forum":"action=setting&operation=mobile&anchor=forum"}*/
	showtableheader('setting_mobile_forum', '', 'id="forum"'.($_GET['anchor'] != 'forum' ? ' style="display: none"' : ''));
	showsetting('setting_mobile_forum_forumindex', ['settingnew[mobile][forum][index]', [
		[1, $lang['setting_mobile_forum_forumindex_guide']],
		[2, $lang['setting_mobile_forum_forumindex_grid']],
		[0, $lang['setting_mobile_forum_forumindex_forumlist']],
		[3, $lang['setting_mobile_forum_forumindex_forumgrid']],
	]], $setting['mobile']['forum']['index'] ? $setting['mobile']['forum']['index'] : 0, 'mradio');
	showsetting('setting_mobile_forum_statshow', 'settingnew[mobile][forum][statshow]', $setting['mobile']['forum']['statshow'], 'radio');
	showsetting('setting_mobile_forum_displayorder3', 'settingnew[mobile][forum][displayorder3]', $setting['mobile']['forum']['displayorder3'], 'radio');
	showsetting('setting_mobile_forum_topicperpage', 'settingnew[mobile][forum][topicperpage]', $setting['mobile']['forum']['topicperpage'] ? $setting['mobile']['forum']['topicperpage'] : 10, 'text');
	showsetting('setting_mobile_forum_postperpage', 'settingnew[mobile][forum][postperpage]', $setting['mobile']['forum']['postperpage'] ? $setting['mobile']['forum']['postperpage'] : 5, 'text');
	showsetting('setting_mobile_forum_forumview', ['settingnew[mobile][forum][forumview]', [
		[1, $lang['pack']],
		[0, $lang['unwind']]
	]], $setting['mobile']['forum']['forumview'] ? $setting['mobile']['forum']['forumview'] : 0, 'mradio');
	showsetting('setting_mobile_forum_iconautowidth', 'settingnew[mobile][forum][iconautowidth]', $setting['mobile']['forum']['iconautowidth'], 'radio');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/
}