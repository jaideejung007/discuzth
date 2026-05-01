<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!($group_userperm = dunserialize($_G['setting']['group_userperm']))) {
	$group_userperm = [];
}
if(!submitcheck('permsubmit')) {
	shownav('group', 'nav_group_userperm');
	$varname = ['newgroup_userperm', [], 'isfloat'];
	showsubmenu(cplang('nav_group_userperm').' - '.cplang('group_userperm_moderator'));
	/*search={"newgroup_userperm":"action=group&operation=userperm"}*/
	showformheader("group&operation=userperm&id=$id");
	showtableheader();
	$varname[1] = [
		['allowstickthread', cplang('admingroup_edit_stick_thread'), '1'],
		['allowbumpthread', cplang('admingroup_edit_bump_thread'), '1'],
		['allowhighlightthread', cplang('admingroup_edit_highlight_thread'), '1'],
		['allowlivethread', cplang('admingroup_edit_live_thread'), '1'],
		['allowstampthread', cplang('admingroup_edit_stamp_thread'), '1'],
		['allowrepairthread', cplang('admingroup_edit_repair_thread'), '1'],
		['allowrefund', cplang('admingroup_edit_refund'), '1'],
		['alloweditpoll', cplang('admingroup_edit_edit_poll'), '1'],
		['allowremovereward', cplang('admingroup_edit_remove_reward'), '1'],
		['alloweditactivity', cplang('admingroup_edit_edit_activity'), '1'],
		['allowedittrade', cplang('admingroup_edit_edit_trade'), '1'],
	];
	showsetting('admingroup_edit_threadperm', $varname, $group_userperm, 'omcheckbox');

	showsetting('admingroup_edit_digest_thread', ['newgroup_userperm[allowdigestthread]', [
		[0, cplang('admingroup_edit_digest_thread_none')],
		[1, cplang('admingroup_edit_digest_thread_1')],
		[2, cplang('admingroup_edit_digest_thread_2')],
		[3, cplang('admingroup_edit_digest_thread_3')],
	]], $group_userperm['allowdigestthread'], 'mradio');

	$varname[1] = [
		['alloweditpost', cplang('admingroup_edit_edit_post'), '1'],
		['allowwarnpost', cplang('admingroup_edit_warn_post'), '1'],
		['allowbanpost', cplang('admingroup_edit_ban_post'), '1'],
		['allowdelpost', cplang('admingroup_edit_del_post'), '1'],
	];
	showsetting('admingroup_edit_postperm', $varname, $group_userperm, 'omcheckbox');

	$varname[1] = [
		['allowupbanner', cplang('group_userperm_upload_banner'), '1'],
	];
	showsetting('admingroup_edit_modcpperm', $varname, $group_userperm, 'omcheckbox');

	$varname[1] = [
		['disablepostctrl', cplang('admingroup_edit_disable_postctrl'), '1'],
		['allowviewip', cplang('admingroup_edit_view_ip'), '1']
	];
	showsetting('group_userperm_others', $varname, $group_userperm, 'omcheckbox');

	showsubmit('permsubmit', 'submit');
	showtablefooter();
	showformfooter();
	/*search*/
} else {
	$default_perm = ['allowstickthread' => 0, 'allowbumpthread' => 0, 'allowhighlightthread' => 0, 'allowlivethread' => 0, 'allowstampthread' => 0, 'allowclosethread' => 0, 'allowmergethread' => 0, 'allowsplitthread' => 0, 'allowrepairthread' => 0, 'allowrefund' => 0, 'alloweditpoll' => 0, 'allowremovereward' => 0, 'alloweditactivity' => 0, 'allowedittrade' => 0, 'allowdigestthread' => 0, 'alloweditpost' => 0, 'allowwarnpost' => 0, 'allowbanpost' => 0, 'allowdelpost' => 0, 'allowupbanner' => 0, 'disablepostctrl' => 0, 'allowviewip' => 0];
	if(empty($_GET['newgroup_userperm']) || !is_array($_GET['newgroup_userperm'])) {
		$_GET['newgroup_userperm'] = [];
	}
	$_GET['newgroup_userperm'] = array_merge($default_perm, $_GET['newgroup_userperm']);
	if(serialize($_GET['newgroup_userperm']) != serialize($group_userperm)) {
		table_common_setting::t()->update_setting('group_userperm', $_GET['newgroup_userperm']);
		updatecache('setting');
	}
	cpmsg('group_userperm_succeed', 'action=group&operation=userperm', 'succeed');
}
	