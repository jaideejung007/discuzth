<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$modactioncode = lang('forum/modaction');

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td23"'], [
	'ID',
	cplang('operator'),
	cplang('usergroup'),
	cplang('logs_device'),
	cplang('time'),
	cplang('forum'),
	cplang('thread'),
	cplang('action'),
	cplang('reason'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[0] = $logrow['id'];
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = "<a href=\"home.php?mod=space&username=".rawurlencode($data['operator_username'])."\" target=\"_blank\">".($data['operator_username'] != $_G['member']['username'] ? '<b>'.$data['operator_username'].'</b>' : $data['operator_username'])."</a>";
	$log[3] = $usergroup[$data['operator_adminid']];
	$log[4] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';
	$log[6] = "<a href=\"./forum.php?mod=forumdisplay&fid=".$data['forum_fid']."\" target=\"_blank\">".$data['forum_name'].'</a>';
	if(!empty($data['toforum_fid'])) {
		$data['forum_name'] .= " -> <a href=\"./forum.php?mod=forumdisplay&fid=".$data['toforum_fid']."\" target=\"_blank\">".$data['toforum_name'].'</a>';
	}
	$log[8] = "<a href=\"./forum.php?mod=viewthread&tid=".$data['tid']."\" target=\"_blank\" title=\"".$data['subject']."\">".cutstr($data['subject'], 15).'</a>';
	$log[9] = $modactioncode[trim($data['action'])];
	showtablerow('', ['class="bold"'], [
		$log[0],
		$log[2],
		$log[3],
		$log[4],
		$log[1],
		$log[6],
		$log[8],
		$log[9],
		$data['reason'],
	]);
	echo showdevice($logrow['id'], $device, 9);
}
	