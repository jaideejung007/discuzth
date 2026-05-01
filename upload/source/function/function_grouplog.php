<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function updategroupcreditlog($fid, $uid) {
	global $_G;
	if(empty($fid) || empty($uid)) {
		return false;
	}
	$today = date('Ymd', TIMESTAMP);
	$updategroupcredit = getcookie('groupcredit_'.$fid);
	if($updategroupcredit < $today) {
		$status = table_forum_groupcreditslog::t()->check_logdate($fid, $uid, $today);
		if(empty($status)) {
			table_forum_forum::t()->update_commoncredits($fid);
			table_forum_groupcreditslog::t()->insert(['fid' => $fid, 'uid' => $uid, 'logdate' => $today], false, true);
			if(empty($_G['forum']) || empty($_G['forum']['level'])) {
				$forum = table_forum_forum::t()->fetch($fid);
				$forum = ['name' => $forum['name'], 'level' => $forum['level'], 'commoncredits' => $forum['commoncredits']];
			} else {
				$_G['forum']['commoncredits']++;
				$forum = &$_G['forum'];
			}
			if(empty($_G['grouplevels'])) {
				loadcache('grouplevels');
			}
			$grouplevel = $_G['grouplevels'][$forum['level']];

			if($grouplevel['type'] == 'default' && !($forum['commoncredits'] >= $grouplevel['creditshigher'] && $forum['commoncredits'] < $grouplevel['creditslower'])) {
				$levelinfo = table_forum_grouplevel::t()->fetch_by_credits($forum['commoncredits']);
				$levelid = $levelinfo['levelid'];
				if(!empty($levelid)) {
					table_forum_forum::t()->update_group_level($levelid, $fid);
					$query = table_forum_forumfield::t()->fetch($fid);
					$groupfounderuid = $query['founderuid'];
					notification_add($groupfounderuid, 'system', 'grouplevel_update', [
						'groupname' => '<a href="forum.php?mod=group&fid='.$fid.'">'.$forum['name'].'</a>',
						'newlevel' => $_G['grouplevels'][$levelid]['leveltitle'],
						'from_id' => 0,
						'from_idtype' => 'changeusergroup'
					]);
				}
			}
		}
		dsetcookie('groupcredit_'.$fid, $today, 86400);
	}
}