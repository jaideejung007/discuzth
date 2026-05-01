<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!submitcheck('pollsubmit', 1)) {
	showmessage('undefined_action');
}

if(!$_G['group']['allowvote']) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
} elseif(!empty($thread['closed'])) {
	showmessage('thread_poll_closed', NULL, [], ['login' => 1]);
} elseif(empty($_GET['pollanswers'])) {
	showmessage('thread_poll_invalid', NULL, [], ['login' => 1]);
}

$pollarray = table_forum_poll::t()->fetch($_G['tid']);
$overt = $pollarray['overt'];
if(!$pollarray) {
	showmessage('poll_not_found');
} elseif($pollarray['expiration'] && $pollarray['expiration'] < TIMESTAMP) {
	showmessage('poll_overdue', NULL, [], ['login' => 1]);
} elseif($pollarray['maxchoices'] && $pollarray['maxchoices'] < count($_GET['pollanswers'])) {
	showmessage('poll_choose_most', NULL, ['maxchoices' => $pollarray['maxchoices']], ['login' => 1]);
}

$voterids = $_G['uid'] ? $_G['uid'] : $_G['clientip'];

$polloptionid = [];
$query = table_forum_polloption::t()->fetch_all_by_tid($_G['tid']);
foreach($query as $pollarray) {
	if(strexists("\t".$pollarray['voterids']."\t", "\t".$voterids."\t")) {
		showmessage('thread_poll_voted', NULL, [], ['login' => 1]);
	}
	$polloptionid[] = $pollarray['polloptionid'];
}

$polloptionids = [];
foreach($_GET['pollanswers'] as $key => $id) {
	if(!in_array($id, $polloptionid)) {
		showmessage('parameters_error');
	}
	unset($polloptionid[$key]);
	$polloptionids[] = $id;
}

table_forum_polloption::t()->update_vote($polloptionids, $voterids."\t", 1);
table_forum_thread::t()->update($_G['tid'], ['lastpost' => $_G['timestamp']], true);
table_forum_poll::t()->update_vote($_G['tid']);
table_forum_pollvoter::t()->insert([
	'tid' => $_G['tid'],
	'uid' => $_G['uid'],
	'username' => $_G['username'],
	'options' => implode("\t", $_GET['pollanswers']),
	'dateline' => $_G['timestamp'],
]);
updatecreditbyaction('joinpoll');

$space = [];
space_merge($space, 'field_home');

if($overt && !empty($space['privacy']['feed']['newreply'])) {
	$feed['icon'] = 'poll';
	$feed['title_template'] = 'feed_thread_votepoll_title';
	$feed['title_data'] = [
		'subject' => "<a href=\"forum.php?mod=viewthread&tid={$_G['tid']}\">{$thread['subject']}</a>",
		'author' => "<a href=\"home.php?mod=space&uid={$thread['authorid']}\">{$thread['author']}</a>",
		'hash_data' => "tid{$_G['tid']}"
	];
	$feed['id'] = $_G['tid'];
	$feed['idtype'] = 'tid';
	postfeed($feed);
}

if(!empty($_G['inajax'])) {
	showmessage('thread_poll_succeed', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['location' => true]);
} else {
	showmessage('thread_poll_succeed', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''));
}
	