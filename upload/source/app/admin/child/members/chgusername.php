<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('chgusernamesubmit')) {

	shownav('user', 'members_chgusername');
	showchildmenu([['nav_members', 'members&operation=list'],
		[$member['username'].' ', 'members&operation=edit&uid='.$member['uid']]], cplang('members_chgusername'));
	showformheader('members&operation=chgusername&uid='.$member['uid']);
	showtableheader();
	showsetting('members_chgusername_oldusername', '', '', $member['username']);
	showsetting('members_chgusername_newusername', 'newusername', $member['username'], 'text', null, null, '');
	showsubmit('chgusernamesubmit');
	showtablefooter();
	showformfooter();

	$hisList = table_common_member_username_history::t()->fetch_all_by_uid(dintval($_GET['uid']));
	showtableheader();
	showsubtitle(['members_chgusername_history_name', 'members_chgusername_history_time']);
	foreach($hisList as $row) {
		showtablerow('', [], [
			$row['username'],
			dgmdate($row['dateline'])
		]);
	}
	showtablefooter();

} else {

	if(empty($member)) {
		cpmsg('members_edit_nonexistence');
	}

	loaducenter();
	uc_user_chgusername(dintval($_GET['uid']), addslashes(trim($_GET['newusername'])), $member['username']);

	cpmsg('members_chgusername_change_success', 'action=members&operation=search', 'succeed');

}
	