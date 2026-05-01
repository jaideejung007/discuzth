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
	cplang('uid'),
	cplang('username'),
	cplang('logs_device'),
	cplang('time'),
	cplang('smsgw_send_test_secmobicc'),
	cplang('smsgw_send_test_secmobile'),
	cplang('message'),
	cplang('logs_data'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[0] = $logrow['id'];
	$log[1] = $logrow['uid'];
	$log[2] = $logrow['username'];
	$log[3] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';
	$log[4] = dgmdate($logrow['dateline']);
	$_smsdata = json_decode($logrow['data'], true);
	$log[5] = $_smsdata['secmobicc'];
	$log[6] = $_smsdata['secmobile'];
	$log[7] = $_smsdata['content']['content'] ?? '';
	$log[8] = $_smsdata['ret'];

	showtablerow('', ['class="bold"'], [
		$log[0],
		$log[1],
		$log[2],
		$log[3],
		$log[4],
		$log[5],
		$log[6],
		$log[7],
		$log[8],
	]);
	echo showdevice($logrow['id'], $device, 9);
}
	