<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtips('logs_tips_ban');

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td24"', 'class="td24"', 'class="td23"', 'class="td23"', 'class="td23"', 'class="td25"', 'style="width:160px"', 'class="td23"'], [
	'ID',
	cplang('operator'),
	cplang('logs_device'),
	cplang('time'),
	cplang('username'),
	cplang('operation'),
	cplang('logs_banned_group'),
	cplang('validity'),
	cplang('reason'),
]);
$operations = [1 => '<b>'.$lang['logs_lock'].'</b>', 2 => '<b>'.$lang['logs_unlock'].'</b>', 3 => '<i>'.$lang['logs_banned_unban'].'</i>', 4 => '<b>'.$lang['logs_banned_ban'].'</b>'];
$extrainput = '&nbsp;'.cplang('operation').': <select name="filter"><option></option>';
foreach($operations as $k => $v) {
	$extrainput .= '<option value="'.$k.'"'.($_GET['filter'] == $k ? ' selected="selected"' : '').'>'.strip_tags($v).'</option>';
}
$extrainput .= '</select>';

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[0] = $logrow['id'];
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = "<a href=\"home.php?mod=space&username=".rawurlencode($data['operator_username'])."\" target=\"_blank\">".$data['operator_username']."</a> <span class=\"normal\">".$usergroup[$data['operator_groupid']].'</span>';
	$log[4] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';
	$log[5] = "<a href=\"home.php?mod=space&username=".rawurlencode($data['username'])."\" target=\"_blank\">".$data['username'].'</a>';
	$log[8] = trim($data['expiration']) ? dgmdate($data['expiration']) : '';
	if($data['status'] == -1) {
		$operation = 1;
	} else {
		if($data['origgroupid'] == $data['newgroupid']) {
			$operation = 2;
		} else {
			$operation = (in_array($data['origgroupid'], [4, 5]) && !in_array($data['newgroupid'], [4, 5])) ? 3 : 4;
		}
	}
	if(!empty($_GET['filter']) && $_GET['filter'] != $operation) {
		continue;
	}
	$operation = $operations[$operation];

	showtablerow('', ['', 'class="bold"', 'class="smallefont"', 'class="smallefont"', '', '', '', 'class="smallefont"', ''], [
		$log[0],
		$log[2],
		$log[4],
		$log[1],
		$log[5],
		$operation,
		"{$usergroup[$data['origgroupid']]} / {$usergroup[$data['newgroupid']]}",
		$log[8],
		$data['reason']
	]);
	echo showdevice($logrow['id'], $device, 9);
}
	