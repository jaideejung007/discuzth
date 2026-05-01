<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showsubmenu('nav_report', [
	['report_newreport', 'report', 0],
	['report_resolved', 'report&operation=resolved', 0],
	['report_receiveuser', 'report&operation=receiveuser', 1]
]);
if(!$admincp->isfounder) {
	cpmsg('report_need_founder');
}
$report_receive = dunserialize($_G['setting']['report_receive']);
showformheader('report&operation=receiveuser');
showtips('report_receive_tips');
$users = [];
$founders = $_G['config']['admincp']['founder'] !== '' ? explode(',', str_replace(' ', '', addslashes($_G['config']['admincp']['founder']))) : [];
if($founders) {
	$founderexists = true;
	$fuid = $fuser = [];
	foreach($founders as $founder) {
		if(is_numeric($founder)) {
			$fuid[] = $founder;
		} else {
			$fuser[] = $founder;
		}
	}
	if($fuid) {
		$users = table_common_member::t()->fetch_all($fuid);
	}
	if($fuser) {
		$users = $users + table_common_member::t()->fetch_all_by_username($fuser);
	}
}
$query = table_common_admincp_member::t()->fetch_all_uid_by_gid_perm(0, 'report');
foreach($query as $user) {
	if(empty($users[$user['uid']])) {
		$newuids[] = $user['uid'];
	}
}
if($newuids) {
	$users = $users + table_common_member::t()->fetch_all($newuids);
}
$supmoderator = [];
foreach(table_common_member::t()->fetch_all_by_adminid(2) as $uid => $row) {
	if(empty($users[$uid])) {
		$supmoderator[$uid] = $row['username'];
	}
}
showtableheader('<input type="checkbox" name="chkall_admin" id="chkall_admin" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'adminuser\', \'chkall_admin\')" />'.cplang('usergroups_system_1'));
foreach($users as $uid => $member) {
	$username = trim($member['username']);
	if(empty($username) || empty($uid)) continue;
	$checked = is_array($report_receive['adminuser']) && in_array($uid, $report_receive['adminuser']) ? 'checked' : '';
	showtablerow('style="height:20px;width:50px;"', ['class="td25"'], [
		"<input class=\"checkbox\" type=\"checkbox\" name=\"adminuser[]\" value=\"$uid\" $checked>",
		"<a href=\"home.php?mod=space&uid=$uid\" target=\"_blank\">$username</a>"
	]);
}
showtablefooter();

showtableheader('<input type="checkbox" name="chkall_sup" id="chkall_sup" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'supmoderator\', \'chkall_sup\')" />'.cplang('usergroups_system_2'));
foreach($supmoderator as $uid => $username) {
	$username = trim($username);
	if(empty($username) || empty($uid)) continue;
	$checked = is_array($report_receive['supmoderator']) && in_array($uid, $report_receive['supmoderator']) ? 'checked' : '';
	showtablerow('style="height:20px;width:50px;"', ['class="td25"'], [
		"<input class=\"checkbox\" type=\"checkbox\" name=\"supmoderator[]\" value=\"$uid]\" $checked>",
		"<a href=\"home.php?mod=space&uid=$uid\" target=\"_blank\">$username</a>"
	]);
}
showsubmit('', '', '', '<input type="submit" class="btn" name="receivesubmit" value="'.$lang['submit'].'" />');
showtablefooter();
showformfooter();
	