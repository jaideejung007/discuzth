<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_G['adminid'] != 1) {
	exit;
}

$allowfuntype = ['portal', 'forum', 'friend', 'follower', 'group', 'follow', 'collection', 'guide', 'feed', 'blog', 'doing', 'album', 'share', 'wall', 'homepage', 'ranklist', 'medal', 'task', 'magic', 'favorite', 'pm'];
$type = in_array($_GET['type'], $allowfuntype) ? trim($_GET['type']) : '';
$do = in_array($_GET['do'], ['open', 'close']) ? $_GET['do'] : 'close';
if(!submitcheck('funcsubmit', true)) {
	$navtitle = lang('spacecp', $do == 'open' ? 'select_the_navigation_position' : 'close_module', ['type' => lang('spacecp', $type)]);
	$closeprompt = lang('spacecp', 'close_module', ['type' => lang('spacecp', $type)]);
	include template('common/header_ajax');
	include template('admin/setnav');
	include template('common/footer_ajax');
} else {
	if(!empty($type)) {
		$funkey = $type.'status';
		$funstatus = $do == 'open' ? 1 : 0;
		if($type != 'homepage') {
			$identifier = ['portal' => 1, 'forum' => 2, 'group' => 3, 'feed' => 4, 'ranklist' => 8, 'follow' => 9, 'guide' => 10, 'collection' => 11, 'blog' => 12, 'album' => 13, 'share' => 14, 'doing' => 15, 'friend' => 26, 'favorite' => 27, 'medal' => 29, 'task' => 30, 'magic' => 31];
			$navdata = ['available' => -1];
			$navtype = $do == 'open' ? [] : [0, 3];
			if(in_array($type, ['blog', 'album', 'share', 'doing', 'follow', 'friend', 'favorite', 'medal', 'task', 'magic'])) {
				$navtype[] = 2;
			}
			if($do == 'close' && $type == 'medal') {
				if(intval(table_forum_medal::t()->count_by_available()) > 0) {
					showmessage('medals_existence', dreferer(), [], ['showdialog' => true, 'locationtime' => true]);
					exit;
				}
			}
			if($do == 'close' && $type == 'forum') {
				if($_G['setting']['groupstatus'] || $_G['setting']['guidestatus'] || $_G['setting']['collectionstatus'] || $_G['setting']['followstatus']) {
					showmessage('close_ggcf_before_close_forum', dreferer(), [], ['showdialog' => true, 'locationtime' => true]);
					exit;
				}
			}
			if($do == 'open' && in_array($type, ['group', 'guide', 'collection', 'follow'])) {
				if(!$_G['setting']['forumstatus']) {
					showmessage('open_forum_before_open_ggcf', dreferer(), [], ['showdialog' => true, 'locationtime' => true]);
					exit;
				}
			}
			if($do == 'open') {
				if($_GET['location']['header']) {
					$navtype[] = 0;
					$navdata['available'] = 1;
				}
				if($_GET['location']['quick']) {
					$navtype[] = 3;
					$navdata['available'] = 1;
				}
				$navdata['available'] = $navdata['available'] == 1 ? 1 : 0;
				if(empty($_GET['location']['header']) || empty($_GET['location']['quick'])) {
					table_common_nav::t()->update_by_navtype_type_identifier([0, 2, 3], 0, ["$type", "$identifier[$type]"], ['available' => 0]);
				}
			}
			if($navtype) {
				table_common_nav::t()->update_by_navtype_type_identifier($navtype, 0, ["$type", "$identifier[$type]"], $navdata);
				if(in_array($type, ['blog', 'album', 'share', 'doing', 'follow']) && !$navdata['available']) {
					table_common_nav::t()->update_by_navtype_type_identifier([2], 0, ["$type"], ['available' => 1]);
				}
			}
		}
		table_common_setting::t()->update_setting($funkey, $funstatus);

		$setting[$funkey] = $funstatus;
		if(!function_exists('updatecache')) {
			require_once libfile('function/cache');
		}
		updatecache('setting');
	}
	showmessage('do_success', dreferer(), [], ['showdialog' => true, 'locationtime' => true]);
}
exit;
	