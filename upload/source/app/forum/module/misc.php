<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
const NOROBOT = true;

if(!in_array($_GET['action'], ['paysucceed', 'showdarkroom']) && !$_G['setting']['forumstatus']) {
	showmessage('forum_status_off');
}

$after_actions = ['votepoll', 'viewvote', 'rate', 'removerate',
	'viewratings', 'viewwarning', 'pay', 'viewpayments',
	'viewthreadmod', 'bestanswer', 'activityapplies', 'getactivityapplylist',
	'activityapplylist', 'activityexport', 'tradeorder', 'debatevote',
	'debateumpire', 'recommend', 'protectsort', 'usertag',
	'postreview', 'hidden', 'hiderecover'];

require_once libfile('function/post');

$file = childfile($_GET['action']);
if(!file_exists($file)) {
	showmessage('undefined_action');
}

$feed = [];
if(!in_array($_GET['action'], $after_actions)) {
	require_once $file;
} else {
	if(empty($_G['forum']['allowview'])) {
		if(!$_G['forum']['viewperm'] && !$_G['group']['readaccess']) {
			showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
		} elseif($_G['forum']['viewperm'] && !forumperm($_G['forum']['viewperm'])) {
			showmessage('forum_nopermission', NULL, [$_G['group']['grouptitle']], ['login' => 1]);
		}
	}

	$thread = table_forum_thread::t()->fetch_thread($_G['tid']);
	if(!($thread['displayorder'] >= 0 || $thread['displayorder'] == -4 && $thread['authorid'] == $_G['uid'])) {
		$thread = [];
	}
	if($thread['readperm'] && $thread['readperm'] > $_G['group']['readaccess'] && !$_G['forum']['ismoderator'] && $thread['authorid'] != $_G['uid']) {
		showmessage('thread_nopermission', NULL, ['readperm' => $thread['readperm']], ['login' => 1]);
	}

	if($_G['forum']['password'] && $_G['forum']['password'] != $_G['cookie']['fidpw'.$_G['fid']]) {
		showmessage('forum_passwd', "forum.php?mod=forumdisplay&fid={$_G['fid']}");
	}

	if(!$thread) {
		showmessage('thread_nonexistence');
	}

	if($_G['forum']['type'] == 'forum') {
		$navigation = '<a href="forum.php">'.$_G['setting']['navs'][2]['navname']."</a> <em>&rsaquo;</em> <a href=\"forum.php?mod=forumdisplay&fid={$_G['fid']}\">".$_G['forum']['name']."</a> <em>&rsaquo;</em> <a href=\"forum.php?mod=viewthread&tid={$_G['tid']}\">{$thread['subject']}</a> ";
		$navtitle = strip_tags($_G['forum']['name']).' - '.$thread['subject'];
	} elseif($_G['forum']['type'] == 'sub') {
		$fup = table_forum_forum::t()->fetch($_G['forum']['fup']);
		$navigation = '<a href="forum.php">'.$_G['setting']['navs'][2]['navname']."</a> <em>&rsaquo;</em> <a href=\"forum.php?mod=forumdisplay&fid={$fup['fid']}\">{$fup['name']}</a> &raquo; <a href=\"forum.php?mod=forumdisplay&fid={$_G['fid']}\">".$_G['forum']['name']."</a> <em>&rsaquo;</em> <a href=\"forum.php?mod=viewthread&tid={$_G['tid']}\">{$thread['subject']}</a> ";
		$navtitle = strip_tags($fup['name']).' - '.strip_tags($_G['forum']['name']).' - '.$thread['subject'];
	}
}

require_once $file;

function getratelist($raterange) {
	global $_G;
	$maxratetoday = getratingleft($raterange);

	$ratelist = [];
	foreach($raterange as $id => $rating) {
		if(isset($_G['setting']['extcredits'][$id])) {
			$ratelist[$id] = '';
			$rating['max'] = $rating['max'] < $maxratetoday[$id] ? $rating['max'] : $maxratetoday[$id];
			$rating['min'] = -$rating['min'] < $maxratetoday[$id] ? $rating['min'] : -$maxratetoday[$id];
			$offset = abs(ceil(($rating['max'] - $rating['min']) / 10));
			if($rating['max'] > $rating['min']) {
				for($vote = $rating['max']; $vote >= $rating['min']; $vote -= $offset) {
					$ratelist[$id] .= $vote ? '<li>'.($vote > 0 ? '+'.$vote : $vote).'</li>' : '';
				}
			}
		}
	}
	return $ratelist;
}

function getratingleft($raterange) {
	global $_G;
	$maxratetoday = [];

	foreach($raterange as $id => $rating) {
		$maxratetoday[$id] = $rating['mrpd'];
	}

	foreach(table_forum_ratelog::t()->fetch_all_sum_score($_G['uid'], $_G['timestamp'] - 86400) as $rate) {
		$maxratetoday[$rate['extcredits']] = $raterange[$rate['extcredits']]['mrpd'] - $rate['todayrate'];
	}
	return $maxratetoday;
}

