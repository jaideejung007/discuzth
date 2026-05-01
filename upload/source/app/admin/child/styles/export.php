<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$stylearray = table_common_style::t()->fetch_by_styleid($id);
if(!$stylearray) {
	cpheader();
	cpmsg('styles_export_invalid', '', 'error');
}

$addonid = '';
if(preg_match('/^.?\/template\/([a-z]+[a-z0-9_]*)$/', $stylearray['directory'], $a) && $a[1] != 'default') {
	$addonid = $a[1].'.template';
}

if(($isplugindeveloper && $isfounder) || !$addonid || !cloudaddons_getmd5($addonid)) {
	if(ispluginkey(basename($stylearray['directory']))) {
		cpheader();
		cloudaddons_validator(basename($stylearray['directory']).'.template');
	}
	foreach(table_common_stylevar::t()->fetch_all_by_styleid($id) as $style) {
		$stylearray['style'][$style['variable']] = $style['substitute'];
	}

	foreach(table_common_stylevar_extra::t()->fetch_all_by_styleid($id) as $var) {
		unset($var['stylevarid']);
		unset($var['styleid']);
		$stylearray['var'][$var['variable']] = $var;
	}

	$stylearray['version'] = strip_tags($_G['setting']['version']);
	exportdata('Discuz! Style', basename($stylearray['directory']), $stylearray);
} else {
	cpheader();
	cpmsg('styles_export_invalid', '', 'error');
}