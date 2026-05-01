<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$updateviews = [];
$deltids = [];
foreach(table_forum_threadaddviews::t()->fetch_all_order_by_tid(500) as $tid => $addview) {
	$deltids[$tid] = $updateviews[$addview['addviews']][] = $tid;
}
if($deltids) {
	table_forum_threadaddviews::t()->delete($deltids);
}
foreach($updateviews as $views => $tids) {
	table_forum_thread::t()->increase($tids, ['views' => $views], true);
}

