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
	cplang('email'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$log = $data;
	if(empty($log['timestamp'])) {
		continue;
	}
	$log['message'] = trim(str_replace('sendmail failed.', '', $log['message']));
	if(!$log['message']) {
		continue;
	}
	$logemail[] = $log['message'];
}

$members = table_common_member::t()->fetch_all_by_email($logemail);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = $data;
	$log['message'] = trim(str_replace('sendmail failed.', '', $log['message']));
	$logrow[6] = $members[$log['message']]['username'];
	if(strtolower($logrow[6]) == strtolower($_G['member']['username'])) {
		$logrow[6] = "<b>$logrow[6]</b>";
	}
	showtablerow('', ['class="smallefont"', 'class="smallefont"', 'class="bold"', 'class="smallefont"'], [
		$logrow['id'],
		dgmdate($logrow['dateline']),
		$_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-',
		'<a href="home.php?mod=space&username='.$logrow[6].'" target="_blank">'.$logrow[6].'</a>',
		$log['message']
	]);
	echo showdevice($logrow['id'], $device, 5);
}
	