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
if(!$style) {
	cpmsg('style_not_found', '', 'error');
}
$dir = $style['directory'];

require_once libfile('function/cloudaddons');
$templatedir = DISCUZ_TEMPLATE($style['directory']);
cloudaddons_validator(basename($dir).'.template');
$searchdir = dir($templatedir);
$stylearrays = $oldstylevars = $oldstylevarextras = [];
while($searchentry = $searchdir->read()) {
	if(str_starts_with($searchentry, 'discuz_style_') && (fileext($searchentry) == 'xml' || fileext($searchentry) == 'json')) {
		$importfile = $templatedir.'/'.$searchentry;
		$importtxt = implode('', file($importfile));
		$stylearrays[] = getimportdata('Discuz! Style');
	}
}

$result = table_common_stylevar::t()->fetch_all_by_styleid($id);
if(is_array($result) && !empty($result)) {
	foreach($result as $style) {
		$oldstylevars[$style['variable']] = $style['substitute'];
	}
}

$result = table_common_stylevar_extra::t()->fetch_all_by_styleid($id);
if(is_array($result) && !empty($result)) {
	foreach($result as $style) {
		$oldstylevarextras[$style['variable']] = $style;
	}
}

foreach($stylearrays as $stylearray) {

	foreach($stylearray['style'] as $variable => $substitute) {
		if(!isset($oldstylevars[$variable])) {
			table_common_stylevar::t()->insert(['styleid' => $id, 'variable' => $variable, 'substitute' => @dhtmlspecialchars($substitute)]);
		}
	}

	$delvariables = [];
	foreach($oldstylevars as $variable => $substitute) {
		if(!isset($stylearray['style'][$variable])) {
			$delvariables[] = $variable;
		}
	}
	if($delvariables) {
		table_common_stylevar::t()->delete_by_variable($id, $delvariables);
	}

	foreach($stylearray['var'] as $variable => $data) {
		if(!isset($oldstylevarextras[$variable])) {
			table_common_stylevar_extra::t()->insert(
				[
					'styleid' => $id,
					'displayorder' => $data['displayorder'],
					'title' => @dhtmlspecialchars($data['title']),
					'description' => @dhtmlspecialchars($data['description']),
					'variable' => $data['variable'],
					'type' => $data['type'],
					'value' => is_array($data['value']) ? serialize($data['value']) : $data['value'],
					'extra' => $data['extra'],
				]
			);
		} else {
			table_common_stylevar_extra::t()->update_by_variable(
				$id,
				$variable,
				[
					'displayorder' => $data['displayorder'],
					'title' => @dhtmlspecialchars($data['title']),
					'description' => @dhtmlspecialchars($data['description']),
					'type' => $data['type'],
					'extra' => $data['extra'],
				]
			);
		}
	}

	$delvariables = [];
	foreach($oldstylevarextras as $variable => $data) {
		if(!isset($stylearray['var'][$variable])) {
			$delvariables[] = $variable;
		}
	}

	if($delvariables) {
		table_common_stylevar_extra::t()->delete_by_variable($id, $delvariables);
	}

	table_common_style::t()->update($id, ['version' => $stylearray['style']['version']], true);

}

updatecache('styles');
updatecache('setting');

cpmsg('styles_upgrade_succeed', 'action=styles', 'succeed');