<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!empty($thread['closed'])) {
	showmessage('thread_poll_closed');
}

if(!$_G['uid']) {
	showmessage('debate_poll_nopermission', NULL, [], ['login' => 1]);
}

$isfirst = empty($_GET['pid']);

$debate = table_forum_debate::t()->fetch($_G['tid']);

if(empty($debate)) {
	showmessage('debate_nofound');
}

if($isfirst) {
	$stand = intval($_GET['stand']);

	if($stand == 1 || $stand == 2) {
		if(str_contains("\t".$debate['affirmvoterids'], "\t{$_G['uid']}\t") || str_contains("\t".$debate['negavoterids'], "\t{$_G['uid']}\t")) {
			showmessage('debate_poll_voted');
		} elseif($debate['endtime'] && $debate['endtime'] < TIMESTAMP) {
			showmessage('debate_poll_end');
		}
	}
	table_forum_debate::t()->update_voters($_G['tid'], $_G['uid'], $stand);

	showmessage('debate_poll_succeed', 'forum.php?mod=viewthread&tid='.$_G['tid'], [], ['showmsg' => 1, 'locationtime' => true]);
}

$debatepost = table_forum_debatepost::t()->fetch($_GET['pid']);
if(empty($debatepost) || $debatepost['tid'] != $_G['tid']) {
	showmessage('debate_nofound');
}
$debate = array_merge($debate, $debatepost);
unset($debatepost);

if($debate['uid'] == $_G['uid']) {
	showmessage('debate_poll_myself', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showmsg' => 1]);
} elseif(str_contains("\t".$debate['voterids'], "\t{$_G['uid']}\t")) {
	showmessage('debate_poll_voted', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showmsg' => 1]);
} elseif($debate['endtime'] && $debate['endtime'] < TIMESTAMP) {
	showmessage('debate_poll_end', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showmsg' => 1]);
}

table_forum_debatepost::t()->update_voters($_GET['pid'], $_G['uid']);

showmessage('debate_poll_succeed', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showmsg' => 1]);
	