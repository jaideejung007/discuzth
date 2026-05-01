<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($statid && $statkey) {
	$q = "statid=$statid&statkey=$statkey";
	$q = rawurlencode(base64_encode($q));
	$url = 'https://stat.discuz.vip/stat_ins.php?action=checkstat&q='.$q;
	$key = dfsockopen($url);
	$newstatdisable = $key == $statkey ? 0 : 1;
	if($newstatdisable != $statdisable) {
		table_common_setting::t()->update_setting('statdisable', $newstatdisable);
		require_once libfile('function/cache');
		updatecache('setting');
	}
}
	