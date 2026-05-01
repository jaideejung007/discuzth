<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$view = 'follow';
if(in_array($_GET['view'], ['special', 'follow', 'other'])) {
	$view = $_GET['view'];
	$theurl .= '&view='.$_GET['view'];
}

$vuid = $view == 'other' ? 0 : $_G['uid'];
$list = getfollowfeed($vuid, $view, false, $start, $perpage);
if((empty($list['feed']) || !is_array($list['feed']) || count($list['feed']) < 20) && (!empty($list['user']) || $view == 'other')) {
	$primary = 0;
	$alist = getfollowfeed($vuid, $view, true, $start, $perpage);
	if(empty($list['feed']) && empty($alist['feed'])) {
		$showguide = true;
		$archiver = 0;
	} else {
		$showguide = false;
		foreach($alist as $key => $values) {
			if($key != 'user') {
				foreach($values as $id => $value) {
					if(!isset($list[$key][$id])) {
						$list[$key][$id] = $value;
					}
				}
			}
		}
	}

} elseif(empty($list['user']) && $view != 'other') {
	$archiver = $primary = 0;
	$showguide = false;
}
$showguide = false;
if($showguide) {
	if(!empty($_G['cookie']['lastshowtime'])) {
		$time = explode('|', $_G['cookie']['lastshowtime']);
		$today = strtotime(dgmdate($_G['timestamp'], 'Y-m-d'));
		if($time[0] == $uid && (TIMESTAMP - $time[1] < 86400 && $time[1] > $today)) {
			$showguide = false;
		}
	}
	dsetcookie('lastshowtime', $uid.'|'.TIMESTAMP, 86400);
}

if(!empty($_G['cookie']['lastviewtime'])) {
	$time = explode('|', $_G['cookie']['lastviewtime']);
	if($time[0] == $_G['uid']) {
		$lastviewtime = $time[1];
	}
} else {
	$lastviewtime = getuserprofile('lastactivity');
}
dsetcookie('lastviewtime', $_G['uid'].'|'.TIMESTAMP, 31536000);
if($_G['member']['newprompt_num']['follow']) {
	table_home_notification::t()->delete_by_type('follow', $_G['uid']);
	helper_notification::update_newprompt($_G['uid'], 'follow');
}
$recommend = $users = [];
if(helper_access::check_module('follower')) {
	loadcache('recommend_follow');
	if(empty($_G['cache']['recommend_follow']) || !empty($_G['cache']['recommend_follow']) && (empty($_G['cache']['recommend_follow']['users']) || TIMESTAMP - $_G['cache']['recommend_follow']['dateline'] > 86400)) {
		foreach(table_home_specialuser::t()->fetch_all_by_status(0, 10) as $value) {
			$recommend[$value['uid']] = $value['username'];
		}
		unset($recommend[$_G['uid']]);
		if(count($recommend) < 10) {
			$followuser = table_common_member_count::t()->range_by_field(0, 100, 'follower', 'DESC');
			$userstatus = table_common_member_status::t()->fetch_all_orderby_lastpost(array_keys($followuser), 0, 20);
			$users = table_common_member::t()->fetch_all_username_by_uid(array_keys($userstatus));
		}
		savecache('recommend_follow', ['dateline' => TIMESTAMP, 'users' => $users, 'defaultusers' => $recommend]);
	} else {
		$users = &$_G['cache']['recommend_follow']['users'];
		$recommend = &$_G['cache']['recommend_follow']['defaultusers'];
	}
	if(!empty($users)) {
		if(count($recommend) < 10) {
			$randkeys = array_rand($users, min(count($users), 11 - count($recommend)));
			foreach($randkeys as $ruid) {
				if($ruid != $_G['uid']) {
					$recommend[$ruid] = $users[$ruid];
				}
			}
		}
	}
	if($do == 'following') {
		foreach($list as $ruid => $user) {
			if(isset($recommend[$ruid])) {
				unset($recommend[$ruid]);
			}
		}
	}
	if($recommend) {
		$users = table_home_follow::t()->fetch_all_by_uid_followuid($_G['uid'], array_keys($recommend));
		foreach($users as $ruid => $user) {
			if(isset($recommend[$ruid])) {
				unset($recommend[$ruid]);
			}
		}
	}
}

$navactives = ['feed' => ' class="a"'];
$actives = [$view => ' class="a"'];

list($seccodecheck, $secqaacheck) = seccheck('publish');
	