<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$isplugindeveloper || !$pluginid) {
	cpmsg('undefined_action', '', 'error');
}

$plugin = table_common_plugin::t()->fetch($pluginid);
if(!$plugin) {
	cpheader();
	cpmsg('plugin_not_found', '', 'error');
}

unset($plugin['pluginid']);

$pluginarray = [];
$pluginarray['plugin'] = $plugin;
$pluginarray['version'] = strip_tags($_G['setting']['version']);

foreach(table_common_pluginvar::t()->fetch_all_by_pluginid($pluginid) as $var) {
	unset($var['pluginvarid'], $var['pluginid']);
	$pluginarray['var'][] = $var;
}
$modules = dunserialize($pluginarray['plugin']['modules']);
if($modules['extra']['langexists'] && file_exists($file = DISCUZ_DATA.'./plugindata/'.$pluginarray['plugin']['identifier'].'.lang.php')) {
	include $file;
	if(!empty($scriptlang[$pluginarray['plugin']['identifier']])) {
		$pluginarray['language']['scriptlang'] = $scriptlang[$pluginarray['plugin']['identifier']];
	}
	if(!empty($templatelang[$pluginarray['plugin']['identifier']])) {
		$pluginarray['language']['templatelang'] = $templatelang[$pluginarray['plugin']['identifier']];
	}
	if(!empty($installlang[$pluginarray['plugin']['identifier']])) {
		$pluginarray['language']['installlang'] = $installlang[$pluginarray['plugin']['identifier']];
	}
	if(!empty($systemlang[$pluginarray['plugin']['identifier']])) {
		$pluginarray['language']['systemlang'] = $systemlang[$pluginarray['plugin']['identifier']];
	}
}
unset($modules['extra']);
$pluginarray['plugin']['modules'] = serialize($modules);
$plugindir = DISCUZ_PLUGIN($pluginarray['plugin']['directory']);
if(file_exists($plugindir.'/install.php')) {
	$pluginarray['installfile'] = 'install.php';
}
if(file_exists($plugindir.'/uninstall.php')) {
	$pluginarray['uninstallfile'] = 'uninstall.php';
}
if(file_exists($plugindir.'/upgrade.php')) {
	$pluginarray['upgradefile'] = 'upgrade.php';
}
if(file_exists($plugindir.'/check.php')) {
	$pluginarray['checkfile'] = 'check.php';
}
if(file_exists($plugindir.'/enable.php')) {
	$pluginarray['enablefile'] = 'enable.php';
}
if(file_exists($plugindir.'/disable.php')) {
	$pluginarray['disablefile'] = 'disable.php';
}

exportdata('Discuz! Plugin', $plugin['identifier'], $pluginarray);
	