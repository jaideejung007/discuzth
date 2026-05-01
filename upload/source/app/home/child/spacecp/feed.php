<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['feedstatus']) {
	showmessage('feed_status_off');
}

$feedid = empty($_GET['feedid']) ? 0 : intval($_GET['feedid']);
$page = empty($_GET['page']) ? 0 : intval($_GET['page']);
if($page < 1) $page = 1;

if($feedid) {
	if(!$feed = table_home_feed::t()->fetch_feed('', '', '', $feedid)) {
		showmessage('feed_no_found');
	}
}

if(submitcheck('commentsubmit')) {

	if(empty($feed['id']) || empty($feed['idtype'])) {
		showmessage('non_normal_operation');
	}

	if($feed['idtype'] == 'doid') {

		$_GET['id'] = intval($_POST['cid']);
		$_GET['doid'] = $feed['id'];

		require_once childfile('doing', 'home/spacecp');

	} else {
		$_POST['id'] = $feed['id'];
		$_POST['idtype'] = $feed['idtype'];

		require_once childfile('comment', 'home/spacecp');
	}
}

if($_GET['op'] == 'delete') {
	if(submitcheck('feedsubmit')) {
		require_once libfile('function/delete');
		if(deletefeeds([$feedid])) {
			showmessage('do_success', dreferer(), ['feedid' => $feedid]);
		} else {
			showmessage('no_privilege_feed_del');
		}
	}
} elseif($_GET['op'] == 'ignore') {

	$icon = empty($_GET['icon']) ? '' : preg_replace('/[^0-9a-zA-Z\_\-\.]/', '', $_GET['icon']);
	if(submitcheck('feedignoresubmit')) {
		$uid = empty($_POST['uid']) ? 0 : intval($_POST['uid']);
		if($icon) {
			$icon_uid = $icon.'|'.$uid;
			if(empty($space['privacy']['filter_icon']) || !is_array($space['privacy']['filter_icon'])) {
				$space['privacy']['filter_icon'] = [];
			}
			$space['privacy']['filter_icon'][$icon_uid] = $icon_uid;
			privacy_update();
		}
		showmessage('do_success', dreferer(), ['feedid' => $feedid], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
	}
} elseif($_GET['op'] == 'getapp') {

} elseif($_GET['op'] == 'getcomment') {

	if(empty($feed['id']) || empty($feed['idtype'])) {
		showmessage('non_normal_operation');
	}
	$feedid = $feed['feedid'];

	$list = [];
	$multi = '';

	if($feed['idtype'] == 'doid') {

		$_GET['doid'] = $feed['id'];
		require_once childfile('doing', 'home/spacecp');

	} else {

		$perpage = 5;
		$start = ($page - 1) * $perpage;

		ckstart($start, $perpage);
		$count = table_home_comment::t()->count_by_id_idtype($feed['id'], $feed['idtype']);
		if($count) {
			$query = table_home_comment::t()->fetch_all_by_id_idtype($feed['id'], $feed['idtype'], $start, $perpage);
			foreach($query as $value) {
				$list[] = $value;
			}
			$multi = multi($count, $perpage, $page, "home.php?mod=spacecp&ac=feed&op=getcomment&feedid=$feedid");
		}


	}
} elseif($_GET['op'] == 'menu') {

	$allowmanage = checkperm('managefeed');
	if(empty($feed['uid'])) {
		showmessage('non_normal_operation');
	}

} else {

	$url = "home.php?mod=space&uid={$feed['uid']}&quickforward=1";
	switch($feed['idtype']) {
		case 'doid':
			$url .= "&do=doing&id={$feed['id']}";
			break;
		case 'blogid':
			$url .= "&do=blog&id={$feed['id']}";
			break;
		case 'picid':
			$url .= "&do=album&picid={$feed['id']}";
			break;
		case 'albumid':
			$url .= "&do=album&id={$feed['id']}";
			break;
		case 'sid':
			$url .= "&do=share&id={$feed['id']}";
			break;
		default:
			break;
	}
	dheader('location:'.$url);
}

include template('home/spacecp_feed');

