<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$ids = [];
if(is_array($_GET['delete'])) {
	foreach($_GET['delete'] as $id) {
		$ids[] = $id;
	}
	if($ids) {
		table_home_click::t()->delete($ids, true);
	}
}

if(is_array($_GET['name'])) {
	foreach($_GET['name'] as $id => $val) {
		$id = intval($id);
		$updatearr = [
			'name' => dhtmlspecialchars($_GET['name'][$id]),
			'icon' => $_GET['icon'][$id],
			'idtype' => $idtype,
			'available' => intval($_GET['available'][$id]),
			'displayorder' => intval($_GET['displayorder'][$id]),
		];
		table_home_click::t()->update($id, $updatearr);
	}
}

if(is_array($_GET['newname'])) {
	foreach($_GET['newname'] as $key => $value) {
		if($value != '' && $_GET['newicon'][$key] != '') {
			$data = [
				'name' => dhtmlspecialchars($value),
				'icon' => $_GET['newicon'][$key],
				'idtype' => $idtype,
				'available' => intval($_GET['newavailable'][$key]),
				'displayorder' => intval($_GET['newdisplayorder'][$key])
			];
			table_home_click::t()->insert($data);
		}
	}
}

$keys = $ids = $_G['cache']['click'] = [];
foreach(table_home_click::t()->fetch_all_by_available() as $value) {
	if(!isset($_G['cache']['click'][$value['idtype']]) || count($_G['cache']['click'][$value['idtype']]) < 8) {
		$keys[$value['idtype']] = $keys[$value['idtype']] ? ++$keys[$value['idtype']] : 1;
		$_G['cache']['click'][$value['idtype']][$keys[$value['idtype']]] = $value;
	} else {
		$ids[] = $value['clickid'];
	}
}
if($ids) {
	table_home_click::t()->update($ids, ['available' => 0], true);
}
updatecache('click');
cpmsg('click_edit_succeed', 'action=click&idtype='.$idtype, 'succeed');

		