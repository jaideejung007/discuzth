<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function friend_list($uid, $limit, $start = 0) {
	$list = [];
	$query = table_home_friend::t()->fetch_all_by_uid($uid, $start, $limit, true);
	foreach($query as $value) {
		$list[$value['fuid']] = $value;
	}
	return $list;
}

function friend_group_list() {
	global $_G;

	$space = ['uid' => $_G['uid']];
	space_merge($space, 'field_home');

	$groups = [];
	$spacegroup = empty($space['privacy']['groupname']) ? [] : $space['privacy']['groupname'];
	for($i = 0; $i < $_G['setting']['friendgroupnum']; $i++) {
		if($i == 0) {
			$groups[0] = lang('friend', 'friend_group_default');
		} else {
			if(!empty($spacegroup[$i])) {
				$groups[$i] = $spacegroup[$i];
			} else {
				if($i < 8) {
					$groups[$i] = lang('friend', 'friend_group_'.$i);
				} else {
					$groups[$i] = lang('friend', 'friend_group_more', ['num' => $i]);
				}
			}
		}
	}
	return $groups;
}

function friend_check($touids, $isfull = 0) {
	global $_G;

	if(empty($_G['uid'])) return false;
	if(is_array($touids)) {
		$query = table_home_friend::t()->fetch_all_by_uid_fuid($_G['uid'], $touids);

		foreach($query as $value) {
			$touid = $value['fuid'];
			$var = "home_friend_{$_G['uid']}_{$touid}";
			$fvar = "home_friend_{$touid}_{$_G['uid']}";
			$_G[$var] = $_G[$fvar] = true;
			if($isfull) {
				$fvarinfo = "home_friend_info_{$touid}_{$_G['uid']}";
				$_G[$fvarinfo] = $value;
			}
		}

		if(count($query) != count($touids)) {
			return false;
		} else {
			return true;
		}
	} else {
		$touid = $touids;
		$var = "home_friend_{$_G['uid']}_{$touid}";
		$fvar = "home_friend_{$touid}_{$_G['uid']}";
		if(!isset($_G[$var])) {
			$query = table_home_friend::t()->fetch_all_by_uid_fuid($_G['uid'], $touid);
			$friend = $query[0] ?? '';
			if($friend) {
				$_G[$var] = $_G[$fvar] = true;
				if($isfull) {
					$fvarinfo = "home_friend_info_{$touid}_{$_G['uid']}";
					$_G[$fvarinfo] = $friend;
				}
			} else {
				$_G[$var] = $_G[$fvar] = false;
			}
		}
		return $_G[$var];
	}

}

function friend_request_check($touid) {
	global $_G;

	$var = "home_friend_request_{$touid}";
	if(!isset($_G[$var])) {
		$result = table_home_friend_request::t()->fetch_by_uid_fuid($_G['uid'], $touid);
		$_G[$var] = (bool)$result;
	}
	return $_G[$var];
}

function friend_add($touid, $gid = 0, $note = '') {
	global $_G;

	if($touid == $_G['uid']) return -2;
	if(friend_check($touid)) return -2;

	include_once libfile('function/stat');
	$freind_request = table_home_friend_request::t()->fetch_by_uid_fuid($_G['uid'], $touid);
	if($freind_request) {
		$setarr = [
			'uid' => $_G['uid'],
			'fuid' => $freind_request['fuid'],
			'fusername' => addslashes($freind_request['fusername']),
			'gid' => $gid,
			'dateline' => $_G['timestamp']
		];
		table_home_friend::t()->insert($setarr);

		friend_request_delete($touid);

		friend_cache($_G['uid']);

		$setarr = [
			'uid' => $touid,
			'fuid' => $_G['uid'],
			'fusername' => $_G['username'],
			'gid' => $freind_request['gid'],
			'dateline' => $_G['timestamp']
		];
		table_home_friend::t()->insert($setarr);

		addfriendlog($_G['uid'], $touid);
		friend_cache($touid);
		updatestat('friend');
	} else {

		$to_freind_request = table_home_friend_request::t()->fetch_by_uid_fuid($touid, $_G['uid']);
		if($to_freind_request) {
			return -1;
		}

		$setarr = [
			'uid' => $touid,
			'fuid' => $_G['uid'],
			'fusername' => $_G['username'],
			'gid' => $gid,
			'note' => $note,
			'dateline' => $_G['timestamp']
		];
		table_home_friend_request::t()->insert($setarr);

		updatestat('addfriend');
	}

	return 1;
}

