<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['tid'] || !$_GET['pid'] || !submitcheck('bestanswersubmit')) {
	showmessage('undefined_action');
}
$forward = 'forum.php?mod=viewthread&tid='.$_G['tid'].($_GET['from'] ? '&from='.$_GET['from'] : '');
$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid'], false);
if($post['tid'] != $_G['tid']) {
	$post = [];
}

if(!($thread['special'] == 3 && $post && ($_G['forum']['ismoderator'] && (!$_G['setting']['rewardexpiration'] || $_G['setting']['rewardexpiration'] > 0 && ($_G['timestamp'] - $thread['dateline']) / 86400 > $_G['setting']['rewardexpiration']) || $thread['authorid'] == $_G['uid']) && $post['authorid'] != $thread['authorid'] && $post['first'] == 0 && $_G['uid'] != $post['authorid'] && $thread['price'] > 0)) {
	showmessage('reward_cant_operate');
} elseif($post['authorid'] == $thread['authorid']) {
	showmessage('reward_cant_self');
} elseif($thread['price'] < 0) {
	showmessage('reward_repeat_selection');
}
updatemembercount($post['authorid'], [$_G['setting']['creditstransextra'][2] => $thread['price']], 1, 'RAC', $_G['tid']);
$thread['price'] = '-'.$thread['price'];
table_forum_thread::t()->update($_G['tid'], ['price' => $thread['price']]);
table_forum_post::t()->update_post('tid:'.$_G['tid'], $_GET['pid'], [
	'dateline' => $thread['dateline'] + 1,
	'bestanswer' => 1,
]);

$thread['dateline'] = dgmdate($thread['dateline']);
if($_G['uid'] != $thread['authorid']) {
	notification_add($thread['authorid'], 'reward', 'reward_question', [
		'tid' => $thread['tid'],
		'subject' => $thread['subject'],
	]);
}
if($thread['authorid'] == $_G['uid']) {
	notification_add($post['authorid'], 'reward', 'reward_bestanswer', [
		'tid' => $thread['tid'],
		'subject' => $thread['subject'],
	]);
} else {
	notification_add($post['authorid'], 'reward', 'reward_bestanswer_moderator', [
		'tid' => $thread['tid'],
		'subject' => $thread['subject'],
	]);
}

showmessage('reward_completion', $forward);
