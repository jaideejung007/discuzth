<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($postusers) {
	$member_verify = $member_field_forum = $member_status = $member_count = $member_profile = $member_field_home = [];
	$uids = array_keys($postusers);
	$uids = array_filter($uids);

	$selfuids = $uids;
	if($_G['setting']['threadblacklist'] && $_G['uid'] && !in_array($_G['uid'], $selfuids)) {
		$selfuids[] = $_G['uid'];
	}
	if(!(getglobal('setting/threadguestlite') && !$_G['uid'])) {
		if($_G['setting']['verify']['enabled']) {
			$member_verify = table_common_member_verify::t()->fetch_all($uids);
			foreach($member_verify as $uid => $data) {
				foreach($_G['setting']['verify'] as $vid => $verify) {
					if($verify['available'] && $verify['showicon']) {
						if($data['verify'.$vid] == 1) {
							$member_verify[$uid]['verifyicon'][] = $vid;
						} elseif(!empty($verify['unverifyicon'])) {
							$member_verify[$uid]['unverifyicon'][] = $vid;
						}
					}
				}
			}
		}
		$member_count = table_common_member_count::t()->fetch_all($selfuids);
		$member_status = table_common_member_status::t()->fetch_all($uids);
		$member_field_forum = table_common_member_field_forum::t()->fetch_all($uids);
		$member_profile = table_common_member_profile::t()->fetch_all($uids);
		$member_field_home = table_common_member_field_home::t()->fetch_all($uids);
	}

	if($_G['setting']['threadblacklist'] && $_G['uid'] && $member_count[$_G['uid']]['blacklist']) {
		$member_blackList = table_home_blacklist::t()->fetch_all_by_uid_buid($_G['uid'], $uids);
	}

	foreach(table_common_member::t()->fetch_all($uids) as $uid => $postuser) {
		$member_field_home[$uid]['privacy'] = empty($member_field_home[$uid]['privacy']) ? [] : dunserialize($member_field_home[$uid]['privacy']);
		$postuser['memberstatus'] = $postuser['status'];
		$postuser['authorinvisible'] = $member_status[$uid]['invisible'];
		$postuser['signature'] = $member_field_forum[$uid]['sightml'];
		unset($member_field_home[$uid]['privacy']['feed'], $member_field_home[$uid]['privacy']['view'], $postuser['status'], $member_status[$uid]['invisible'], $member_field_forum[$uid]['sightml']);
		$postusers[$uid] = array_merge((isset($member_verify[$uid]) ? (array)$member_verify[$uid] : []), (array)$member_field_home[$uid], (array)$member_profile[$uid], (array)$member_count[$uid], (array)$member_status[$uid], (array)$member_field_forum[$uid], $postuser);
		if($postusers[$uid]['regdate'] + $postusers[$uid]['oltime'] * 3600 > TIMESTAMP) {
			$postusers[$uid]['oltime'] = 0;
		}
		$postusers[$uid]['office'] = $postusers[$uid]['position'];
		$postusers[$uid]['inblacklist'] = !empty($member_blackList[$uid]);
		$postusers[$uid]['groupcolor'] = $_G['cache']['usergroups'][$postuser['groupid']]['color'];
		unset($postusers[$uid]['position']);
	}
	unset($member_field_forum, $member_status, $member_count, $member_profile, $member_field_home, $member_blackList);
	$_G['medal_list'] = [];
	foreach($postlist as $pid => $post) {
		if(getstatus($post['status'], 6)) {
			$locationpids[] = $pid;
		}
		$postusers[$post['authorid']]['field_position'] = $postusers[$post['authorid']]['position'];
		if(!defined('IN_RESTFUL')) {
			$post = array_merge($postlist[$pid], (array)$postusers[$post['authorid']]);
		} else {
			$post['username'] = $post['author'];
		}
		$postlist[$pid] = viewthread_procpost($post, $_G['member']['lastvisit'], $ordertype, $maxposition);
	}
}

if($_G['allblocked']) {
	$_G['blockedpids'] = [];
}

if($locationpids) {
	$locations = table_forum_post_location::t()->fetch_all($locationpids);
}

if($postlist && !empty($rushids)) {
	foreach($postlist as $pid => $post) {
		$post['number'] = $post['position'];
		$postlist[$pid] = checkrushreply($post);
	}
}

if($_G['setting']['repliesrank'] && $postlist) {
	if($postlist) {
		foreach(table_forum_hotreply_number::t()->fetch_all_by_pids(array_keys($postlist)) as $pid => $post) {
			$postlist[$pid]['postreview']['support'] = dintval($post['support']);
			$postlist[$pid]['postreview']['against'] = dintval($post['against']);
		}
	}
}

