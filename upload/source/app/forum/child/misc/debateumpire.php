<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$debate = table_forum_debate::t()->fetch($_G['tid']);

if(empty($debate)) {
	showmessage('debate_nofound');
} elseif(!empty($thread['closed']) && TIMESTAMP - $debate['endtime'] > 3600) {
	showmessage('debate_umpire_edit_invalid');
} elseif($_G['member']['username'] != $debate['umpire']) {
	showmessage('debate_umpire_nopermission');
}

$debate = array_merge($debate, $thread);

if(!submitcheck('umpiresubmit')) {
	$candidates = [];
	$uids = [];
	$voters = table_forum_debatepost::t()->fetch_all_voters($_G['tid'], 30);
	foreach($voters as $candidate) {
		$uids[] = $candidate['uid'];
	}
	$users = table_common_member::t()->fetch_all_username_by_uid($uids);
	foreach($voters as $candidate) {
		$candidate['username'] = dhtmlspecialchars($users[$candidate['uid']]);
		$candidates[$candidate['username']] = $candidate;
	}
	$winnerchecked = [$debate['winner'] => ' checked="checked"'];

	list($debate['bestdebater']) = preg_split('/\s/', $debate['bestdebater']);

	include template('forum/debate_umpire');
} else {
	if(empty($_GET['bestdebater'])) {
		showmessage('debate_umpire_nofound_bestdebater');
	} elseif(empty($_GET['winner'])) {
		showmessage('debate_umpire_nofound_winner');
	} elseif(empty($_GET['umpirepoint'])) {
		showmessage('debate_umpire_nofound_point');
	}
	$bestdebateruid = table_common_member::t()->fetch_uid_by_username($_GET['bestdebater']);
	if(!$bestdebateruid) {
		showmessage('debate_umpire_bestdebater_invalid');
	}
	if(!($bestdebaterstand = table_forum_debatepost::t()->get_stand_by_bestuid($_G['tid'], $bestdebateruid, [$debate['uid'], $_G['uid']]))) {
		showmessage('debate_umpire_bestdebater_invalid');
	}
	list($bestdebatervoters, $bestdebaterreplies) = table_forum_debatepost::t()->get_numbers_by_bestuid($_G['tid'], $bestdebateruid);

	$umpirepoint = dhtmlspecialchars($_GET['umpirepoint']);
	$bestdebater = dhtmlspecialchars($_GET['bestdebater']);
	$winner = intval($_GET['winner']);
	table_forum_thread::t()->update($_G['tid'], ['closed' => 1]);
	table_forum_debate::t()->update($_G['tid'], ['umpirepoint' => $umpirepoint, 'winner' => $winner, 'bestdebater' => "$bestdebater\t$bestdebateruid\t$bestdebaterstand\t$bestdebatervoters\t$bestdebaterreplies", 'endtime' => $_G['timestamp']]);
	showmessage('debate_umpire_comment_succeed', 'forum.php?mod=viewthread&tid='.$_G['tid'].($_GET['from'] ? '&from='.$_GET['from'] : ''));
}
	