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

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td23"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td24"', 'class="td23"'], [
	'ID',
	cplang('uid'),
	cplang('username'),
	cplang('type'),
	cplang('logs_device'),
	cplang('logs_data'),
	cplang('time'),
	cplang('ip'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[0] = $logrow['id'];
	$log[1] = $logrow['uid'];
	$log[2] = $logrow['username'];
	$log[3] = $data['type'] ? $data['type'] : 'unknown';
	$log[4] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';
	$log[5] = $logrow['data'];
	$log[6] = dgmdate($logrow['dateline']);
	$log[7] = $device['client_ip'];

	showtablerow('', ['class="bold"'], [
		$log[0],
		$log[1],
		$log[2],
		$log[3],
		$log[4],
		$log[5],
		$log[6],
		$log[7],
	]);
	echo showdevice($logrow['id'], $device, 9);
}
	