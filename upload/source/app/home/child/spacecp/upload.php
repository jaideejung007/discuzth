<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['albumstatus']) {
	showmessage('album_status_off');
}

$albumid = empty($_GET['albumid']) ? 0 : intval($_GET['albumid']);
$_GET['op'] = in_array($_GET['op'], ['recount', 'cam', 'flash', 'normal']) ? $_GET['op'] : 'normal';

if($_GET['op'] == 'recount') {
	$newsize = table_home_pic::t()->count_size_by_uid($_G['uid']);
	table_common_member_count::t()->update($_G['uid'], ['attachsize' => $newsize]);
	showmessage('do_success', 'home.php?mod=spacecp&ac=upload');
}

if(submitcheck('albumsubmit') && helper_access::check_module('album')) {

	if(!isset($_POST['title']) || !is_array($_POST['title']) || !count($_POST['title'])) {
		showmessage('upload_select_image');
	}
	if($_POST['albumop'] == 'creatalbum') {
		$catid = intval($catid);
		$_POST['albumname'] = empty($_POST['albumname']) ? '' : getstr($_POST['albumname'], 50);
		$_POST['albumname'] = censor($_POST['albumname'], NULL, TRUE);

		if(is_array($_POST['albumname']) && $_POST['albumname']['message']) {
			showmessage($_POST['albumname']['message']);
		}

		if(empty($_POST['albumname'])) $_POST['albumname'] = gmdate('Ymd');

		$_POST['friend'] = intval($_POST['friend']);

		$_POST['target_ids'] = '';
		if($_POST['friend'] == 2) {
			$uids = [];
			$names = empty($_POST['target_names']) ? [] : explode(' ', str_replace([lang('spacecp', 'tab_space'), "\r\n", "\n", "\r"], ' ', $_POST['target_names']));
			if($names) {
				$uids = table_common_member::t()->fetch_all_uid_by_username($names);
			}
			if(empty($uids)) {
				$_POST['friend'] = 3;
			} else {
				$_POST['target_ids'] = implode(',', $uids);
			}
		} elseif($_POST['friend'] == 4) {
			$_POST['password'] = trim($_POST['password']);
			if($_POST['password'] == '') $_POST['friend'] = 0;
		}
		if($_POST['friend'] !== 2) {
			$_POST['target_ids'] = '';
		}
		if($_POST['friend'] !== 4) {
			$_POST['password'] = '';
		}

		$setarr = [];
		$setarr['albumname'] = $_POST['albumname'];
		$setarr['catid'] = intval($_POST['catid']);
		$setarr['uid'] = $_G['uid'];
		$setarr['username'] = $_G['username'];
		$setarr['dateline'] = $setarr['updatetime'] = $_G['timestamp'];
		$setarr['friend'] = $_POST['friend'];
		$setarr['password'] = $_POST['password'];
		$setarr['target_ids'] = $_POST['target_ids'];
		$setarr['depict'] = dhtmlspecialchars($_POST['depict']);

		$albumid = table_home_album::t()->insert($setarr, true);

		if($setarr['catid']) {
			table_home_album_category::t()->update_num_by_catid('1', $setarr['catid']);
		}

		if(empty($space['albumnum'])) {
			$space['albums'] = table_home_album::t()->count_by_uid($space['uid']);
			table_common_member_count::t()->update($_G['uid'], ['albums' => $space['albums']]);
		} else {
			table_common_member_count::t()->increase($_G['uid'], ['albums' => 1]);
		}

	} else {
		$albumid = intval($_POST['albumid']);
		$albuminfo = table_home_album::t()->fetch_album($albumid, $_G['uid']);
		if(empty($albuminfo)) {
			$albumid = 0;
		}
	}
	$havetitle = trim(implode('', $_POST['title']));
	if(!empty($havetitle)) {
		foreach($_POST['title'] as $picid => $title) {
			$title = dhtmlspecialchars($title);
			table_home_pic::t()->update_for_uid($_G['uid'], $picid, ['title' => $title, 'albumid' => $albumid]);
		}
	} else {
		$picids = array_keys($_POST['title']);
		table_home_pic::t()->update_for_uid($_G['uid'], $picids, ['albumid' => $albumid]);
	}
	if($albumid) {
		album_update_pic($albumid);
	}

	if(ckprivacy('upload', 'feed')) {
		require_once libfile('function/feed');
		feed_publish($albumid, 'albumid');
	}

	showmessage('upload_images_completed', "home.php?mod=space&uid={$_G['uid']}&do=album&quickforward=1&id=".(empty($albumid) ? -1 : $albumid));

} else {

	if(!checkperm('allowupload') || !helper_access::check_module('album')) {
		showmessage('no_privilege_upload', '', [], ['return' => true]);
	}

	cknewuser();

	$config = urlencode($_G['siteroot'].'home.php?mod=misc&ac=swfupload&op=config'.($_GET['op'] == 'cam' ? '&cam=1' : ''));

	$albums = getalbums($_G['uid']);

	$actives = ($_GET['op'] == 'flash' || $_GET['op'] == 'cam') ? [$_GET['op'] => ' class="a"'] : ['js' => ' class="a"'];

	$maxspacesize = checkperm('maxspacesize');
	if(!empty($maxspacesize)) {

		space_merge($space, 'count');
		space_merge($space, 'field_home');
		$maxspacesize = $maxspacesize + $space['addsize'] * 1024 * 1024;
		$haveattachsize = ($maxspacesize < $space['attachsize'] ? '-' : '').formatsize($maxspacesize - $space['attachsize']);
	} else {
		$haveattachsize = 0;
	}

	require_once libfile('function/friend');
	$groups = friend_group_list();

	loadcache('albumcategory');
	$category = $_G['cache']['albumcategory'];

	$categoryselect = '';
	if($category) {
		include_once libfile('function/portalcp');
		$categoryselect = category_showselect('album', 'catid', !$_G['setting']['albumcategoryrequired'], $_GET['catid']);
	}
}

$navtitle = lang('core', 'title_'.(!empty($_GET['op']) ? $_GET['op'] : 'normal').'_upload');
require_once libfile('function/upload');
$swfconfig = getuploadconfig($_G['uid'], 0, false);

include_once template('home/spacecp_upload');

