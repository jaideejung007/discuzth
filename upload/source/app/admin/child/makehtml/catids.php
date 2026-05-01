<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$starttime = strtotime($_GET['starttime']);
$data = [];
if($starttime) {
	loadcache('portalcategory');
	foreach($_G['cache']['portalcategory'] as $key => $value) {
		if($value['lastpublish'] >= $starttime) {
			$data[$key] = $key;
		}
	}
}
helper_output::xml($data ? implode(',', $data) : '');
	