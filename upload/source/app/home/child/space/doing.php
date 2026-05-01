<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['doingstatus']) {
	showmessage('doing_status_off');
}



require_once libfile('function/doing');
$perpage = 20;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page']) ? 0 : intval($_GET['page']);
if($page < 1) $page = 1;
$start = ($page - 1) * $perpage;


$comment_perpage = 10;
$comment_page = empty($_GET['page_c']) ? 0 : intval($_GET['page_c']);
if($comment_page < 1) $comment_page = 1;
$comment_start = ($comment_page - 1) * $comment_perpage;

ckstart($start, $perpage);

$dolist = [];
$count = 0;

$_GET['view'] = in_array($_GET['view'], ['follow', 'we', 'me', 'all']) ? $_GET['view'] : 'all';

$gets = [
	'mod' => 'space',
	'uid' => $space['uid'],
	'do' => 'doing',
	'view' => $_GET['view'],
	'searchkey' => $_GET['searchkey'],
	'from' => $_GET['from'],
	'tagid' => $_GET['tagid'],
];
$theurl = 'home.php?'.url_implode($gets);

$f_index = '';
$diymode = 0;
if($_GET['view'] == 'all') {

	$f_index = 'dateline';

} elseif($_GET['view'] == 'we') {

	space_merge($space, 'field_home');
	if($space['feedfriend']) {
		$uids = array_merge(explode(',', $space['feedfriend']), [$space['uid']]);
		$f_index = 'dateline';
	} else {
		$uids = [$space['uid']];
	}
} elseif($_GET['view'] == 'follow') {
	$uids[] = $space['uid'];
	if($_GET['viewtype'] == 'special') {
		$followusers = table_home_follow::t()->fetch_all_following_by_uid($space['uid'], 1);
	} else {
		$followusers = table_home_follow::t()->fetch_all_following_by_uid($space['uid']);
	}
	foreach($followusers as $followuser) {
		$uids[] = $followuser['followuid'];
	}
} else {

	if($_GET['from'] == 'space') $diymode = 1;

	$uids = $_GET['highlight'] ? [] : [$space['uid']];
}
$actives = [$_GET['view'] => ' class="a"'];

$tagid = empty($_GET['tagid']) ? 0 : intval($_GET['tagid']);
$doid = empty($_GET['doid']) ? 0 : intval($_GET['doid']);
$all_doids = $clist = $newdoids = [];
$pricount = 0;
if($doid) {
	$op = 'view';
	$count = 1;
	$f_index = '';
	$theurl .= "&doid=$doid";
}
if($tagid) {
	$doid = [];
	$tag = table_common_tag::t()->fetch_info($tagid);
	if(empty($tag['tagid'])) {
		showmessage('article_does_not_exist');
	}
	if($tag['status'] == 1) {
		showmessage('tag_closed');
	}
	$tagname = $tag['tagname'];
	$count = table_common_tagitem::t()->select($tagid, 0, 'doid', '', '', 0, 0, 0, 1);
	$query = table_common_tagitem::t()->select($tagid, 0, 'doid', '', '', $start, $perpage);
	foreach($query as $result) {
		$doid[] = $result['itemid'];
	}
}

if($searchkey = stripsearchkey($_GET['searchkey'])) {
	$searchkey = dhtmlspecialchars($searchkey);
}

if(empty($count)) {
	$count = table_home_doing::t()->fetch_all_search($start, $perpage, 3, $uids, '', $searchkey, '', '', '', 1, $doid, $f_index);
}

if($count) {
	$query = table_home_doing::t()->fetch_all_search($start, $perpage, 1, $uids, '', $searchkey, '', '', '', 1, $doid, $f_index);
	
	foreach($query as $value) {
		if($value['status'] == 0 || $value['uid'] == $_G['uid'] || $_G['adminid'] == 1) {
			$all_doids[] = $value['doid'];
		} else {
			$pricount++;
		}
	}
	
	$recommend_status = array();
	if($_G['uid'] && !empty($all_doids)) {
		$recommend_status = table_home_doing_recomend_log::t()->fetch_all_by_doids_uid($all_doids, $_G['uid']);
	}
	foreach($query as $value) {
		$value = domessageformat($value, $recommend_status);
		if($value['status'] == 0 || $value['uid'] == $_G['uid'] || $_G['adminid'] == 1) {
			$dolist[] = $value;
		}
	}
}
if($doid && is_numeric($doid)) {
	$dovalue = empty($dolist) ? [] : $dolist[0];
	if($dovalue) {
		if($dovalue['uid'] == $_G['uid']) {
			$actives = ['me' => ' class="a"'];
		} else {
			$actives = ['all' => ' class="a"'];
		}
	}
}



$attachments = [];
if (!empty($all_doids)) {
	$attach_list = table_home_doing_attachment::t()->fetch_all_by_id(0, 'doid', $all_doids);
	foreach ($attach_list as $attach) {
		$attach['thumb'] = getdiscuzimg('doing', $attach['aid'], 0, 140, 140);
		$attachments[$attach['doid']][] = $attach;
	}
}

foreach ($dolist as &$dv) {
	$dv['attachments'] = isset($attachments[$dv['doid']]) ? $attachments[$dv['doid']] : [];
}
unset($dv);


$clist = [];
$showdoinglist = [];
$comment_multi = [];


$clist = [];
$showdoinglist = [];
$comment_multi = [];


if($doid && is_numeric($doid)) {
	
	$top_comment_count = intval(table_home_docomment::t()->count_top_by_doid($doid));
	
	
	if($top_comment_count > $comment_perpage) {
		$comment_url = "home.php?mod=space&do=doing&doid=$doid&page_c={page}";
		$comment_multi[$doid] = multi($top_comment_count, $comment_perpage, $comment_page, $comment_url);
	}
}

$multi = multi($count, $perpage, $page, $theurl);

dsetcookie('home_diymode', $diymode);
if($_G['uid']) {
	if($_GET['view'] == 'all') {
		$navtitle = lang('core', 'title_view_all').lang('core', 'title_doing');
	} elseif($_GET['view'] == 'me') {
		$navtitle = lang('core', 'title_doing_view_me');
	} else {
		$navtitle = lang('core', 'title_me_friend_doing');
	}
	$defaultstr = getdefaultdoing();
} else {
	$navtitle = lang('core', 'title_newest_doing');
}
if($tagid){
	$navtitle = $tagname.' - '.$navtitle;
}

if($_G['uid'] != $space['uid'] && $space['username']) {
	$navtitle = lang('space', 'sb_doing', ['who' => $space['username']]);
}
$metakeywords = $navtitle;
$metadescription = $navtitle;
include_once template('diy:home/space_doing');