function friend_make($touid, $tousername, $checkrequest = true) {
	global $_G;

	if($touid == $_G['uid']) return false;

	if($checkrequest) {
		$to_freind_request = table_home_friend_request::t()->fetch_by_uid_fuid($touid, $_G['uid']);
		if($to_freind_request) {
			table_home_friend_request::t()->delete_by_uid_fuid($touid, $_G['uid']);
		}

		$to_freind_request = table_home_friend_request::t()->fetch_by_uid_fuid($_G['uid'], $touid);
		if($to_freind_request) {
			table_home_friend_request::t()->delete_by_uid_fuid($_G['uid'], $touid);
		}
	}


	$insertarray = [
		'uid' => $touid,
		'fuid' => $_G['uid'],
		'fusername' => $_G['username'],
		'dateline' => $_G['timestamp'],
	];
	table_home_friend::t()->insert($insertarray, false, true);

	$insertarray = [
		'uid' => $_G['uid'],
		'fuid' => $touid,
		'fusername' => $tousername,
		'dateline' => $_G['timestamp'],
	];
	table_home_friend::t()->insert($insertarray, false, true);

	addfriendlog($_G['uid'], $touid);
	include_once libfile('function/stat');
	updatestat('friend');
	friend_cache($touid);
	friend_cache($_G['uid']);
}

function addfriendlog($uid, $touid, $action = 'add') {
	global $_G;

	if($uid && $touid) {
		$flog = [
			'uid' => $uid > $touid ? $uid : $touid,
			'fuid' => $uid > $touid ? $touid : $uid,
			'dateline' => $_G['timestamp'],
			'action' => $action
		];
		DB::insert('home_friendlog', $flog, false, true);
		return true;
	}

	return false;

}

function friend_addnum($touid) {
	global $_G;

	if($_G['uid'] && $_G['uid'] != $touid) {
		table_home_friend::t()->update_num_by_uid_fuid(1, $_G['uid'], $touid);
	}
}

function friend_cache($touid) {
	global $_G;

	$tospace = ['uid' => $touid];
	space_merge($tospace, 'field_home');

	$filtergids = empty($tospace['privacy']['filter_gid']) ? [] : $tospace['privacy']['filter_gid'];

	$uids = [];
	$count = 0;
	$fcount = 0;
	$query = table_home_friend::t()->fetch_all_by_uid($touid, 0, 0, true);
	foreach($query as $value) {
		if($value['fuid'] == $touid) continue;
		if($fcount > 200) {
			$count = count($query);
			break;
		} elseif(empty($filtergids) || !in_array($value['gid'], $filtergids)) {
			$uids[] = $value['fuid'];
			$fcount++;
		}
		$count++;
	}
	table_common_member_field_home::t()->update($touid, ['feedfriend' => implode(',', $uids)]);
	table_common_member_count::t()->update($touid, ['friends' => $count]);

}


function friend_request_delete($touid) {
	global $_G;

	return table_home_friend_request::t()->delete_by_uid_fuid($_G['uid'], $touid);
}

function friend_delete($touid) {
	global $_G;

	if(!friend_check($touid)) return false;

	table_home_friend::t()->delete_by_uid_fuid_dual($_G['uid'], $touid);

	if(DB::affected_rows()) {
		addfriendlog($_G['uid'], $touid, 'delete');
		friend_cache($_G['uid']);
		friend_cache($touid);
	}
}

