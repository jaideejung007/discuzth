<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('usergroups');

$source = intval($_GET['source']);
$sourceusergroup = $_G['cache']['usergroups'][$source];

if(empty($sourceusergroup) || $sourceusergroup['type'] == 'system' || ($sourceusergroup['type'] == 'special' && $sourceusergroup['radminid'])) {
	cpmsg('usergroups_copy_source_invalid', '', 'error');
}

if(!submitcheck('copysubmit')) {

	$groupselect = [];
	foreach(table_common_usergroup::t()->fetch_all_not([6, 7], true) as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= "<option value=\"{$group['groupid']}\">{$group['grouptitle']}</option>\n";
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '');

	$usergroupselect = '<select name="target" size="10">'.$groupselect.'</select>';

	shownav('user', 'usergroups_merge');
	showchildmenu([['nav_usergroups', 'usergroups']], cplang('usergroups_merge'));
	showtips('usergroups_merge_tips');
	showformheader('usergroups&operation=merge');
	showhiddenfields(['source' => $source]);
	showtableheader();
	showtitle('usergroups_copy');
	showsetting(cplang('usergroups_copy_source').':', '', '', $sourceusergroup['grouptitle']);
	showsetting('usergroups_merge_target', '', '', $usergroupselect);
	showsetting('usergroups_merge_delete_source', 'delete_source', 0, 'radio');
	showsubmit('copysubmit');
	showtablefooter();
	showformfooter();

} else {

	$target = intval($_GET['target']);
	$targetusergroup = $_G['cache']['usergroups'][$target];

	if(empty($targetusergroup) || $targetusergroup['type'] == 'system' || ($targetusergroup['type'] == 'special' && $targetusergroup['radminid'])) {
		cpmsg('usergroups_copy_target_invalid', '', 'error');
	}

	table_common_member::t()->update_groupid_by_groupid($source, $target);
	if(helper_dbtool::isexisttable('common_member_archive')) {
		table_common_member_archive::t()->update_groupid_by_groupid($source, $target);
	}

	if($_GET['delete_source']) {
		table_common_usergroup::t()->delete_usergroup($source, $sourceusergroup['type']);
		table_common_usergroup_field::t()->delete($source);
		table_forum_onlinelist::t()->delete_by_groupid($source);
	}

	updatecache('usergroups');
	cpmsg('usergroups_merge_succeed', 'action=usergroups', 'succeed');

}
	