<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

dsetcookie('discuz_recommend', '', -1, 0);
if(empty($_G['uid'])) {
	showmessage('to_login', null, [], ['showmsg' => true, 'login' => 1]);
}

if(empty($_GET['hash']) || $_GET['hash'] != formhash()) {
	showmessage('submit_invalid');
}
if(!$_G['setting']['recommendthread']['status'] || !$_G['group']['allowrecommend']) {
	showmessage('no_privilege_recommend');
}

if($thread['authorid'] == $_G['uid'] && !$_G['setting']['recommendthread']['ownthread']) {
	showmessage('recommend_self_disallow', '', ['recommendc' => $thread['recommends']], ['msgtype' => 3]);
}
if(table_forum_memberrecommend::t()->fetch_by_recommenduid_tid($_G['uid'], $_G['tid'])) {
	showmessage('recommend_duplicate', '', ['recommendc' => $thread['recommends']], ['msgtype' => 3]);
}

$recommendcount = table_forum_memberrecommend::t()->count_by_recommenduid_dateline($_G['uid'], $_G['timestamp'] - 86400);
if($_G['setting']['recommendthread']['daycount'] && $recommendcount >= $_G['setting']['recommendthread']['daycount']) {
	showmessage('recommend_outoftimes', '', ['recommendc' => $thread['recommends']], ['msgtype' => 3]);
}

$_G['group']['allowrecommend'] = intval($_GET['do'] == 'add' ? $_G['group']['allowrecommend'] : -$_G['group']['allowrecommend']);
$fieldarr = [];
if($_GET['do'] == 'add') {
	$heatadd = 'recommend_add=recommend_add+1';
	$fieldarr['recommend_add'] = 1;
} else {
	$heatadd = 'recommend_sub=recommend_sub+1';
	$fieldarr['recommend_sub'] = 1;
}

update_threadpartake($_G['tid']);
$fieldarr['heats'] = 0;
$fieldarr['recommends'] = $_G['group']['allowrecommend'];
table_forum_thread::t()->increase($_G['tid'], $fieldarr);
if(empty($thread['closed'])) {
	table_forum_thread::t()->update($_G['tid'], ['lastpost' => TIMESTAMP]);
}
table_forum_memberrecommend::t()->insert(['tid' => $_G['tid'], 'recommenduid' => $_G['uid'], 'dateline' => $_G['timestamp']]);

dsetcookie('recommend', 1, 43200);
$recommendv = $_G['group']['allowrecommend'] > 0 ? '+'.$_G['group']['allowrecommend'] : $_G['group']['allowrecommend'];
if($_G['setting']['recommendthread']['daycount']) {
	$daycount = $_G['setting']['recommendthread']['daycount'] - $recommendcount;
	showmessage('recommend_daycount_succeed', '', ['recommendv' => $recommendv, 'recommendc' => $thread['recommends'], 'daycount' => $daycount], ['msgtype' => 3]);
} else {
	showmessage('recommend_succeed', '', ['recommendv' => $recommendv, 'recommendc' => $thread['recommends']], ['msgtype' => 3]);
}
	