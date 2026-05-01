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

if(empty($_GET['confirm'])) {
	showmessage('forum_member_exit_confirm', '',
		['url' => 'forum.php?mod=member&action=exit&confirm=yes&fid='.$_G['fid'].'&formhash='.formhash()],
		['alert' => 'info']);
}
table_forum_groupuser::t()->delete_by_fid($_G['fid'], $_G['uid']);
showmessage('forum_member_exit', $_G['siteurl']);
	