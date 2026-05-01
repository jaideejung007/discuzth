<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$titlelimit = 30;
$desclimit = 250;
$reasonlimit = 250;

$oplist = ['add', 'edit', 'remove', 'addthread', 'delthread', 'acceptinvite', 'removeworker', 'invite'];
if(!in_array($op, $oplist)) {
	$op = '';
}

if(empty($_G['uid'])) {
	showmessage('login_before_enter_home', null, [], ['showmsg' => true, 'login' => 1]);
}

if(empty($op) || $op == 'add') {
	if(!helper_access::check_module('collection')) {
		showmessage('quickclear_noperm');
	}
	$_GET['handlekey'] = 'createcollection';

	$navtitle = lang('core', 'title_collection_create');

	$createdcollectionnum = table_forum_collection::t()->count_by_uid($_G['uid']);
	$reamincreatenum = $_G['group']['allowcreatecollection'] - $createdcollectionnum;
	if(!$_G['group']['allowcreatecollection'] || $reamincreatenum <= 0) {
		showmessage('collection_create_exceed_limit');
	}
	if(!$_GET['submitcollection']) {

		include template('forum/collection_add');

	} else {
		if(!submitcheck('collectionsubmit')) {
			showmessage('undefined_action', NULL);
		}
		if(!$_GET['title']) {
			showmessage('collection_edit_checkentire');
		}

		$newCollectionTitle = censor(dhtmlspecialchars($_GET['title']));
		$newCollectionTitle = cutstr($newCollectionTitle, $titlelimit, '');

		$newcollection = [
			'name' => $newCollectionTitle,
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'desc' => dhtmlspecialchars(cutstr(censor($_GET['desc']), $desclimit, '')),
			'dateline' => $_G['timestamp'],
			'lastupdate' => $_G['timestamp'],
			'lastvisit' => $_G['timestamp'],
			'keyword' => parse_keyword($_GET['keyword'], true)
		];

		$newctid = table_forum_collection::t()->insert($newcollection, true);

		$newcollection = [
			'cover' => uploadCollectionImg('cover', $newctid, 1000, 250),
			'icon' => uploadCollectionImg('icon', $newctid, 200, 200),
		];
		table_forum_collection::t()->update($newctid, $newcollection);

		if($newctid) {
			showmessage('collection_create_succ', 'forum.php?mod=collection&action=view&ctid='.$newctid, ['ctid' => $newctid, 'title' => $newCollectionTitle], ['closetime' => '2', 'showmsg' => ($_GET['inajax'] ? '0' : '1')]);
		}
	}

} elseif($op == 'edit') {
	$navtitle = lang('core', 'title_collection_edit');

	if(!$ctid) {
		showmessage('undefined_action', NULL);
	}

	if(!$_G['collection']['ctid'] || !checkcollectionperm($_G['collection'], $_G['uid'])) {
		showmessage('collection_permission_deny');
	}

	if(!submitcheck('collectionsubmit')) {

		include template('forum/collection_add');

	} else {
		if(!$_GET['title']) {
			showmessage('collection_edit_checkentire');
		}
		if($_GET['formhash'] != FORMHASH) {
			showmessage('undefined_action', NULL);
		}

		$newCollectionTitle = censor(dhtmlspecialchars($_GET['title']));
		$newCollectionTitle = cutstr($newCollectionTitle, 30, '');

		$newcollection = [
			'name' => $newCollectionTitle,
			'desc' => dhtmlspecialchars(cutstr(censor($_GET['desc']), $desclimit, '')),
			'keyword' => parse_keyword($_GET['keyword'], true)
		];

		$upload = uploadCollectionImg('cover', $ctid, 1000, 250);
		if($upload !== -1) {
			$newcollection['cover'] = $upload;
		}
		if(!empty($_GET['deletecover'])) {
			if($_G['collection']['cover']) {
				$imgfile = getCollectionImgDir('cover', $ctid);
				@unlink($_G['setting']['attachdir'].$imgfile);
				ftpcmd('delete', $imgfile);
				$newcollection['cover'] = 0;
			}
		}
		$upload = uploadCollectionImg('icon', $ctid, 200, 200);
		if($upload !== -1) {
			$newcollection['icon'] = $upload;
		}

		table_forum_collection::t()->update($ctid, $newcollection);

		if($_GET['title'] != $_G['collection']['name']) {
			table_forum_collectionteamworker::t()->update_by_ctid($ctid, $_GET['title']);
		}

		showmessage('collection_edit_succ', 'forum.php?mod=collection&action=view&ctid='.$ctid);
	}
} elseif($op == 'remove') {
	if($_GET['formhash'] != FORMHASH) {
		showmessage('undefined_action', NULL);
	}
	if($_G['collection'] && checkcollectionperm($_G['collection'], $_G['uid'])) {
		require_once libfile('function/delete');

		deletecollection($_G['collection']['ctid']);

		showmessage('collection_delete_succ', 'forum.php?mod=collection&op=my');
	} else {
		showmessage('collection_permission_deny');
	}

} elseif($op == 'addthread') {
	if((!$_G['forum_thread'] || !$_G['forum']) && !is_array($_GET['tids'])) {
		showmessage('thread_nonexistence');
	}

	if(!is_array($_GET['tids']) && $_G['forum']['disablecollect']) {
		showmessage('collection_forum_deny', '', [], ['showdialog' => 1]);
	}

	if(!submitcheck('addthread')) {
		$createdcollectionnum = table_forum_collection::t()->count_by_uid($_G['uid']);
		$reamincreatenum = $_G['group']['allowcreatecollection'] - $createdcollectionnum;

		$collections = getmycollection($_G['uid']);
		$tidcollections = [];

		if(count($collections) > 0) {
			$tidrelated = table_forum_collectionrelated::t()->fetch($tid, true);
			$tidcollections = explode("\t", $tidrelated['collection']);
		}
		$allowcollections = array_diff(array_keys($collections), $tidcollections);
		if($reamincreatenum <= 0 && count($allowcollections) <= 0) {
			showmessage('collection_none_avail_collection', '', [], ['showdialog' => 1]);
		}

		include template('forum/collection_select');

	} else {
		if(!$ctid) {
			showmessage('collection_no_selected', '', [], ['showdialog' => 1]);
		}
		if(!is_array($_GET['tids'])) {
			$tid = $_G['tid'];
			$thread[$tid] = &$_G['thread'];
		}
		$collectiondata = table_forum_collection::t()->fetch_all($ctid);
		if(!is_array($collectiondata) || count($collectiondata) < 0) {
			showmessage('undefined_action', NULL);
		} else {
			foreach($collectiondata as $curcollectiondata) {
				if(!$curcollectiondata['ctid']) {
					showmessage('collection_permission_deny', '', [], ['showdialog' => 1]);
				}

				if(!checkcollectionperm($curcollectiondata, $_G['uid'], true)) {
					showmessage('collection_non_creator', '', [], ['showdialog' => 1]);
				}

				if(!is_array($_GET['tids'])) {
					$checkexistctid[$tid] = table_forum_collectionthread::t()->fetch_by_ctid_tid($curcollectiondata['ctid'], $thread[$tid]['tid']);
					if($checkexistctid[$tid]['ctid']) {
						showmessage('collection_thread_exists', '', [], ['showdialog' => 1]);
					}

					$tids[0] = $tid;
					$checkexist[$tid] = table_forum_collectionrelated::t()->fetch($tid, true);
				} else {
					$thread = table_forum_thread::t()->fetch_all($_GET['tids']);
					foreach($thread as $perthread) {
						$fids[$perthread['fid']] = $perthread['fid'];
					}
					$fids = array_keys($fids);
					$foruminfo = table_forum_forumfield::t()->fetch_all($fids);
					$tids = array_keys($thread);
					$checkexistctid = table_forum_collectionthread::t()->fetch_all_by_ctid_tid($curcollectiondata['ctid'], $tids);
					$checkexist = table_forum_collectionrelated::t()->fetch_all($tids, true);
				}

				$addsum = 0;
				foreach($tids as $curtid) {
					$thread_fid = $thread[$curtid]['fid'];
					if(!$checkexistctid[$curtid]['ctid'] && !$foruminfo[$thread_fid]['disablecollect']) {
						$newthread = [
							'ctid' => $curcollectiondata['ctid'],
							'tid' => $thread[$curtid]['tid'],
							'dateline' => $thread[$curtid]['dateline'],
							'reason' => cutstr(censor(dhtmlspecialchars($_GET['reason'])), $reasonlimit, '')
						];

						table_forum_collectionthread::t()->insert($newthread);
					} else {
						continue;
					}

					if(!$checkexist[$curtid]) {
						table_forum_collectionrelated::t()->insert(['tid' => $curtid, 'collection' => $curcollectiondata['ctid']."\t"]);
						$checkexist[$curtid] = 1;
					} else {
						table_forum_collectionrelated::t()->update_collection_by_ctid_tid($curcollectiondata['ctid'], $curtid);
					}
					if(!getstatus($thread[$curtid]['status'], 9)) {
						table_forum_thread::t()->update_status_by_tid($curtid, '256');
					}

					if($_G['uid'] != $thread[$curtid]['authorid']) {
						notification_add($thread[$curtid]['authorid'], 'system', 'collection_becollected', ['from_id' => $_G['collection']['ctid'], 'from_idtype' => 'collectionthread', 'ctid' => $_G['collection']['ctid'], 'collectionname' => $_G['collection']['name'], 'tid' => $curtid, 'threadname' => $thread[$curtid]['subject']], 1);
					}

					$addsum++;
				}

				if($addsum > 0) {
					$lastpost = [
						'lastpost' => $thread[$tids[0]]['tid'],
						'lastsubject' => $thread[$tids[0]]['subject'],
						'lastposttime' => $thread[$tids[0]]['dateline'],
						'lastposter' => $thread[$tids[0]]['author']
					];
					table_forum_collection::t()->update_by_ctid($curcollectiondata['ctid'], $addsum, 0, 0, $_G['timestamp'], 0, 0, $lastpost);
				}
			}
		}

		showmessage('collection_collect_succ', dreferer(), [], ['alert' => 'right', 'closetime' => true, 'locationtime' => true, 'showdialog' => 1]);
	}

} elseif($op == 'delthread') {
	if($_GET['formhash'] != FORMHASH) {
		showmessage('undefined_action', NULL);
	}
	if(!$ctid || empty($_GET['delthread']) || !is_array($_GET['delthread']) || count($_GET['delthread']) == 0) {
		showmessage('collection_no_thread');
	}

	if(!$_G['collection']['ctid'] || !checkcollectionperm($_G['collection'], $_G['uid'])) {
		showmessage('collection_permission_deny');
	}
	require_once libfile('function/delete');
	deleterelatedtid($_GET['delthread'], $_G['collection']['ctid']);
	$decthread = table_forum_collectionthread::t()->delete_by_ctid_tid($ctid, $_GET['delthread']);

	$lastpost = null;
	if(in_array($_G['collection']['lastpost'], $_GET['delthread']) && ($_G['collection']['threadnum'] - $decthread) > 0) {
		$collection_thread = table_forum_collectionthread::t()->fetch_by_ctid_dateline($ctid);
		if($collection_thread) {
			$thread = table_forum_thread::t()->fetch_thread($collection_thread['tid']);
			$lastpost = [
				'lastpost' => $thread['tid'],
				'lastsubject' => $thread['subject'],
				'lastposttime' => $thread['dateline'],
				'lastposter' => $thread['authorid']
			];
		}
	}

	table_forum_collection::t()->update_by_ctid($ctid, -$decthread, 0, 0, 0, 0, 0, $lastpost);

	showmessage('collection_remove_thread', 'forum.php?mod=collection&action=view&ctid='.$ctid);
} elseif($op == 'invite') {
	if(!$ctid) {
		showmessage('undefined_action', NULL);
	}

	if(!$_G['collection']['ctid'] || !checkcollectionperm($_G['collection'], $_G['uid'])) {
		showmessage('collection_permission_deny');
	}

	$collectionteamworker = table_forum_collectionteamworker::t()->fetch_all_by_ctid($ctid);

	$submitworkers = (!empty($_GET['users']) && is_array($_GET['users'])) ? count($_GET['users']) : 0;

	if((count($collectionteamworker) + $submitworkers) >= $maxteamworkers) {
		showmessage('collection_teamworkers_exceed');
	}

	require_once libfile('function/friend');

	if($_GET['username'] && !$_GET['users']) {
		$_GET['users'][] = $_GET['username'];
	}

	if(!$_GET['users']) {

		if($_POST['formhash']) {
			showmessage('collection_teamworkers_noselect', NULL);
		}

		$friends = [];
		if($space['friendnum']) {
			$query = table_home_friend::t()->fetch_all_by_uid($_G['uid'], 0, 100, true);
			foreach($query as $value) {
				$value['uid'] = $value['fuid'];
				$value['username'] = daddslashes($value['fusername']);
				$friends[] = $value;
			}
		}
		$friendgrouplist = friend_group_list();

		include template('forum/collection_invite');
	} else {
		$invitememberuids = [];
		if(is_array($_GET['users'])) {
			$invitememberuids = table_common_member::t()->fetch_all_uid_by_username($_GET['users']);
		}

		if(!$invitememberuids) {
			showmessage('collection_no_teamworkers');
		}

		if(!friend_check($invitememberuids) || in_array($_G['uid'], $invitememberuids)) {
			showmessage('collection_non_friend');
		}

		$collectionteamworker = array_keys($collectionteamworker);

		if(in_array($invitememberuids, $collectionteamworker)) {
			showmessage('collection_teamworkers_exists');
		}

		foreach($invitememberuids as $invitememberuid) {
			$data = ['ctid' => $ctid, 'uid' => $invitememberuid, 'dateline' => $_G['timestamp']];

			table_forum_collectioninvite::t()->insert($data, false, true);

			notification_add($invitememberuid, 'system', 'invite_collection', ['ctid' => $_G['collection']['ctid'], 'collectionname' => $_G['collection']['name'], 'dateline' => $_G['timestamp']], 1);
		}

		showmessage('collection_invite_succ', 'forum.php?mod=collection&action=view&ctid='.$ctid, [], ['alert' => 'right', 'closetime' => true, 'showdialog' => 1]);
	}
} elseif($op == 'acceptinvite') {
	if(!submitcheck('ctid', 1)) {
		showmessage('undefined_action', NULL);
	} else {
		$collectioninvite = table_forum_collectioninvite::t()->fetch_by_ctid_uid($ctid, $_G['uid']);
		if(!$collectioninvite['ctid'] || $_GET['dateline'] != $collectioninvite['dateline']) {
			showmessage('undefined_action', NULL);
		}

		$teamworkernum = table_forum_collectionteamworker::t()->count_by_ctid($ctid);
		if($teamworkernum >= $maxteamworkers) {
			showmessage('collection_teamworkers_exceed');
		}

		table_forum_collectioninvite::t()->delete_by_ctid_uid($ctid, $_G['uid']);

		$newworker = [
			'ctid' => $ctid,
			'uid' => $_G['uid'],
			'name' => $_G['collection']['name'],
			'username' => $_G['username'],
			'lastvisit' => $_G['timestamp']
		];

		table_forum_collectionteamworker::t()->insert($newworker, false, true);

		showmessage('collection_invite_accept', 'forum.php?mod=collection&action=view&ctid='.$ctid);
	}
} elseif($op == 'removeworker') {
	if(!submitcheck('ctid', 1)) {
		showmessage('undefined_action', NULL);
	} else {
		if($_GET['formhash'] != FORMHASH) {
			showmessage('undefined_action', NULL);
		}

		if(!$_G['collection']['ctid']) {
			showmessage('collection_permission_deny');
		}
		if($_GET['uid'] != $_G['uid']) {
			if($_G['collection']['uid'] != $_G['uid']) {
				showmessage('collection_remove_deny');
			}
			$removeuid = $_GET['uid'];
		} else {
			$removeuid = $_G['uid'];
		}

		$collectionteamworker = array_keys(table_forum_collectionteamworker::t()->fetch_all_by_ctid($ctid));

		if(!in_array($removeuid, $collectionteamworker)) {
			showmessage('collection_teamworkers_nonexists');
		}

		table_forum_collectionteamworker::t()->delete_by_ctid_uid($ctid, $removeuid);

		notification_add($removeuid, 'system', 'exit_collection', ['ctid' => $_G['collection']['ctid'], 'collectionname' => $_G['collection']['name']], 1);

		if($_GET['inajax']) {
			showmessage('', dreferer(), [], ['msgtype' => 3, 'showmsg' => 1]);
		} else {
			showmessage('collection_teamworkers_exit_succ', 'forum.php?mod=collection&action=view&ctid='.$ctid);
		}
	}
}

