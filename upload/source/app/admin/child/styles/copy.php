<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$style = table_common_style::t()->fetch_by_styleid($id);
if(ispluginkey(basename($style['directory']))) {
	cloudaddons_validator(basename($style['directory']).'.template');
}
$style['name'] .= '_'.random(4);
$styleidnew = table_common_style::t()->insert(['name' => $style['name'], 'available' => $style['available'], 'templateid' => $style['templateid']], true);

foreach(table_common_stylevar::t()->fetch_all_by_styleid($id) as $stylevar) {
	table_common_stylevar::t()->insert(['styleid' => $styleidnew, 'variable' => $stylevar['variable'], 'substitute' => $stylevar['substitute']]);
}

foreach(table_common_stylevar_extra::t()->fetch_all_by_styleid($id) as $stylevarextra) {
	table_common_stylevar_extra::t()->insert(['styleid' => $styleidnew,
		'displayorder' => $stylevarextra['displayorder'],
		'title' => $stylevarextra['title'],
		'description' => $stylevarextra['description'],
		'variable' => $stylevarextra['variable'],
		'type' => $stylevarextra['type'],
		'value' => $stylevarextra['value'],
		'extra' => $stylevarextra['extra'],
	]);
}

updatecache(['setting', 'styles']);
cpmsg('styles_copy_succeed', 'action=styles', 'succeed');
	