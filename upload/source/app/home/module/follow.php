<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(!$_G['uid']) {
	showmessage('login_before_enter_home', null, [], ['showmsg' => true, 'login' => 1]);
}
$dos = ['feed', 'follower', 'following', 'view'];
$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos)) ? $_GET['do'] : (!$_GET['uid'] ? 'feed' : 'view');

if(in_array($do, ['follower', 'following'])){
	if(!$_G['setting']['followerstatus']) {
		showmessage('follower_status_off');
	}
}else{
	if(!$_G['setting']['followstatus']) {
		showmessage('follow_status_off');
	}
}

$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
if($page < 1) $page = 1;
$perpage = 20;
$start = ($page - 1) * $perpage;
$multi = '';
$theurl = 'home.php?mod='.($do == 'view' ? 'space' : 'follow').(!in_array($do, ['feed', 'view']) ? '&do='.$do : '');
$uid = $_GET['uid'] ? $_GET['uid'] : $_G['uid'];
$viewself = $uid == $_G['uid'];
$space = $viewself ? $_G['member'] : getuserbyuid($uid, 1);
if(empty($space)) {
	showmessage('follow_visituser_not_exist');
} elseif(in_array($space['groupid'], [4, 5, 6]) && ($_G['adminid'] != 1 && $space['uid'] != $_G['uid'])) {
	dheader("Location:home.php?mod=space&uid=$uid&do=profile");
}
space_merge($space, 'count');
space_merge($space, 'profile');
space_merge($space, 'field_home');

if($viewself) {
	$showguide = false;
} else {
	$theurl .= $uid ? '&uid='.$uid : '';
	$do = $do == 'feed' ? 'view' : $do;

	$flag = table_home_follow::t()->fetch_status_by_uid_followuid($_G['uid'], $uid);
}
$showrecommend = true;
$archiver = $primary = 1;
$followerlist = [];
$space['bio'] = cutstr($space['bio'], 200);
$lastviewtime = 0;
if($do == 'feed') {

	require_once childfile('feed');

} elseif($do == 'view') {

	require_once childfile('view');

} elseif($do == 'follower') {

	require_once childfile('follower');

} elseif($do == 'following') {

	require_once childfile('following');

}

if(($do == 'follower' || $do == 'following') && $list) {

	require_once childfile('list');

}

if($viewself) {
	if(!isset($_G['cache']['forums'])) {
		loadcache('forums');
	}
	$fields = table_forum_forumfield::t()->fetch_all_by_fid(array_keys($_G['cache']['forums']));
	foreach($fields as $fid => $field) {
		if(!empty($field['threadsorts'])) {
			unset($_G['cache']['forums'][$fid]);
		}
	}
	require_once libfile('function/forumlist');
	$forumlist = forumselect();
	$defaultforum = $_G['setting']['followforumid'] ? $_G['cache']['forums'][$_G['setting']['followforumid']] : [];
	require_once libfile('function/upload');
	$swfconfig = getuploadconfig($_G['uid']);
}

if($do == 'feed') {
	$navigation = ' <em>&rsaquo;</em> <a href="home.php?mod=follow&view='.$view.'">'.lang('space', 'follow_view_'.$view).'</a>';
	$navtitle = lang('space', 'follow_view_'.$view);
} elseif($do == 'view') {
	$navigation = ' <em>&rsaquo;</em> <a href="home.php?mod=space&uid='.$uid.'">'.$space['username'].'</a>';
	if($type != 'feed') {
		$navigation .= ' <em>&rsaquo;</em> '.lang('space', 'follow_view_type_feed').'</a>';
	}
	$navtitle = lang('space', 'follow_view_feed', ['who' => $space['username']]);
} else {
	$navigation = ' <em>&rsaquo;</em> <a href="home.php?mod=space&uid='.$uid.'">'.$space['username'].'</a> <em>&rsaquo;</em> '.lang('space', 'follow_view_'.($viewself ? 'my' : 'do').'_'.$do);
	$navtitle = lang('space', 'follow_view_'.($viewself ? 'my' : 'do').'_'.$do);
}
$metakeywords = $navtitle;
$metadescription = $navtitle;
$navtitle = helper_seo::get_title_page($navtitle, $_G['page']);
include template('diy:home/follow_feed');

