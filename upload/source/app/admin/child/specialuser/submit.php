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
		table_home_specialuser::t()->delete_by_uid_status($ids, $status);
		cpmsg('specialuser_'.$op.'_del_succeed', 'action='.$url, 'succeed');
	}
}

if(is_array($_GET['displayorder'])) {
	foreach($_GET['displayorder'] as $id => $val) {
		$updatearr = ['displayorder' => intval($_GET['displayorder'][$id])];
		table_home_specialuser::t()->update_by_uid_status($id, $status, $updatearr);
	}
}
cpmsg('specialuser_defaultuser_edit_succeed', 'action='.$url, 'succeed');
		