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

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td24"', 'class="td23"', 'class="td23"'], [
	'ID',
	cplang('time'),
	cplang('logs_device'),
	cplang('username'),
	cplang('reason'),
]);

$logarr = $loguids = [];
foreach($logs as $logrow) {
	$data = json_decode($logrow['data'], true);
	$log = $data;
	if(empty($log['timestamp'])) {
		continue;
	}
	if(!$log['message']) {
		continue;
	}
	$log['uid'] = intval($log['uid']);
	$loguids[] = $log['uid'];
}

$members = table_common_member::t()->fetch_all_username_by_uid($loguids);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = $data;
	$log[6] = $members[$log['uid']];
	showtablerow('', ['class="smallefont"', 'class="bold"', 'class="smallefont"'], [
		$logrow['id'],
		dgmdate($logrow['dateline']),
		$_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-',
		'<a href="home.php?mod=space&username='.$log[6].'" target="_blank">'.$log[6].'</a>',
		$data['message']
	]);
	echo showdevice($logrow['id'], $device, 5);
}
	