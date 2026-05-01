<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$setting = &$_G['setting'];
if(!($group_creditspolicy = dunserialize($setting['group_creditspolicy']))) {
	$group_creditspolicy = [];
}
if(!($group_admingroupids = dunserialize($setting['group_admingroupids']))) {
	$group_admingroupids = [];
}
$setting['group_recommend'] = $setting['group_recommend'] ? implode(',', array_keys(dunserialize($setting['group_recommend']))) : '';
if(!($group_postpolicy = dunserialize($setting['group_postpolicy']))) {
	$group_postpolicy = [];
}
if($group_postpolicy['autoclose']) {
	$group_postpolicy['autoclosetime'] = abs($group_postpolicy['autoclose']);
	$group_postpolicy['autoclose'] = $group_postpolicy['autoclose'] / abs($group_postpolicy['autoclose']);
}
if(!submitcheck('updategroupsetting')) {
	shownav('group', 'nav_group_setting');
	showsubmenu('nav_group_setting');
	/*search={"nav_group_setting":"action=group&operation=setting"}*/
	showformheader('group&operation=setting');
	showtableheader();
	showtitle('groups_setting_basic');
	showsetting('groups_setting_basic_mod', 'settingnew[groupmod]', $setting['groupmod'], 'radio');
	showcomponent('groups_setting_basic_iconsize', 'settingnew[group_imgsizelimit]', $setting['group_imgsizelimit'], 'component_size');
	showsetting('groups_setting_basic_recommend', 'settingnew[group_recommend]', $setting['group_recommend'], 'text');
	showtitle('groups_setting_admingroup');
	$varname = ['newgroup_admingroupids', [], 'isfloat'];
	$query = table_common_usergroup::t()->fetch_all_by_radminid([1, 2], '=', 'groupid');
	foreach($query as $ugroup) {
		$varname[1][] = [$ugroup['groupid'], $ugroup['grouptitle'], '1'];
	}
	showsetting('', $varname, $group_admingroupids, 'omcheckbox');
	showsetting('forums_edit_posts_allowfeed', 'settingnew[group_allowfeed]', $setting['group_allowfeed'], 'radio');

	showsubmit('updategroupsetting');
	showtablefooter();
	showformfooter();
	/*search*/
} else {

	require_once libfile('function/group');
	$settings = [];
	$settings['group_recommend'] = cacherecommend($_GET['settingnew']['group_recommend']);
	require_once libfile('function/discuzcode');
	$skey_array = ['group_imgsizelimit', 'group_allowfeed', 'groupmod'];
	foreach($_GET['settingnew'] as $skey => $svalue) {
		if(in_array($skey, $skey_array)) {
			$settings[$skey] = intval($svalue);
		}
	}

	$settings['group_admingroupids'] = $_GET['newgroup_admingroupids'];
	$descriptionnew = preg_replace('/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i', '', $_GET['descriptionnew']);
	$keywordsnew = $_GET['keywordsnew'];
	$settings['group_description'] = $descriptionnew;
	$settings['group_keywords'] = $keywordsnew;
	table_common_setting::t()->update_batch($settings);

	updatecache('setting');
	cpmsg('groups_setting_succeed', 'action=group&operation=setting', 'succeed');
}
	