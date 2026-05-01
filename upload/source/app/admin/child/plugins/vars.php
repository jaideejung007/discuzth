<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$plugin = table_common_plugin::t()->fetch($pluginid);
if(!$plugin) {
	cpmsg('plugin_not_found', '', 'error');
}

$pluginvarid = $_GET['pluginvarid'];
$pluginvar = table_common_plugin::t()->fetch_by_pluginvarid($pluginid, $pluginvarid);
if(!$pluginvar) {
	cpmsg('pluginvar_not_found', '', 'error');
}

if(!submitcheck('varsubmit')) {
	shownav('plugin');

	showchildmenu([['nav_plugins', 'plugins'], [$plugin['name'].($plugin['available'] ? cplang('plugins_edit_available') : ' '), ' '],
		[cplang('plugins_editlink'), 'plugins&operation=edit&pluginid='.$pluginid.'&anchor=vars']],
		$pluginvar['title']);

	$typeselect = '<select name="typenew" onchange="if(this.value.indexOf(\'select\') != -1) $(\'extra\').style.display=\'\'; else $(\'extra\').style.display=\'none\';">';
	$typeselect .= !str_starts_with($pluginvar['type'], 'style') ? admin\class_component::get_optgroup($pluginvar['type']) : '<option value="'.$pluginvar['type'].'" selected>'.cplang('plugins_edit_vars_type_'.$pluginvar['type']).'</option>';
	$typeselect .= '</select>';

	showformheader("plugins&operation=vars&pluginid=$pluginid&pluginvarid=$pluginvarid");
	showtableheader();
	showtitle($lang['plugins_edit_vars']);
	showsetting('plugins_edit_vars_title', 'titlenew', $pluginvar['title'], 'text');
	showsetting('plugins_edit_vars_description', 'descriptionnew', $pluginvar['description'], 'textarea');
	showsetting('plugins_edit_vars_type', '', '', $typeselect);
	showsetting('plugins_edit_vars_variable', 'variablenew', $pluginvar['variable'], 'text');
	$iscomponent = !empty($_G['cache']['admin']['component']) && isset($_G['cache']['admin']['component'][$pluginvar['type']]);
	showtagheader('tbody', 'extra', $pluginvar['type'] == 'select' || $pluginvar['type'] == 'selects' || $iscomponent);
	showsetting('plugins_edit_vars_extra', 'extranew', $pluginvar['extra'], 'textarea', comment: !$iscomponent ? '' : admin\class_component::get_desc($pluginvar['type']));
	showtagfooter('tbody');
	showsubmit('varsubmit');
	showtablefooter();
	showformfooter();

} else {

	$titlenew = cutstr(trim($_GET['titlenew']), 25);
	$descriptionnew = cutstr(trim($_GET['descriptionnew']), 255);
	$variablenew = trim($_GET['variablenew']);
	$extranew = trim($_GET['extranew']);

	if(!$titlenew) {
		cpmsg('plugins_edit_var_title_invalid', '', 'error');
	} elseif($variablenew != $pluginvar['variable']) {
		if(!$variablenew || strlen($variablenew) > 40 || !ispluginkey($variablenew) || table_common_pluginvar::t()->check_variable($pluginid, $variablenew)) {
			cpmsg('plugins_edit_vars_invalid', '', 'error');
		}
	}

	table_common_pluginvar::t()->update_by_pluginvarid($pluginid, $pluginvarid, [
		'title' => $titlenew,
		'description' => $descriptionnew,
		'type' => $_GET['typenew'],
		'variable' => $variablenew,
		'extra' => $extranew
	]);

	updatecache(['plugin', 'setting', 'styles']);
	cleartemplatecache();
	cpmsg('plugins_edit_vars_succeed', "action=plugins&operation=edit&pluginid=$pluginid&anchor=vars", 'succeed');
}
