<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$lastpost = $_G['thread']['lastpost'];

$glue = '<';
$sort = 'DESC';
if($_GET['goto'] == 'nextnewset') {
	$glue = '>';
	$sort = 'ASC';
}
$next = table_forum_thread::t()->fetch_next_tid_by_fid_lastpost($_G['fid'], $lastpost, $glue, $sort, $_G['thread']['threadtableid']);
if($next) {
	dheader("Location: forum.php?mod=viewthread&tid=$next");
} elseif($_GET['goto'] == 'nextnewset') {
	showmessage('redirect_nextnewset_nonexistence');
} else {
	showmessage('redirect_nextoldset_nonexistence');
}
	