<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_forumstick() {
	$data = [];
	$forumstickthreads = table_common_setting::t()->fetch_setting('forumstickthreads', true);
	$forumstickcached = [];
	if($forumstickthreads) {
		foreach($forumstickthreads as $forumstickthread) {
			foreach($forumstickthread['forums'] as $fid) {
				$forumstickcached[$fid][] = $forumstickthread['tid'];
			}
		}
		$data = $forumstickcached;
	} else {
		$data = [];
	}

	savecache('forumstick', $data);
}

