<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!empty($_GET['delete']) && ($isfounder || table_common_grouppm::t()->count_by_id_authorid($_GET['delete'], $_G['uid']))) {
	if(!empty($_GET['confirm'])) {
		table_common_grouppm::t()->delete($_GET['delete']);
		table_common_member_grouppm::t()->delete_by_gpmid($_GET['delete']);
	} else {
		cpmsg('members_grouppm_delete_confirm', 'action=members&operation=grouppmlist&delete='.intval($_GET['delete']).'&confirm=yes', 'form');
	}
}
shownav('user', 'nav_members_newsletter');
showsubmenu('nav_members_newsletter', [
	['members_grouppmlist_newsletter', 'members&operation=newsletter', 0],
	['members_grouppmlist', 'members&operation=grouppmlist', 1]
]);
if($do) {
	$unreads = table_common_member_grouppm::t()->count_by_gpmid($do, 0);
}

showtableheader();
$id = empty($do) ? 0 : $do;
$authorid = $isfounder ? 0 : $_G['uid'];
$grouppms = table_common_grouppm::t()->fetch_all_by_id_authorid($id, $authorid);
if(!empty($grouppms)) {
	$users = table_common_member::t()->fetch_all(table_common_grouppm::t()->get_uids());
	foreach($grouppms as $grouppm) {
		showtablerow('', ['valign="top" class="td25"', 'valign="top"'], [
			'<a href="home.php?mod=space&uid='.$grouppm['authorid'].'" target="_blank">'.avatar($grouppm['authorid'], 'small').'</a>',
			'<a href="home.php?mod=space&uid='.$grouppm['authorid'].'" target="_blank"><b>'.$users[$grouppm['authorid']]['username'].'</b></a> ('.dgmdate($grouppm['dateline']).'):<br />'.
			$grouppm['message'].'<br /><br />'.
			(!$do ?
				'<a href="'.ADMINSCRIPT.'?action=members&operation=grouppmlist&do='.$grouppm['id'].'">'.cplang('members_grouppmlist_view', ['number' => $grouppm['numbers']]).'</a>' :
				'<a href="'.ADMINSCRIPT.'?action=members&operation=grouppmlist&do='.$grouppm['id'].'&filter=unread">'.cplang('members_grouppmlist_view_unread').'</a>('.$unreads.') &nbsp; '.
				'<a href="'.ADMINSCRIPT.'?action=members&operation=grouppmlist&do='.$grouppm['id'].'&filter=read">'.cplang('members_grouppmlist_view_read').'</a>('.($grouppm['numbers'] - $unreads).')'),
			'<a href="'.ADMINSCRIPT.'?action=members&operation=grouppmlist&delete='.$grouppm['id'].'">'.cplang('delete').'</a>'
		]);
	}
} else {
	showtablerow('', '', cplang('members_newsletter_empty'));
}
showtablefooter();
if($do) {
	showboxheader();
	$_GET['filter'] = in_array($_GET['filter'], ['read', 'unread']) ? $_GET['filter'] : '';
	$filteradd = $_GET['filter'] ? '&filter='.$_GET['filter'] : '';
	$ppp = 100;
	$start_limit = ($page - 1) * $ppp;
	if($_GET['filter'] != 'unread') {
		$count = table_common_member_grouppm::t()->count_by_gpmid($do, 1);
	} else {
		$count = $unreads;
	}
	$multipage = multi($count, $ppp, $page, ADMINSCRIPT."?action=members&operation=grouppmlist&do=$do".$filteradd);
	$alldata = table_common_member_grouppm::t()->fetch_all_by_gpmid($do, $_GET['filter'] == 'read' ? 1 : 0, $start_limit, $ppp);
	$allmember = $alldata ? table_common_member::t()->fetch_all_username_by_uid(array_keys($alldata)) : [];
	foreach($alldata as $uid => $gpmuser) {
		echo '<div style="margin-bottom:5px;float:left;width:24%"><b><a href="home.php?mod=space&uid='.$uid.'" target="_blank">'.$allmember[$uid].'</a></b><br />&nbsp;';
		if($gpmuser['status'] == 0) {
			echo '<span class="lightfont">'.cplang('members_grouppmlist_status_0').'</span>';
		} else {
			echo dgmdate($gpmuser['dateline'], 'u').' '.cplang('members_grouppmlist_status_1');
			if($gpmuser['status'] == -1) {
				echo ', <span class="error">'.cplang('members_grouppmlist_status_-1').'</span>';
			}
		}
		echo '</div>';
	}
	echo $multipage;
	showboxfooter();
}
	