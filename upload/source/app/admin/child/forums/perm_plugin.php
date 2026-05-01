<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$s = $exists = [];
foreach($_G['setting']['plugins']['perm'] as $c => $v) {
	$pluginid = $v['pluginid'];
	$colums = [
		'<input class="checkbox" title="'.cplang('select_all').'" type="checkbox" name="chkallplugin_'.$pluginid.'_'.$c.'" onclick="checkAll(\'value\', this.form, \'plugin_'.$pluginid.'_'.$c.'\', \'chkallplugin_'.$pluginid.'_'.$c.'\')" id="chkallplugin_'.$pluginid.'_'.$c.'" />',
		'<label for="chkallplugin_'.$pluginid.'_'.$c.'"> '.lang('plugin/'.$pluginid, $v['name']).'</label>', 'p_'.$c];
	foreach($perms as $perm) {
		$checked = str_contains($forum[$perm], "\tp_$c\t") ? 'checked="checked"' : NULL;
		$checked && $exists[$pluginid] = true;
		$colums[] = '<input class="checkbox" type="checkbox" name="'.$perm.'[]" value="p_'.$c.'" chkvalue="plugin_'.$pluginid.'_'.$c.'" '.$checked.'>';
	}
	$s[$pluginid] .= showtablerow('', ['', '', 'class="lightfont"'], $colums, true);
}

foreach($s as $pluginid => $value) {
	$pluginname = $_G['setting']['plugins']['name'][$pluginid] ?? $pluginid;
	$tg = '<a href="javascript:;" id="a_gplugin_'.$pluginid.'"  onclick="toggle_group(\'gplugin_'.$pluginid.'\')">['.($exists[$pluginid] ? '-' : '+').']</a>';
	showtablerow('', ['', '', 'class="lightfont" colspan="'.$permcolspan.'"'], [$tg, '<b>'.$pluginname.'</b>', 'plugin_'.$pluginid]);
	showtagheader('tbody', 'gplugin_'.$pluginid, $exists[$pluginid]);
	echo $value;
	showtagfooter('tbody');
}