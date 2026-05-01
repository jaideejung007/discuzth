<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!empty($_GET['fidarray'])) {
	$groups = [];
	$query = table_forum_forum::t()->fetch_all_info_by_fids($_GET['fidarray']);
	foreach($query as $group) {
		$groups[$group['fid']] = $group;
		$fups[$group['fup']]++;
	}
	if(submitcheck('validate')) {
		table_forum_forum::t()->validate_level_for_group($_GET['fidarray']);
		$updateforum = '';
		foreach($groups as $fid => $group) {
			notification_add($group['founderuid'], 'group', 'group_mod_check', ['fid' => $fid, 'groupname' => $group['name'], 'url' => $_G['siteurl'].'forum.php?mod=group&fid='.$fid], 1);
		}
	} elseif(submitcheck('delsubmit')) {
		table_forum_forum::t()->delete_by_fid($_GET['fidarray']);
		table_home_favorite::t()->delete_by_id_idtype($_GET['fidarray'], 'gid');
		table_forum_groupuser::t()->delete_by_fid($_GET['fidarray']);
		$updateforum = '-';
	}
	foreach($fups as $fid => $num) {
		$updateforum && table_forum_forumfield::t()->update_groupnum($fid, $updateforum.$num);
	}
	cpmsg('group_mod_succeed', 'action=group&operation=mod', 'succeed');
}

loadcache('grouptype');
$perpage = 50;
$page = intval($_GET['page']) ? intval($_GET['page']) : 1;
$startlimit = ($page - 1) * $perpage;
$count = table_forum_forum::t()->validate_level_num();
$multipage = multi($count, $perpage, $page, ADMINSCRIPT.'?action=group&operation=mod&submit=yes');
$query = table_forum_forum::t()->fetch_all_validate($startlimit, $startlimit + $perpage);
foreach($query as $group) {
	$groups .= showtablerow('', ['class="td25"', '', ''], [
		"<input type=\"checkbox\" name=\"fidarray[]\" value=\"{$group['fid']}\" class=\"checkbox\">",
		"<a href=\"forum.php?mod=forumdisplay&fid={$group['fid']}\" target=\"_blank\">{$group['name']}</a>",
		empty($_G['cache']['grouptype']['first'][$group['fup']]) ? $_G['cache']['grouptype']['second'][$group['fup']]['name'] : $_G['cache']['grouptype']['first'][$group['fup']]['name'],
		"<a href=\"home.php?mod=space&uid={$group['founderuid']}\" target=\"_blank\">{$group['foundername']}</a>",
		dgmdate($group['dateline'])
	], TRUE);
	$groups .= showtablerow('', ['', 'colspan="4"'], ['', cplang('group_mod_description').'&nbsp;:&nbsp;'.$group['description']], TRUE);
}
shownav('group', 'nav_group_mod');
showsubmenu('nav_group_mod');
showformheader('group&operation=mod');
showtableheader('group_mod_wait');
showsubtitle(['', 'groups_manage_name', 'groups_editgroup_category', 'groups_manage_founder', 'groups_manage_createtime']);
echo $groups;
showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'fidarray\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn" name="validate" value="'.cplang('validate').'" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn" name="delsubmit" value="'.cplang('delete').'" onclick="return confirm(\''.cplang('group_mod_delconfirm').'\')" />', $multipage);
showtablefooter();
showformfooter();
	