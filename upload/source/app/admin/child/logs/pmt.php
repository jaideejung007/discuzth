<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td31"', ''], [
	cplang('time'),
	cplang('logs_payment_channel'),
	cplang('logs_payment_status'),
	cplang('logs_payment_order'),
	cplang('operator'),
	cplang('logs_device'),
	cplang('logs_payment_error'),
]);

$channels = payment::channels();

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = $channels[$data['channel']]['title'];
	$log[3] = cplang('logs_payment_status_'.$data['status']);
	$log[6] = $data['clientip'].':'.$data['remoteport'];
	$log[8] = cplang('payment_error_'.$data['error']);
	showtablerow('', ['class="bold"'], [
		$log[1],
		$log[2],
		$log[3],
		$data['order_id'],
		$data['uid'],
		$_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-',
		'<a href="javascript:;" onclick="togglecplog('.$k.')">'.$log[8].'</a>',
	]);
	echo '<tbody id="cplog_'.$k.'" style="display:none;">';
	echo '<tr><td colspan="6">'.$data['data'].'</td></tr>';
	echo '</tbody>';
	echo showdevice($logrow['id'], $device, 7);
}
	