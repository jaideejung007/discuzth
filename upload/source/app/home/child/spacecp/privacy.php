<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

space_merge($space, 'field_home');
$operation = in_array(getgpc('op'), ['base', 'feed', 'filter', 'other', 'getgroup']) ? trim($_GET['op']) : 'base';

if(submitcheck('privacysubmit')) {

	if($operation == 'base') {
		$space['privacy']['view'] = [];
		$viewtype = ['index', 'friend', 'wall', 'doing', 'blog', 'album', 'share', 'home', 'videoviewphoto'];
		foreach($_POST['privacy']['view'] as $key => $value) {
			if(in_array($key, $viewtype)) {
				$space['privacy']['view'][$key] = intval($value);
			}
		}
	}

	if($operation == 'feed') {
		$space['privacy']['feed'] = [];
		if(isset($_POST['privacy']['feed'])) {
			foreach($_POST['privacy']['feed'] as $key => $value) {
				$space['privacy']['feed'][$key] = 1;
			}
		}
	}
	privacy_update();

	showmessage('do_success', 'home.php?mod=spacecp&ac=privacy&op='.$operation);

} elseif(submitcheck('privacy2submit')) {

	$space['privacy']['filter_icon'] = [];
	if(isset($_POST['privacy']['filter_icon'])) {
		foreach($_POST['privacy']['filter_icon'] as $key => $value) {
			$space['privacy']['filter_icon'][$key] = 1;
		}
	}
	$space['privacy']['filter_gid'] = [];
	if(isset($_POST['privacy']['filter_gid'])) {
		foreach($_POST['privacy']['filter_gid'] as $key => $value) {
			$space['privacy']['filter_gid'][$key] = intval($value);
		}
	}
	$space['privacy']['filter_note'] = [];
	if(isset($_POST['privacy']['filter_note'])) {
		foreach($_POST['privacy']['filter_note'] as $key => $value) {
			$space['privacy']['filter_note'][$key] = 1;
		}
	}
	privacy_update();

	require_once libfile('function/friend');
	friend_cache($_G['uid']);

	showmessage('do_success', 'home.php?mod=spacecp&ac=privacy&op='.$operation);

} elseif(submitcheck('privacy3submit')) {

	$arr = [];

	if(isset($_POST['allowasfriend'])) {
		$arr['allowasfriend'] = (int)$_POST['allowasfriend'];
	}

	if(isset($_POST['allowasfollow'])) {
		$arr['allowasfollow'] = (int)$_POST['allowasfollow'];
	}

	table_common_member_field_home::t()->update($_G['uid'], $arr);

	showmessage('do_success', 'home.php?mod=spacecp&ac=privacy&op='.$operation);
}

if($operation == 'filter') {
	require_once libfile('function/friend');
	$groups = friend_group_list();

	$filter_icons = empty($space['privacy']['filter_icon']) ? [] : $space['privacy']['filter_icon'];
	$filter_note = empty($space['privacy']['filter_note']) ? [] : $space['privacy']['filter_note'];
	$iconnames = $appids = $icons = $uids = $users = [];
	foreach($filter_icons as $key => $value) {
		list($icon, $uid) = explode('|', $key);
		$icons[$key] = $icon;
		$uids[$key] = $uid;
	}
	foreach($filter_note as $key => $value) {
		list($type, $uid) = explode('|', $key);
		$types[$key] = $type;
		$uids[$key] = $uid;
	}
	if($uids) {
		foreach(table_common_member::t()->fetch_all($uids) as $uid => $value) {
			$users[$uid] = $value['username'];
		}
	}

} elseif($operation == 'other') {

	$arr = [];

	$fields = table_common_member_field_home::t()->fetch($_G['uid']);

	$arr['allowasfriend'][$fields['allowasfriend']] = ' selected="selected"';
	$arr['allowasfollow'][$fields['allowasfollow']] = ' selected="selected"';

} elseif($operation == 'getgroup') {

	$gid = empty($_GET['gid']) ? 0 : intval($_GET['gid']);
	$users = [];
	$query = table_home_friend::t()->fetch_all_by_uid_gid($_G['uid'], $gid, 0, 0, false);
	foreach($query as $value) {
		$users[] = $value['fusername'];
	}
	$ustr = empty($users) ? '' : dhtmlspecialchars(implode(',', $users));
	if(!checkmobile()) {
		showmessage($ustr, '', [], ['msgtype' => 3, 'handle' => false]);
	} else {
		echo $ustr;
		exit();
	}

} else {

	$sels = [];
	if($space['privacy']['view']) {
		foreach($space['privacy']['view'] as $key => $value) {
			$sels['view'][$key] = [$value => ' selected'];
		}
	}
	if($space['privacy']['feed']) {
		foreach($space['privacy']['feed'] as $key => $value) {
			$sels['feed'][$key] = ' checked';
		}
	}
}

$actives = ['privacy' => ' class="a"'];
$opactives = [$operation => ' class="a"'];

include template('home/spacecp_privacy');

