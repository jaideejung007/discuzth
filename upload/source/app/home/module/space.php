<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$dos = ['index', 'doing', 'blog', 'album', 'friend', 'wall',
	'notice', 'share', 'home', 'pm', 'favorite',
	'thread', 'trade', 'poll', 'activity', 'debate', 'reward', 'profile', 'plugin', 'follow'];

$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos)) ? $_GET['do'] : (!$_G['setting']['homepagestyle'] ? 'profile' : 'index');
if($do == 'index' && !$_G['setting']['homepagestyle']){
	$do = 'profile';
}

if(!in_array($do, ['home', 'doing', 'blog', 'album', 'share', 'wall'])) {
	$_G['mnid'] = 'mn_common';
}
if(empty($_G['uid']) && in_array(getgpc('do'), ['thread', 'trade', 'poll', 'activity', 'debate', 'reward'])) {
	showmessage('login_before_enter_home', null, [], ['showmsg' => true, 'login' => 1]);
}
$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);

$member = [];
if(getgpc('username')) {
	$member = table_common_member::t()->fetch_by_username($_GET['username']);
	if(empty($member) && !($member = table_common_member_archive::t()->fetch_by_username($_GET['username']))) {
		$his = table_common_member_username_history::t()->fetch($_GET['username']);
		if(!$his) {
			showmessage('space_does_not_exist');
		}
		$member = table_common_member::t()->fetch($his['uid']);
	}
	$uid = $member['uid'];
	$member['self'] = $uid == $_G['uid'] ? 1 : 0;
}

if(getgpc('view') == 'admin') {
	$_GET['do'] = $do;
}
if(empty($uid) || in_array($do, ['notice', 'pm'])) $uid = $_G['uid'];
if(empty($_GET['do']) && !isset($_GET['diy'])) {
	if($_G['adminid'] == 1) {
		if($_G['setting']['allowquickviewprofile']) {
			if(!$_G['inajax']) dheader("Location:home.php?mod=space&uid=$uid&do=profile");
		}
	}
	$do = $_GET['do'] = !$_G['setting']['homepagestyle'] ? 'profile' : 'index';
} elseif(empty($_GET['do']) && isset($_GET['diy']) && !empty($_G['setting']['homepagestyle'])) {
	$_GET['do'] = 'index';
}

if($_GET['do'] == 'follow') {
	if($uid != $_G['uid']) {
		$_GET['do'] = 'view';
		$_GET['uid'] = $uid;
	}
	include_once appfile('module/follow');
	exit;
} elseif(empty($_GET['do']) && !$_G['inajax'] && !helper_access::check_module('follow')) {
	$do = 'profile';
}

if($uid && empty($member)) {
	$space = getuserbyuid($uid, 1);
	if(empty($space)) {
		showmessage('space_does_not_exist');
	}
} else {
	$space = &$member;
}

if(empty($space)) {
	if(in_array($do, ['doing', 'blog', 'album', 'share', 'home', 'trade', 'poll', 'activity', 'debate', 'reward', 'group'])) {
		if(empty($_GET['view']) || $_GET['view'] == 'all') {
			$_GET['view'] = 'all';
			$space['uid'] = 0;
			$space['self'] = 0;
		} else {
			showmessage('login_before_enter_home', null, [], ['showmsg' => true, 'login' => 1]);
		}
	} else {
		showmessage('login_before_enter_home', null, [], ['showmsg' => true, 'login' => 1]);
	}
} else {

	$navtitle = $space['username'];

	if($space['status'] == -1 && $_G['adminid'] != 1) {
		showmessage('space_has_been_locked');
	}

	if(in_array($space['groupid'], [4, 5, 6]) && ($_G['adminid'] != 1 && $space['uid'] != $_G['uid'])) {
		$_GET['do'] = $do = 'profile';
	}

	$encodeusername = rawurlencode($space['username']);

	if($do != 'profile' && $do != 'index' && !ckprivacy($do, 'view')) {
		$_G['privacy'] = 1;
		require_once childfile('profile', 'home/space');
		include template('home/space_privacy');
		exit();
	}

	if(!$space['self'] && getgpc('view') != 'eccredit' && getgpc('view') != 'admin') $_GET['view'] = 'me';
}

$diymode = 0;

list($seccodecheck, $secqaacheck) = seccheck('publish');
if($do != 'index') {
	$_G['disabledwidthauto'] = 0;
}
require_once libfile('function/friend');
$isfriend = friend_check($space['uid']);
require_once childfile($do);

