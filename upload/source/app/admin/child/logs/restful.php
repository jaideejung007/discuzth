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

showtablerow('class="header"', ['class="td23"', 'class="td23" style="box-sizing: unset;"', 'class="td24" style="box-sizing: unset;"', 'class="td23" style="box-sizing: unset;"', 'style="word-break: break-all"', 'class="td23" style="box-sizing: unset;"', 'class="td23" style="box-sizing: unset;"', 'class="td24" style="box-sizing: unset;"'], [
	'ID',
	cplang('time'),
	'appid',
	'api',
	'params',
	'ret',
	cplang('username'),
	cplang('logs_device'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[0] = $logrow['id'];
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';

	showtablerow('', ['', 'class="bold" style="box-sizing: unset;"', 'style="box-sizing: unset;"', '', 'style="word-break: break-all"'], [
		$log[0],
		$log[1],
		$data['appid'],
		$data['api'],
		'<a href="javascript:;" onclick="display(\'body_'.$logrow['id'].'\')">'.cutstr(print_r($data['params'], 1), 100).'</a>',
		$data['ret'],
		"<a href=\"home.php?mod=space&uid={$logrow['uid']}\" target=\"_blank\">{$logrow['username']}",
		$_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-',
	]);
	echo '<tbody id="body_'.$logrow['id'].'" style="display:none; background-color: #cfd6dd;">'.
		'<tr><td colspan="7"><strong>Request:</strong> '.dhtmlspecialchars(print_r($data['params'], 1)).'</td></tr>'.
		'<tr><td colspan="7"><strong>Response:</strong> '.dhtmlspecialchars(print_r($data['response'], 1)).'</td></tr>'.
		'</tbody>';
	echo showdevice($logrow['id'], $device, 8);
}
