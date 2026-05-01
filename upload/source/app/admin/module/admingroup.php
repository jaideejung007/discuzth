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

$operation = $operation ? $operation : 'list';

$file = childfile('admingroup/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

function deletegroupcache($groupidarray) {
	if(!empty($groupidarray) && is_array($groupidarray)) {
		$cachenames = [];
		foreach($groupidarray as $id) {
			if(($id = dintval($id))) {
				$cachenames['usergroup_'.$id] = 'usergroup_'.$id;
				$cachenames['admingroup_'.$id] = 'admingroup_'.$id;
			}
		}
		if(!empty($cachenames)) {
			table_common_syscache::t()->delete_syscache($cachenames);
		}
	}
}

