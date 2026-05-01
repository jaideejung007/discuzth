<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/cache');
loadcache('collection_index');
$collectionrecommend = dunserialize($_G['setting']['collectionrecommend']);
if(TIMESTAMP - $_G['cache']['collection_index']['dateline'] > 3600 * 4) {
	$collectiondata = $followdata = [];
	if($_G['setting']['collectionrecommendnum']) {
		if($collectionrecommend['ctids']) {
			$collectionrecommend['ctidsKey'] = array_keys($collectionrecommend['ctids']);
			$tmpcollection = table_forum_collection::t()->fetch_all($collectionrecommend['ctidsKey']);
			foreach($collectionrecommend['ctids'] as $ctid => $setcollection) {
				if($tmpcollection[$ctid]) {
					$collectiondata[$ctid] = $tmpcollection[$ctid];
				}
			}
			unset($tmpcollection, $ctid, $setcollection);
		}
		if($collectionrecommend['autorecommend']) {
			require_once libfile('function/collection');
			$autorecommenddata = getHotCollection(500);
		}
	}

	savecache('collection_index', ['dateline' => TIMESTAMP, 'data' => $collectiondata, 'auto' => $autorecommenddata]);
	$collectiondata = ['data' => $collectiondata, 'auto' => $autorecommenddata];
} else {
	$collectiondata = &$_G['cache']['collection_index'];
}

if($_G['setting']['showfollowcollection']) {
	$followcollections = $_G['uid'] ? table_forum_collectionfollow::t()->fetch_all_by_uid($_G['uid']) : [];;
	if($followcollections) {
		$collectiondata['follows'] = table_forum_collection::t()->fetch_all(array_keys($followcollections), 'dateline', 'DESC', 0, $_G['setting']['showfollowcollection']);
	}
}
if($collectionrecommend['autorecommend'] && $collectiondata['auto']) {
	$randrecommend = array_rand($collectiondata['auto'], min($collectionrecommend['autorecommend'], count($collectiondata['auto'])));
	if($randrecommend && !is_array($randrecommend)) {
		$collectiondata['data'][$randrecommend] = $collectiondata['auto'][$randrecommend];
	} else {
		foreach($randrecommend as $ctid) {
			$collectiondata['data'][$ctid] = $collectiondata['auto'][$ctid];
		}
	}
}
if($collectiondata['data']) {
	$collectiondata['data'] = array_slice($collectiondata['data'], 0, $collectionrecommend['autorecommend'], true);
}
	