<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$delids = [];
foreach(table_common_district::t()->fetch_all_by_upid($theid) as $value) {
	$usetype = 0;
	if($_POST['birthcity'][$value['id']] && $_POST['residecity'][$value['id']]) {
		$usetype = 3;
	} elseif($_POST['birthcity'][$value['id']]) {
		$usetype = 1;
	} elseif($_POST['residecity'][$value['id']]) {
		$usetype = 2;
	}
	if(!isset($_POST['district'][$value['id']])) {
		$delids[] = $value['id'];
	} elseif($_POST['district'][$value['id']] != $value['name'] || $_POST['displayorder'][$value['id']] != $value['displayorder'] || $usetype != $value['usetype']) {
		table_common_district::t()->update($value['id'], ['name' => $_POST['district'][$value['id']], 'displayorder' => $_POST['displayorder'][$value['id']], 'usetype' => $usetype]);
	}
}
if($delids) {
	$ids = $delids;
	for($i = $level; $i < 4; $i++) {
		$ids = [];
		foreach(table_common_district::t()->fetch_all_by_upid($delids) as $value) {
			$value['id'] = intval($value['id']);
			$delids[] = $value['id'];
			$ids[] = $value['id'];
		}
		if(empty($ids)) {
			break;
		}
	}
	table_common_district::t()->delete($delids);
}
if(!empty($_POST['districtnew'])) {
	$inserts = [];
	$displayorder = '';
	foreach($_POST['districtnew'] as $key => $value) {
		$displayorder = trim($_POST['districtnew_order'][$key]);
		$value = trim($value);
		if(!empty($value)) {
			table_common_district::t()->insert(['name' => $value, 'level' => $level, 'upid' => $theid, 'displayorder' => $displayorder]);
		}
	}
}
cpmsg('setting_district_edit_success', 'action=district&countryid='.$values[0].'&pid='.$values[1].'&cid='.$values[2].'&did='.$values[3], 'succeed');
	