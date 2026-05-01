<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include_once libfile('function/home');
$perpage = 8;
$page = max(1, $_GET['page']);
$start_limit = ($page - 1) * $perpage;
$aid = intval($_GET['aid']);
$photolist = [];
$query = table_home_album::t()->fetch_all_by_uid($_G['uid'], false, 0, 0, $aid);
$count = $query[0]['picnum'];
$albuminfo = table_home_album::t()->fetch_album($aid, $_G['uid']);
if(empty($albuminfo)) {
	showmessage('to_view_the_photo_does_not_exist');
}
$query = table_home_pic::t()->fetch_all_by_albumid($aid, $start_limit, $perpage, 0, 0, 1);
foreach($query as $value) {
	$value['bigpic'] = pic_get($value['filepath'], 'album', $value['thumb'], $value['remote'], 0);
	$value['pic'] = pic_get($value['filepath'], 'album', $value['thumb'], $value['remote']);
	$value['count'] = $count;
	$value['url'] = (preg_match('/^https?:\/\//is', $value['bigpic']) ? '' : $_G['siteurl']).$value['bigpic'];
	$value['thumburl'] = (preg_match('/^https?:\/\//is', $value['pic']) ? '' : $_G['siteurl']).$value['pic'];
	$photolist[] = $value;
}
$_GET['ajaxtarget'] = $_GET['ajaxtarget'] ?? 'albumphoto';
$multi = multi($count, $perpage, $page, "forum.php?mod=post&action=albumphoto&aid=$aid".($_GET['from'] ? '&from=albumWin' : ''));
include template('forum/ajax_albumlist');
exit;