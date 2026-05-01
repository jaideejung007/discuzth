<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['wallstatus']) {
	showmessage('wall_status_off');
}

$perpage = 20;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
if($page < 1) $page = 1;
$start = ($page - 1) * $perpage;

ckstart($start, $perpage);

$theurl = "home.php?mod=space&uid={$space['uid']}&do=$do";

$diymode = 1;

$cid = empty($_GET['cid']) ? 0 : intval($_GET['cid']);

$list = [];
$count = table_home_comment::t()->count_by_id_idtype($space['uid'], 'uid', $cid);
if($count) {
	$query = table_home_comment::t()->fetch_all_by_id_idtype($space['uid'], 'uid', $start, $perpage, $cid, 'DESC');
	foreach($query as $value) {
		$list[] = $value;
	}
}

$multi = multi($count, $perpage, $page, $theurl);

$navtitle = lang('space', 'sb_wall', ['who' => $space['username']]);
$metakeywords = lang('space', 'sb_wall', ['who' => $space['username']]);
$metadescription = lang('space', 'sb_wall', ['who' => $space['username']]);

dsetcookie('home_diymode', 1);

include_once template('home/space_wall');

