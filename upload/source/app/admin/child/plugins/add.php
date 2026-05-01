<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$isplugindeveloper) {
	cpmsg('undefined_action', '', 'error');
}

if(!submitcheck('addsubmit')) {
	shownav('plugin');
	showsubmenu('nav_plugins', [
		['plugins_add', 'plugins&operation=add', 1],
		['cloudaddons_plugin_link', 'cloudaddons&frame=no&operation=plugins&from=more', 0, 1],
	]);
	showtips('plugins_add_tips');

	showformheader('plugins&operation=add', '', 'configform');
	showtableheader();
	showsetting('plugins_edit_name', 'namenew', '', 'text');
	showsetting('plugins_edit_version', 'versionnew', '', 'text');
	showsetting('plugins_edit_copyright', 'copyrightnew', '', 'text');
	showsetting('plugins_edit_identifier', 'identifiernew', '', 'text');
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();
} else {
	$namenew = dhtmlspecialchars(trim($_GET['namenew']));
	$versionnew = strip_tags(trim($_GET['versionnew']));
	$identifiernew = trim($_GET['identifiernew']);
	$copyrightnew = dhtmlspecialchars($_GET['copyrightnew']);

	if(!$namenew) {
		cpmsg('plugins_edit_name_invalid', '', 'error');
	} else {
		if(!ispluginkey($identifiernew) || table_common_plugin::t()->fetch_by_identifier($identifiernew)) {
			cpmsg('plugins_edit_identifier_invalid', '', 'error');
		}
	}
	$data = [
		'name' => $namenew,
		'version' => $versionnew,
		'identifier' => $identifiernew,
		'directory' => $identifiernew.'/',
		'available' => 0,
		'copyright' => $copyrightnew,
	];
	$pluginid = table_common_plugin::t()->insert($data, true);
	dmkdir(DISCUZ_PLUGIN($identifiernew).'/');
	updatecache(['plugin', 'setting', 'styles']);
	cleartemplatecache();
	cpmsg('plugins_add_succeed', "action=plugins&operation=edit&pluginid=$pluginid", 'succeed');
}
	