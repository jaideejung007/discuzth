<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('medals');

$lpp = empty($_GET['lpp']) ? 50 : $_GET['lpp'];
$start_limit = ($page - 1) * $lpp;

$mpurl = ADMINSCRIPT."?action=logs&operation=medal&lpp=$lpp";

$type = $medalid = '';
if(in_array($_GET['opt'], ['0', '1', '2', '3'])) {
	$type = $_GET['opt'];
	$mpurl .= '&opt='.$_GET['opt'];
}
if(!empty($_GET['medalid'])) {
	$medalid = $_GET['medalid'];
	$mpurl .= '&medalid='.$_GET['medalid'];
}

$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';

$check1 = $check2 = [];
$check1[$_GET['medalid']] = 'selected="selected"';
$check2[$_GET['opt']] = 'selected="selected"';

$filters .= '<select onchange="window.location=\''.ADMINSCRIPT.'?action=logs&operation=medal&opt='.$_GET['opt'].'&lpp='.$lpp.'&medalid=\'+this.options[this.selectedIndex].value"><option value="">'.$lang['medals'].'</option><option value="">'.$lang['all'].'</option>';
foreach($_G['cache']['medals'] as $id => $medal) {
	$filters .= '<option value="'.$id.'" '.$check1[$id].'>'.$medal['name'].'</option>';
}
$filters .= '</select>';

$filters .= '<select onchange="window.location=\''.ADMINSCRIPT.'?action=logs&operation=medal&medalid='.$_GET['medalid'].'&lpp='.$lpp.'&opt=\'+this.options[this.selectedIndex].value"><option value="">'.$lang['all'].'</option>';
foreach(['0', '1', '2', '3'] as $o) {
	$filters .= '<option value="'.$o.'" '.$check2[$o].'>'.$lang['logs_medal_operation_'.$o].'</option>';
}
$filters .= '</select>';

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td24"', 'class="td23"', 'class="td23"'], [
	cplang('username'),
	cplang('logs_medal_name'),
	cplang('type'),
	cplang('time'),
	cplang('logs_medal_expiration')
]);

$num = table_forum_medallog::t()->count_by_type_medalid($type, $medalid);
if($num) {
	$multipage = multi($num, $lpp, $page, $mpurl, 0, 3);

	$uids = [];
	$logs = table_forum_medallog::t()->fetch_all_by_type_medalid($type, $medalid, $start_limit, $lpp);
	foreach($logs as $log) {
		$uids[] = $log['uid'];
	}
	$users = table_common_member::t()->fetch_all_username_by_uid($uids);
	foreach($logs as $log) {
		$log['name'] = $_G['cache']['medals'][$log['medalid']]['name'];
		$log['dateline'] = dgmdate($log['dateline'], 'Y-n-j H:i');
		$log['expiration'] = empty($log['expiration']) ? cplang('logs_noexpire') : dgmdate($log['expiration'], 'Y-n-j H:i');
		showtablerow('', ['class="td23"', 'class="td24"', 'class="td23"', 'class="td24"'], [
			"<a href=\"home.php?mod=space&uid=".$log['uid']."\" target=\"_blank\">".$users[$log['uid']],
			$log['name'],
			$lang['logs_medal_operation_'.$log['type']],
			$log['dateline'],
			$log['expiration']
		]);
	}
}
	