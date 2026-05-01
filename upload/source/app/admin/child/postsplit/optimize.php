<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$_G['setting']['bbclosed']) {
	cpmsg('postsplit_forum_must_be_closed', 'action=postsplit&operation=manage', 'error');
}

$fromtableid = intval($_GET['tableid']);
$optimize = true;
$tablename = getposttable($fromtableid);
if($fromtableid && $tablename != 'forum_post') {
	$count = table_forum_post::t()->count_table($fromtableid);
	if(!$count) {
		table_forum_post::t()->drop_table($fromtableid);

		unset($posttable_info[$fromtableid]);
		table_common_setting::t()->update_setting('posttable_info', $posttable_info);
		savecache('posttable_info', $posttable_info);
		update_posttableids();
		$optimize = false;
	}

}
if($optimize) {
	table_forum_post::t()->optimize_table($fromtableid);
}
cpmsg('postsplit_do_succeed', 'action=postsplit', 'succeed');
	