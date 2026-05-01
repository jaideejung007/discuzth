<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('posttableids');
if(!empty($_G['cache']['posttableids'])) {
	$posttableids = $_G['cache']['posttableids'];
} else {
	$posttableids = ['0'];
}
$pidmax = 0;
foreach($posttableids as $id) {
	if($id == 0) {
		$pidtmp = table_forum_post::t()->fetch_maxid(0);
	} else {
		$pidtmp = table_forum_post::t()->fetch_maxid($id);
	}
	if($pidtmp > $pidmax) {
		$pidmax = $pidtmp;
	}
}
$auto_increment = $pidmax + 1;
table_forum_post_tableid::t()->alter_auto_increment($auto_increment);
cpmsg('postsplit_resetpid_succeed', 'action=postsplit&operation=manage', 'succeed');
	