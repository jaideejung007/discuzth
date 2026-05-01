<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$op || !$ctid || $_GET['formhash'] != FORMHASH) {
	showmessage('undefined_action', NULL);
}

if(!$_G['collection']['ctid'] || $_G['collection']['uid'] == $_G['uid']) {
	showmessage('collection_permission_deny');
}
$_GET['handlekey'] = 'followcollection';
if($op == 'follow') {
	$follownum = table_forum_collectionfollow::t()->count_by_uid($_G['uid']);
	if($follownum >= $_G['group']['allowfollowcollection']) {
		showmessage('collection_follow_limited', '', ['limit' => $_G['group']['allowfollowcollection']], ['closetime' => '2', 'showmsg' => '1']);
	}

	$collectionfollow = table_forum_collectionfollow::t()->fetch_by_ctid_uid($ctid, $_G['uid']);
	if(!$collectionfollow['ctid']) {
		$data = [
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'ctid' => $ctid,
			'dateline' => $_G['timestamp'],
			'lastvisit' => $_G['timestamp']
		];

		table_forum_collectionfollow::t()->insert($data);
		table_forum_collection::t()->update_by_ctid($ctid, 0, 1, 0);

		if($_G['collection']['uid'] != $_G['uid']) {
			updatecreditbyaction('followedcollection', $_G['collection']['uid']);
			notification_add($_G['collection']['uid'], 'system', 'collection_befollowed', ['from_id' => $_G['collection']['ctid'], 'from_idtype' => 'collectionfollow', 'ctid' => $_G['collection']['ctid'], 'collectionname' => $_G['collection']['name']], 1);
		}

		showmessage('collection_follow_succ', dreferer(), ['status' => 1], ['closetime' => '2', 'showmsg' => '1']);
	}


} elseif($op == 'unfo') {
	$collectionfollow = table_forum_collectionfollow::t()->fetch_by_ctid_uid($ctid, $_G['uid']);
	if($collectionfollow['ctid']) {
		table_forum_collectionfollow::t()->delete_by_ctid_uid($ctid, $_G['uid']);
		table_forum_collection::t()->update_by_ctid($ctid, 0, -1, 0);
		showmessage('collection_unfollow_succ', dreferer(), ['status' => 2], ['closetime' => '2', 'showmsg' => '1']);
	}
}

