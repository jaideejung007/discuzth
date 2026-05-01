<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

if(!empty($_GET['identifier']) && !empty($_GET['pmod'])) {
	$operation = 'config';
}

if($operation != 'config' && !$admincp->isfounder) {
	cpmsg('noaccess_isfounder', '', 'error');
}

$pluginid = !empty($_GET['pluginid']) ? intval($_GET['pluginid']) : 0;
$anchor = !empty($_GET['anchor']) ? $_GET['anchor'] : '';
$isplugindeveloper = isset($_G['config']['plugindeveloper']) && $_G['config']['plugindeveloper'] > 0;
if(!empty($_GET['dir']) && !ispluginkey($_GET['dir'])) {
	unset($_GET['dir']);
}

require_once libfile('function/plugin');

$operation = $operation ? $operation : 'list';

if($operation == 'enable' || $operation == 'disable') {
	require_once childfile('plugins/enable_disable');
} elseif($operation == 'plugininstall' || $operation == 'pluginupgrade') {
	require_once childfile('plugins/plugininstall_upgrade');
} else {
	$file = childfile('plugins/'.$operation);
	if(!file_exists($file)) {
		cpmsg('undefined_action');
	}
	require_once $file;
}

function moduleample($typeid, $module, $plugin) {
	$samples = [
		1 => "<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

?>",
		2 => "<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


?>",
		3 => "<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

?>",
		4 => "<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_{PLUGINID} {

}

?>",
		5 => "<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mobileplugin_{PLUGINID} {

}

?>",
		6 => "<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class threadplugin_{PLUGINID} {
	var \$name = '';
	var \$iconfile = '';
	var \$buttontext = '';
}

?>"];
	$types = [1 => 1, 5 => 1, 27 => 1, 23 => 1, 25 => 1, 24 => 1, 7 => 2, 17 => 2, 19 => 2, 14 => 2, 26 => 2, 21 => 2, 15 => 2, 16 => 2, 3 => 3, 11 => 4, 28 => 5, 12 => 6];

	$code = $samples[$types[$typeid]];
	$code = str_replace(
		[
			'{DATE}',
			'{PLUGINID}',
			'{MODULE}',
			'{MODULENAME}',
			'{COPYRIGHT}',
		],
		[
			dgmdate(TIMESTAMP, 'Y'),
			$plugin['identifier'],
			$module,
			cplang('plugins_edit_modules_type_'.$typeid),
			$plugin['copyright'],
		], $code);
	return $code;
}

