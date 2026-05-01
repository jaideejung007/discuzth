<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$albumlist = [];
if(helper_access::check_module('album') && $_G['group']['allowupload'] && $_G['uid']) {
	$query = table_home_album::t()->fetch_all_by_uid($_G['uid'], 'updatetime');
	foreach($query as $value) {
		if($value['picnum']) {
			$albumlist[] = $value;
		}
	}
}

include template('forum/albumlist');