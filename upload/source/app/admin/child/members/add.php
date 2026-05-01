<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('addsubmit')) {

	$groupselect = [];
	$query = table_common_usergroup::t()->fetch_all_by_not_groupid([5, 6, 7]);
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		if($group['type'] == 'member' && $group['creditshigher'] == 0) {
			$groupselect[$group['type']] .= "<option value=\"{$group['groupid']}\" selected>{$group['grouptitle']}</option>\n";
		} else {
			$groupselect[$group['type']] .= "<option value=\"{$group['groupid']}\">{$group['grouptitle']}</option>\n";
		}
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';
	/*search={"nav_members_add":"action=members&operation=add"}*/
	shownav('user', 'nav_members');
	showsubmenu('nav_members', [
		['search', 'members&operation=search', 0],
		['clean', 'members&operation=clean', 0],
		['nav_repeat', 'members&operation=repeat', 0],
		['add', 'members&operation=add', 1],
	]);
	showformheader('members&operation=add');
	showtableheader('members_add');
	showsetting('username', 'newusername', '', 'text');
	showsetting('password', 'newpassword', '', 'text');
	showsetting('email', 'newemail', '', 'text');
	showsetting('usergroup', '', '', '<select name="newgroupid">'.$groupselect.'</select>');
	showsetting('members_add_email_notify', 'emailnotify', '', 'radio');
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();
	/*search*/

} else {

	$newusername = trim($_GET['newusername']);
	$newpassword = trim($_GET['newpassword']);
	$newemail = strtolower(trim($_GET['newemail']));

	if(!$newusername || !isset($_GET['confirmed']) && !$newpassword) {
		cpmsg('members_add_invalid', '', 'error');
	}

	$usernamelen = dstrlen($newusername);
	if($usernamelen < 3) {
		cpmsg('members_add_username_tooshort', '', 'error');
	} elseif($usernamelen > 50) {
		cpmsg('members_add_username_toolong', '', 'error');
	}

	if(table_common_member::t()->fetch_uid_by_username($newusername) || table_common_member_archive::t()->fetch_uid_by_username($newusername)) {
		cpmsg('members_add_username_duplicate', '', 'error');
	}

	loaducenter();

	$uid = uc_user_register(addslashes($newusername), $newpassword, $newemail);
	if($uid <= 0) {
		if($uid == -1) {
			cpmsg('members_add_illegal', '', 'error');
		} elseif($uid == -2) {
			cpmsg('members_username_protect', '', 'error');
		} elseif($uid == -3) {
			if(empty($_GET['confirmed'])) {
				cpmsg('members_add_username_activation', 'action=members&operation=add&addsubmit=yes&newgroupid='.$_GET['newgroupid'].'&newusername='.rawurlencode($newusername), 'form');
			} else {
				list($uid, , $newemail) = uc_get_user(addslashes($newusername));
			}
		} elseif($uid == -4) {
			cpmsg('members_email_illegal', '', 'error');
		} elseif($uid == -5) {
			cpmsg('members_email_domain_illegal', '', 'error');
		} elseif($uid == -6) {
			cpmsg('members_email_duplicate', '', 'error');
		}
	}

	$group = table_common_usergroup::t()->fetch($_GET['newgroupid']);
	$newadminid = in_array($group['radminid'], [1, 2, 3]) ? $group['radminid'] : ($group['type'] == 'special' ? -1 : 0);
	if($group['radminid'] == 1) {
		cpmsg('members_add_admin_none', '', 'error');
	}
	if(in_array($group['groupid'], [5, 6, 7])) {
		cpmsg('members_add_ban_all_none', '', 'error');
	}

	$profile = $verifyarr = [];
	loadcache('fields_register');
	$init_arr = explode(',', $_G['setting']['initcredits']);
	$password = md5(random(10));
	table_common_member::t()->insert_user($uid, $newusername, $password, $newemail, 'Manual Acting', $_GET['newgroupid'], $init_arr, $newadminid);
	if($_GET['emailnotify']) {
		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}
		$add_member_subject = [
			'tpl' => 'add_member',
			'var' => [
				'newusername' => $newusername,
				'bbname' => $_G['setting']['bbname'],
				'adminusername' => $_G['member']['username'],
				'siteurl' => $_G['siteurl'],
				'newpassword' => $newpassword,
			],
		];
		if(!sendmail("$newusername <$newemail>", $add_member_subject)) {
			runlog('sendmail', "$newemail sendmail failed.");
		}
	}

	updatecache('setting');
	cpmsg('members_add_succeed', '', 'succeed', ['username' => $newusername, 'uid' => $uid]);

}