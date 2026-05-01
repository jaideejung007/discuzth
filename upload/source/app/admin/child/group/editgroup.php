<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/group');
$fid = intval($_GET['fid']);
if(empty($fid)) {
	cpmsg('group_nonexist', 'action=group&operation=manage', 'error');
}
$group = table_forum_forum::t()->fetch_info_by_fid($fid);
require_once libfile('function/editor');
$group['description'] = html2bbcode($group['description']);

if(!$group || $group['status'] != 3 || $group['type'] != 'sub') {
	cpmsg('group_nonexist', '', 'error');
}

require_once libfile('function/group');
require_once libfile('function/discuzcode');
$groupicon = get_groupimg($group['icon'], 'icon');
$groupbanner = get_groupimg($group['banner']);
$jointypeselect = [['-1', cplang('closed')], ['0', cplang('public')], ['1', cplang('invite')], ['2', cplang('moderate')]];
if(!submitcheck('editsubmit')) {
	$groupselect = get_groupselect(0, $group['fup'], 0);
	shownav('group', 'nav_group_manage');
	showchildmenu([['nav_group_manage', 'group&operation=manage']], $group['name']);

	showformheader("group&operation=editgroup&fid=$fid", 'enctype');
	showtableheader();
	showsetting('groups_editgroup_name', 'namenew', $group['name'], 'text');
	showsetting('groups_editgroup_category', '', '', '<select name="fupnew">'.$groupselect.'</select>');
	showsetting('groups_editgroup_jointype', ['jointypenew', $jointypeselect], $group['jointype'], 'select');
	showsetting('groups_editgroup_visible_all', 'gviewpermnew', $group['gviewperm'], 'radio');
	showsetting('groups_editgroup_description', 'descriptionnew', $group['description'], 'textarea');
	if($groupicon) {
		$groupicon = '<input type="checkbox" class="checkbox" name="deleteicon" value="yes" /> '.$lang['delete'].'<br /><img src="'.$groupicon.'?'.random(6).'" width="48" height="48" />';
	}
	if($groupbanner) {
		$groupbanner = '<input type="checkbox" class="checkbox" name="deletebanner" value="yes" /> '.$lang['delete'].'<br /><img src="'.$groupbanner.'?'.random(6).'" />';
	}
	showsetting('groups_editgroup_icon', 'iconnew', '', 'file', '', 0, $groupicon);
	showsetting('groups_editgroup_banner', 'bannernew', '', 'file', '', 0, $groupbanner);
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

} else {
	$_GET['jointypenew'] = intval($_GET['jointypenew']);
	$_GET['fupnew'] = intval($_GET['fupnew']);
	$_GET['gviewpermnew'] = intval($_GET['gviewpermnew']);
	require_once libfile('function/discuzcode');
	$_GET['descriptionnew'] = discuzcode(dhtmlspecialchars(censor(trim($_GET['descriptionnew']))), 0, 0, 0, 0, 1, 1, 0, 0, 1);
	$_GET['namenew'] = dhtmlspecialchars(censor(trim($_GET['namenew'])));
	$icondata = [];
	$iconnew = upload_icon_banner($group, $_FILES['iconnew'], 'icon');
	$bannernew = upload_icon_banner($group, $_FILES['bannernew'], 'banner');
	if($iconnew) {
		$icondata['icon'] = $iconnew;
	}
	if($bannernew) {
		$icondata['banner'] = $bannernew;
	};

	if($_GET['deleteicon']) {
		@unlink($_G['setting']['attachurl'].'group/'.$group['icon']);
		ftpcmd('delete', 'common/'.$group['icon']);
		$icondata['icon'] = '';
	}
	if($_GET['deletebanner']) {
		@unlink($_G['setting']['attachurl'].'group/'.$group['banner']);
		ftpcmd('delete', 'common/'.$group['banner']);
		$icondata['banner'] = '';
	}
	$groupdata = array_merge($icondata, [
		'description' => $_GET['descriptionnew'],
		'gviewperm' => $_GET['gviewpermnew'],
		'jointype' => $_GET['jointypenew'],
	]);
	table_forum_forumfield::t()->update($fid, $groupdata);
	$setarr = [];
	if($_GET['fupnew']) {
		$setarr['fup'] = $_GET['fupnew'];
	}
	if($_GET['namenew'] && $_GET['namenew'] != $group['name'] && table_forum_forum::t()->fetch_fid_by_name($_GET['namenew'])) {
		cpmsg('group_name_exist', 'action=group&operation=editgroup&fid='.$fid, 'error');
	}
	trim($_GET['namenew']) && $setarr['name'] = $_GET['namenew'];
	table_forum_forum::t()->update($fid, $setarr);

	if(!empty($_GET['fupnew']) && $_GET['fupnew'] != $group['fup']) {
		table_forum_forumfield::t()->update_groupnum($_GET['fupnew'], 1);
		table_forum_forumfield::t()->update_groupnum($group['fup'], -1);
		require_once libfile('function/cache');
		updatecache('grouptype');
	}

	cpmsg('group_edit_succeed', 'action=group&operation=editgroup&fid='.$fid, 'succeed');
}
	