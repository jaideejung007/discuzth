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
$_GET['order'] = in_array($_GET['order'], ['hot', 'dateline']) ? $_GET['order'] : 'dateline';
$opactives['poll'] = 'class="a"';

$_GET['view'] = in_array($_GET['view'], ['we', 'me', 'all']) ? $_GET['view'] : 'we';

$perpage = 20;
$perpage = mob_perpage($perpage);
$start = ($page - 1) * $perpage;
ckstart($start, $perpage);

$list = [];
$userlist = [];
$count = $pricount = 0;

$gets = [
	'mod' => 'space',
	'uid' => $space['uid'],
	'do' => 'poll',
	'view' => $_GET['view'],
	'order' => $_GET['order'],
	'fuid' => $_GET['fuid'],
	'filter' => $_GET['filter'],
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

	$filter = in_array($_GET['filter'], ['publish', 'join']) ? $_GET['filter'] : 'publish';
	if($filter == 'join') {
		$join = true;
		$authorid = $space['uid'];
	} else {
		$authorid = $space['uid'];
	}
	$filteractives = [$filter => ' class="a"'];

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

	$count = table_forum_thread::t()->count_by_special(1, $authorid, $replies, $displayorder, $subject, $join);
	if($count) {

		loadcache('forums');
		$tids = [];
		require_once libfile('function/misc');
		foreach(table_forum_thread::t()->fetch_all_by_special(1, $authorid, $replies, $displayorder, $subject, $join, $start, $perpage) as $value) {
			if(empty($value['author']) && $value['authorid'] != $_G['uid']) {
				$hiddennum++;
				continue;
			}
			$tids[$value['tid']] = $value['tid'];
			$list[$value['tid']] = procthread($value);
		}
		if($tids) {
			$query = table_forum_poll::t()->fetch_all($tids);
			foreach($query as $value) {
				$value['pollpreview'] = explode("\t", trim($value['pollpreview']));
				$list[$value['tid']]['poll'] = $value;
			}
		}

		$multi = multi($count, $perpage, $page, $theurl);
	}


}

if($_G['uid']) {
	$_GET['view'] = !$_GET['view'] ? 'we' : $_GET['view'];
	$navtitle = lang('core', 'title_'.$_GET['view'].'_poll');
} else {
	$_GET['order'] = !$_GET['order'] ? 'dateline' : $_GET['order'];
	$navtitle = lang('core', 'title_'.$_GET['order'].'_poll');
}

$actives = [$_GET['view'] => ' class="a"'];
include_once template('diy:home/space_poll');

