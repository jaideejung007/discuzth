<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['formhash'] != FORMHASH) {
	showmessage('undefined_action', NULL);
}
$formula = dunserialize($_G['forum']['formulaperm']);
if(empty($formula['viewtype'])) {
	showmessage('undefined_action', NULL);
}

if(!empty($formula['viewtype_gids']) && !in_array($_G['groupid'], $formula['viewtype_gids'])) {
	showmessage('undefined_action', NULL);
}
$joinuser = table_forum_groupuser::t()->fetch_userinfo($_G['uid'], $formula['viewtype_fid']);
if(!$joinuser) {
	table_forum_groupuser::t()->insert_groupuser($formula['viewtype_fid'], $_G['uid'], $_G['username'], 0, time());
}

$moderators = table_forum_moderator::t()->fetch_all_by_fid($_G['fid']);
$uids = array_keys($moderators);
foreach($moderators as $uid => $moderator) {
	notification_add($uid, 'system', 'forum_member_new', ['fid' => $_G['fid'], 'forumname' => $_G['forum']['name'], 'url' => $_G['siteurl'].'forum.php?mod=modcp&action=forum&op=member&fid='.$_G['fid']], 1);
}

showmessage('forum_member_apply_succeed', $_G['siteurl']);
	