<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['forumstatus']) {
	showmessage('forum_status_off');
}

$minhot = $_G['setting']['feedhotmin'] < 1 ? 3 : $_G['setting']['feedhotmin'];
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
if($page < 1) $page = 1;
$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
$opactives['debate'] = 'class="a"';

$_GET['view'] = in_array($_GET['view'], ['we', 'me', 'all']) ? $_GET['view'] : 'we';
$_GET['order'] = in_array($_GET['order'], ['hot', 'dateline']) ? $_GET['order'] : 'dateline';
$perpage = 20;
$perpage = mob_perpage($perpage);
$start = ($page - 1) * $perpage;
ckstart($start, $perpage);

$list = $userlist = [];
$count = $pricount = 0;

$gets = [
	'mod' => 'space',
	'uid' => $space['uid'],
	'do' => 'debate',
	'view' => $_GET['view'],
	'order' => $_GET['order'],
	'type' => $_GET['type'],
	'fuid' => $_GET['fuid'],
	'searchkey' => $_GET['searchkey']
];
$theurl = 'home.php?'.url_implode($gets);
$multi = '';


$f_index = '';
$ordersql = 't.dateline DESC';
$need_count = true;
$join = $authorid = $replies = 0;
$displayorder = null;
$subject = '';

if($_GET['view'] == 'me') {

	if($_GET['type'] == 'reply') {
		$authorid = $space['uid'];
		$join = true;
	} else {
		$authorid = $space['uid'];
	}
	$viewtype = in_array($_GET['type'], ['orig', 'reply']) ? $_GET['type'] : 'orig';
	$typeactives = [$viewtype => ' class="a"'];

} else {

	space_merge($space, 'field_home');

	if($space['feedfriend']) {

		$fuid_actives = [];

		require_once libfile('function/friend');
		$fuid = intval($_GET['fuid']);
		if($fuid && friend_check($fuid, $space['uid'])) {
			$authorid = $fuid;
			$fuid_actives = [$fuid => ' selected'];
		} else {
			$authorid = explode(',', $space['feedfriend']);
			$theurl = "home.php?mod=space&uid={$space['uid']}&do=$do&view=we";
		}

		$query = table_home_friend::t()->fetch_all_by_uid($space['uid'], 0, 100, true);
		foreach($query as $value) {
			$userlist[] = $value;
		}
	} else {
		$need_count = false;
	}
}

$actives = [$_GET['view'] => ' class="a"'];

if($need_count) {

	if($_GET['view'] != 'me') {
		$displayorder = 0;
	}
	if($searchkey = stripsearchkey($_GET['searchkey'])) {
		$subject = $searchkey;
		$searchkey = dhtmlspecialchars($searchkey);
	}

	$count = table_forum_thread::t()->count_by_special(5, $authorid, $replies, $displayorder, $subject, $join);
	if($count) {

		$dids = $special = $multitable = $tids = [];
		require_once libfile('function/post');
		foreach(table_forum_thread::t()->fetch_all_by_special(5, $authorid, $replies, $displayorder, $subject, $join, $start, $perpage) as $value) {
			$value['dateline'] = dgmdate($value['dateline']);
			if($_GET['view'] == 'me' && $_GET['type'] == 'reply' && $page == 1 && count($special) < 2) {
				$value['message'] = messagecutstr($value['message'], 200);
				$special[$value['tid']] = $value;
			} else {
				if($page == 1 && count($special) < 2) {
					$tids[$value['posttableid']][$value['tid']] = $value['tid'];
					$special[$value['tid']] = $value;
				} else {
					$list[$value['tid']] = $value;
				}
			}
			$dids[$value['tid']] = $value['tid'];
		}
		if($tids) {
			foreach($tids as $postid => $tid) {
				foreach(table_forum_post::t()->fetch_all_by_tid(0, $tid) as $value) {
					$special[$value['tid']]['message'] = messagecutstr($value['message'], 200);
				}
			}
		}
		if($dids) {
			foreach(table_forum_debate::t()->fetch_all($dids) as $value) {
				$value['negavotesheight'] = $value['affirmvotesheight'] = '8px';
				if($value['affirmvotes'] || $value['negavotes']) {
					$allvotes = $value['affirmvotes'] + $value['negavotes'];
					$value['negavotesheight'] = round($value['negavotes'] / $allvotes * 100, 2).'%';
					$value['affirmvotesheight'] = round($value['affirmvotes'] / $allvotes * 100, 2).'%';
				}
				if($list[$value['tid']]) {
					$list[$value['tid']] = array_merge($value, $list[$value['tid']]);
				} elseif($special[$value['tid']]) {
					$special[$value['tid']] = array_merge($value, $special[$value['tid']]);
				}
			}
		}

		$multi = multi($count, $perpage, $page, $theurl);

	}

}


if($_G['uid']) {
	if($_GET['view'] == 'all') {
		$navtitle = lang('core', 'title_view_all').lang('core', 'title_debate');
	} elseif($_GET['view'] == 'me') {
		$navtitle = lang('core', 'title_my_debate');
	} else {
		$navtitle = lang('core', 'title_friend_debate');
	}
} else {
	if($_GET['order'] == 'hot') {
		$navtitle = lang('core', 'title_top_debate');
	} else {
		$navtitle = lang('core', 'title_newest_debate');
	}
}

include_once template('diy:home/space_debate');

