<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$tid = intval($_GET['tid']);
if($tid) {
	$membernum = table_forum_post::t()->count_author_by_tid($tid);
	showmessage('thread_reshreply_membernum', '', ['membernum' => intval($membernum - 1)], ['alert' => 'info']);
}
dexit();
	