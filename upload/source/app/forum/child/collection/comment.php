<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($_G['uid'])) {
	showmessage('login_before_enter_home', null, [], ['showmsg' => true, 'login' => 1]);
}

$oplist = ['add', 'del', 'pop', 'recommend'];
if(!in_array($op, $oplist)) {
	$op = '';
}

if(empty($op) || $op == 'add') {
	$_GET['handlekey'] = 'addComment';
	if(!$ctid) {
		showmessage('undefined_action', NULL);
	}

	if(!$_G['group']['allowcommentcollection']) {
		showmessage('collection_comment_closed');
	}

	require_once libfile('function/spacecp');


	if(!$_G['collection']['ctid']) {
		showmessage('collection_permission_deny');
	}

	$waittime = interval_check('post');
	if($waittime > 0) {
		showmessage('operating_too_fast', '', ['waittime' => $waittime], ['return' => true]);
	}

	$memberrate = table_forum_collectioncomment::t()->fetch_rate_by_ctid_uid($_G['collection']['ctid'], $_G['uid']);

	if(!trim($_GET['message']) && ((!$memberrate && !$_GET['ratescore']) || $memberrate)) {
		showmessage('collection_edit_checkentire');
	}

	if($_G['setting']['maxpostsize'] && strlen($_GET['message']) > $_G['setting']['maxpostsize']) {
		showmessage('post_message_toolong', '', ['maxpostsize' => $_G['setting']['maxpostsize']]);
	}

	$newcomment = [
		'ctid' => $_G['collection']['ctid'],
		'uid' => $_G['uid'],
		'username' => $_G['username'],
		'message' => dhtmlspecialchars(censor($_GET['message'])),
		'dateline' => $_G['timestamp'],
		'useip' => $_G['clientip'],
		'port' => $_G['remoteport']
	];

	if(!$memberrate) {
		$newcomment['rate'] = $_GET['ratescore'];
	} else {
		$_GET['ratescore'] = 0;
	}

	table_forum_collectioncomment::t()->insert($newcomment);
	table_forum_collection::t()->update_by_ctid($_G['collection']['ctid'], 0, 0, 1, 0, $_GET['ratescore'], $_G['collection']['ratenum']);

	if($_G['collection']['uid'] != $_G['uid']) {
		notification_add($_G['collection']['uid'], 'system', 'collection_becommented', ['from_id' => $_G['collection']['ctid'], 'from_idtype' => 'collectioncomment', 'ctid' => $_G['collection']['ctid'], 'collectionname' => $_G['collection']['name']], 1);
	}

	table_common_member_status::t()->update($_G['uid'], ['lastpost' => TIMESTAMP], 'UNBUFFERED');

	showmessage('collection_comment_succ', $tid ? 'forum.php?mod=viewthread&tid='.$tid : dreferer());
} elseif($op == 'del') {
	if(!submitcheck('formhash')) {
		showmessage('undefined_action', NULL);
	} else {
		if(!$_G['collection']['ctid'] || !checkcollectionperm($_G['collection'], $_G['uid']) || empty($_GET['delcomment']) || !is_array($_GET['delcomment']) || count($_GET['delcomment']) == 0) {
			showmessage('undefined_action', NULL);
		}
		$delrows = table_forum_collectioncomment::t()->delete_by_cid_ctid($_GET['delcomment'], $_G['collection']['ctid']);
		table_forum_collection::t()->update_by_ctid($_G['collection']['ctid'], 0, 0, -$delrows);

		showmessage('collection_comment_remove_succ', 'forum.php?mod=collection&action=view&op=comment&ctid='.$ctid);
	}
} elseif($op == 'pop') {
	$collectionthread = table_forum_collectionthread::t()->fetch_by_ctid_tid($ctid, $tid);
	if(!$collectionthread['ctid']) {
		showmessage('collection_permission_deny');
	}
	$thread = table_forum_thread::t()->fetch_thread($tid);

	include template('forum/collection_commentpop');
} elseif($op == 'recommend') {
	if(!$_G['collection']['ctid']) {
		showmessage('collection_permission_deny');
	}
	if(!submitcheck('formhash')) {
		include template('forum/collection_recommend');
	} else {
		if(!$_GET['threadurl']) {
			showmessage('collection_recommend_url', '', [], ['alert' => 'error', 'closetime' => true, 'showdialog' => 1]);
		}

		$touid = &$_G['collection']['uid'];
		$coef = 1;

		$subject = $message = lang('message', 'collection_recommend_message', ['fromuser' => $_G['username'], 'collectioname' => $_G['collection']['name'], 'url' => $_GET['threadurl']]);
		if(table_home_blacklist::t()->count_by_uid_buid($touid, $_G['uid'])) {
			showmessage('is_blacklist', '', [], ['return' => true]);
		}
		if(($value = getuserbyuid($touid))) {
			require_once libfile('function/friend');
			$value['onlyacceptfriendpm'] = $value['onlyacceptfriendpm'] ? $value['onlyacceptfriendpm'] : ($_G['setting']['onlyacceptfriendpm'] ? 1 : 2);
			if($_G['group']['allowsendallpm'] || $value['onlyacceptfriendpm'] == 2 || ($value['onlyacceptfriendpm'] == 1 && friend_check($touid))) {
				$return = sendpm($touid, $subject, $message, '', 0, 0);
			} else {
				showmessage('message_can_not_send_onlyfriend', '', [], ['return' => true]);
			}
		} else {
			showmessage('message_bad_touid', '', [], ['return' => true]);
		}

		if($return > 0) {
			include_once libfile('function/stat');
			updatestat('sendpm', 0, $coef);

			table_common_member_status::t()->update($_G['uid'], ['lastpost' => TIMESTAMP], 'UNBUFFERED');
			!($_G['group']['exempt'] & 1) && updatecreditbyaction('sendpm', 0, [], '', $coef);
			showmessage('collection_recommend_succ', '', [], ['alert' => 'right', 'closetime' => true, 'showdialog' => 1]);
		}
	}
}

