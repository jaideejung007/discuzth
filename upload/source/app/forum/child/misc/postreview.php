<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['repliesrank'] || empty($_G['uid'])) {
	showmessage('to_login', null, [], ['showmsg' => true, 'login' => 1]);
}
if(empty($_GET['hash']) || $_GET['hash'] != formhash()) {
	showmessage('submit_invalid');
}

$doArray = ['support', 'against'];

$post = table_forum_post::t()->fetch_post('tid:'.$_GET['tid'], $_GET['pid'], false);

if(!in_array($_GET['do'], $doArray) || empty($post) || $post['first'] == 1 || ($_G['setting']['threadfilternum'] && $_G['setting']['filterednovote'] && getstatus($post['status'], 11)) || $post['invisible'] < 0) {
	showmessage('undefined_action', NULL);
}

$hotreply = table_forum_hotreply_number::t()->fetch_by_pid($post['pid']);
if($_G['uid'] == $post['authorid']) {
	showmessage('noreply_yourself_error', '', [], ['msgtype' => 3]);
}

if(empty($hotreply)) {
	$hotreply['pid'] = table_forum_hotreply_number::t()->insert([
		'pid' => $post['pid'],
		'tid' => $post['tid'],
		'support' => 0,
		'against' => 0,
		'total' => 0,
	], true);
} else {
	if(table_forum_hotreply_member::t()->fetch_member($post['pid'], $_G['uid'])) {
		showmessage('noreply_voted_error', '', [], ['msgtype' => 3]);
	}
}

$typeid = $_GET['do'] == 'support' ? 1 : 0;

table_forum_hotreply_number::t()->update_num($post['pid'], $typeid);
table_forum_hotreply_member::t()->insert([
	'tid' => $post['tid'],
	'pid' => $post['pid'],
	'uid' => $_G['uid'],
	'attitude' => $typeid,
]);

$hotreply[$_GET['do']]++;

showmessage('thread_poll_succeed', '', [], ['msgtype' => 3, 'extrajs' => '<script type="text/javascript">postreviewupdate('.$post['pid'].', '.$typeid.');</script>']);
	