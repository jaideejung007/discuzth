<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$source = $_GET['source'];
$target = $_GET['target'];
if(!submitcheck('mergesubmit') || $source == $target) {

	require_once libfile('function/forumlist');
	loadcache('forums');
	$forumselect = "<select name=\"%s\">\n<option value=\"\">&nbsp;&nbsp;> ".cplang('select')."</option><option value=\"\">&nbsp;</option>".str_replace('%', '%%', forumselect(FALSE, 0, 0, TRUE)).'</select>';
	shownav('forum', 'forums_merge');
	showsubmenu('forums_merge');
	showformheader('forums&operation=merge');
	showtableheader();
	showsetting('forums_merge_source', '', '', sprintf($forumselect, 'source'));
	showsetting('forums_merge_target', '', '', sprintf($forumselect, 'target'));
	showsubmit('mergesubmit');
	showtablefooter();
	showformfooter();

} else {
	if(table_forum_forum::t()->check_forum_exists([$source, $target]) != 2) {
		cpmsg_error('forums_nonexistence');
	}
	if(table_forum_forum::t()->fetch_forum_num('', $source)) {
		cpmsg_error('forums_merge_source_sub_notnull');
	}

	table_forum_thread::t()->update_by_fid($source, ['fid' => $target]);
	loadcache('posttableids');
	$posttableids = $_G['cache']['posttableids'] ? $_G['cache']['posttableids'] : ['0'];
	foreach($posttableids as $id) {
		table_forum_post::t()->update_fid_by_fid($id, $source, $target);
	}

	$sourceforum = table_forum_forum::t()->fetch_info_by_fid($source);
	$targetforum = table_forum_forum::t()->fetch_info_by_fid($target);
	$sourcethreadtypes = (array)dunserialize($sourceforum['threadtypes']);
	$targethreadtypes = (array)dunserialize($targetforum['threadtypes']);
	$targethreadtypes['types'] = array_merge((array)$targethreadtypes['types'], (array)$sourcethreadtypes['types']);
	$targethreadtypes['icons'] = array_merge((array)$targethreadtypes['icons'], (array)$sourcethreadtypes['icons']);
	table_forum_forum::t()->update($target, ['threads' => $targetforum['threads'] + $sourceforum['threads'], 'posts' => $targetforum['posts'] + $sourceforum['posts']]);
	table_forum_forumfield::t()->update($target, ['threadtypes' => serialize($targethreadtypes)]);
	table_forum_threadclass::t()->update_by_fid($source, ['fid' => $target]);
	table_forum_forum::t()->delete_by_fid($source);
	table_home_favorite::t()->delete_by_id_idtype($source, 'fid');
	table_forum_moderator::t()->delete_by_fid($source);
	table_common_member_forum_buylog::t()->delete_by_fid($target);

	$query = table_forum_access::t()->fetch_all_by_fid_uid($source);
	foreach($query as $access) {
		table_forum_access::t()->insert(['uid' => $access['uid'], 'fid' => $target, 'allowview' => $access['allowview'], 'allowpost' => $access['allowpost'], 'allowreply' => $access['allowreply'], 'allowgetattach' => $access['allowgetattach']], false, true);
	}
	table_forum_access::t()->delete_by_fid($source);
	table_forum_thread::t()->clear_cache([$source, $target], 'forumdisplay_');
	updatecache('forums');

	cpmsg('forums_merge_succeed', 'action=forums', 'succeed');
}
	