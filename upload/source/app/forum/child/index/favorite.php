<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$favfids = [];
$forum_favlist = table_home_favorite::t()->fetch_all_by_uid_idtype($_G['uid'], 'fid');
if(!$forum_favlist) {
	dsetcookie('nofavfid', 1, 31536000);
}
foreach($forum_favlist as $key => $favorite) {
	if(defined('IN_MOBILE')) {
		$forum_favlist[$key]['title'] = strip_tags($favorite['title']);
	}
	$favfids[] = $favorite['id'];
}
if($favfids) {
	$favforumlist = table_forum_forum::t()->fetch_all($favfids);
	$favforumlist_fields = table_forum_forumfield::t()->fetch_all($favfids);
	foreach($favforumlist as $id => $forum) {
		if($favforumlist_fields[$forum['fid']]['fid']) {
			$favforumlist[$id] = array_merge($forum, $favforumlist_fields[$forum['fid']]);
		}
		$favforumlist[$id]['extra'] = empty($favforumlist[$id]['extra']) ? [] : dunserialize($favforumlist[$id]['extra']);
		if(!is_array($favforumlist[$id]['extra'])) {
			$favforumlist[$id]['extra'] = [];
		}
		forum($favforumlist[$id]);
	}
}
	