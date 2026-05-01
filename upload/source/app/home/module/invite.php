<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$id = intval($_GET['id']);
$uid = intval($_GET['u']);
$acceptconfirm = false;
if($_G['setting']['regstatus'] < 2) {
	showmessage('not_open_invite', '', [], ['return' => true]);
}
if($_G['uid']) {

	if($_GET['accept'] == 'yes') {
		$cookies = empty($_G['cookie']['invite_auth']) ? [] : explode(',', $_G['cookie']['invite_auth']);

		if(empty($cookies)) {
			showmessage('invite_code_error', '', [], ['return' => true]);
		}
		if(count($cookies) == 3) {
			$uid = intval($cookies[0]);
			$_GET['c'] = $cookies[1];
		} else {
			$id = intval($cookies[0]);
			$_GET['c'] = $cookies[1];
		}
		$acceptconfirm = true;

	} elseif($_GET['accept'] == 'no') {
		dsetcookie('invite_auth', '');
		showmessage('invite_accept_no', 'home.php');
	}
}

if($id) {

	$invite = table_common_invite::t()->fetch($id);

	if(empty($invite) || $invite['code'] != $_GET['c']) {
		showmessage('invite_code_error', '', [], ['return' => true]);
	}
	if($invite['fuid'] && $invite['fuid'] != $_G['uid']) {
		showmessage('invite_code_fuid', '', [], ['return' => true]);
	}
	if($invite['endtime'] && $_G['timestamp'] > $invite['endtime']) {
		table_common_invite::t()->delete($id);
		showmessage('invite_code_endtime_error', '', [], ['return' => true]);
	}

	$uid = $invite['uid'];

	$cookievar = "$id,{$invite['code']}";

} elseif($uid) {

	$id = 0;
	$invite_code = helper_invite::generate_key($uid);
	if($_GET['c'] !== $invite_code) {
		showmessage('invite_code_error', '', [], ['return' => true]);
	}
	$inviteuser = getuserbyuid($uid);
	loadcache('usergroup_'.$inviteuser['groupid']);
	if(!empty($_G['cache']['usergroup_'.$inviteuser['groupid']]) && (!$_G['cache']['usergroup_'.$inviteuser['groupid']]['allowinvite'] || $_G['cache']['usergroup_'.$inviteuser['groupid']]['inviteprice'])) {
		showmessage('invite_code_error', '', [], ['return' => true]);
	}

	$cookievar = "$uid,$invite_code,0";

} else {
	showmessage('invite_code_error', '', [], ['return' => true]);
}

$space = getuserbyuid($uid);
if(empty($space)) {
	showmessage('space_does_not_exist', '', [], ['return' => true]);
}
$jumpurl = 'home.php?mod=space&uid='.$uid;
if($acceptconfirm) {

	dsetcookie('invite_auth', '');

	if($_G['uid'] == $uid) {
		showmessage('should_not_invite_your_own', '', [], ['return' => true]);
	}

	require_once libfile('function/friend');
	if(friend_check($uid)) {
		showmessage('you_have_friends', $jumpurl);
	}

	
	$fields = table_common_member_field_home::t()->fetch($uid);
	if(!$fields['allowasfriend']) {
		showmessage('is_blacklist');
	}

	require_once libfile('function/spacecp');
	if(isblacklist($uid)) {
		showmessage('is_blacklist');
	}

	friend_make($space['uid'], $space['username']);

	if($id) {
		table_common_invite::t()->update($id, ['fuid' => $_G['uid'], 'fusername' => $_G['username'], 'regdateline' => $_G['timestamp'], 'status' => 2]);
		notification_add($uid, 'friend', 'invite_friend', ['actor' => '<a href="home.php?mod=space&uid='.$_G['uid'].'" target="_blank">'.$_G['username'].'</a>'], 1);
	}
	space_merge($space, 'field_home');
	if(is_array($space['privacy']) && !empty($space['privacy']['feed']['invite'])) {
		require_once libfile('function/feed');
		$tite_data = ['username' => '<a href="home.php?mod=space&uid='.$_G['uid'].'">'.$_G['username'].'</a>'];
		feed_add('friend', 'feed_invite', $tite_data, '', [], '', [], [], '', '', '', 0, 0, '', $space['uid'], $space['username']);
	}

	if($_G['setting']['inviteconfig']['inviteaddcredit']) {
		updatemembercount($_G['uid'],
			[$_G['setting']['inviteconfig']['inviterewardcredit'] => $_G['setting']['inviteconfig']['inviteaddcredit']]);
	}
	if($_G['setting']['inviteconfig']['invitedaddcredit']) {
		updatemembercount($uid,
			[$_G['setting']['inviteconfig']['inviterewardcredit'] => $_G['setting']['inviteconfig']['invitedaddcredit']]);
	}

	include_once libfile('function/stat');
	updatestat('invite');

	showmessage('invite_friend_ok', $jumpurl);

} else {
	dsetcookie('invite_auth', $cookievar, 604800);
}

space_merge($space, 'count');
space_merge($space, 'field_home');
space_merge($space, 'profile');
$flist = [];
$query = table_home_friend::t()->fetch_all_by_uid($uid, 0, 12, true);
foreach($query as $value) {
	$value['uid'] = $value['fuid'];
	$value['username'] = $value['fusername'];
	$flist[] = $value;
}
$jumpurl = urlencode($jumpurl);
include_once template('home/invite');

