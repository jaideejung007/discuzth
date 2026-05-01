<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/forumlist');
$favforums = table_home_favorite::t()->fetch_all_by_uid_idtype($_G['uid'], 'fid');
$visitedforums = [];
if($_G['cookie']['visitedfid']) {
	loadcache('forums');
	foreach(explode('D', $_G['cookie']['visitedfid']) as $fid) {
		$fid = intval($fid);
		$visitedforums[$fid] = $_G['cache']['forums'][$fid]['name'];
	}
}
$forumlist = forumselect(FALSE, 1);
include template('forum/ajax_forumlist');
	