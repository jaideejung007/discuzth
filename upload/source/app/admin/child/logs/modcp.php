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

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td23"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td24"', ''], [
	'ID',
	cplang('operator'),
	cplang('usergroup'),
	cplang('logs_device'),
	cplang('time'),
	cplang('action'),
	cplang('operation'),
	cplang('forum'),
	cplang('other')
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
	preg_match('/operation=(.[^;]*)/i', $data['extralog'], $operationInfo);
	$logExplain = $explainAction[rtrim($data['action']).'_'.$operationInfo[1]] ? $explainAction[rtrim($data['action']).'_'.$operationInfo[1]] : $explainAction[rtrim($data['action'])];
	$log[5] = $logExplain ? $logExplain : rtrim($data['action']);
	$log[6] = $data['op'];
	$log[7] = "<a href=\"forum.php?mod=forumdisplay&fid=".$data['fid']."\" target=\"_blank\">".$data['fid']."</a>";
	showtablerow('', ['class="bold"'], [
		$log[0],
		$log[2],
		$log[3],
		$log[4],
		$log[1],
		$log[5],
		$log[6],
		$log[7],
		'<a href="javascript:;" onclick="togglecplog('.$k.')">'.cutstr($data['extralog'], 200).'</a>',
	]);
	echo '<tbody id="cplog_'.$k.'" style="display:none;">';
	echo '<tr><td colspan="8">'.$data['extralog'].'</td></tr>';
	echo '</tbody>';
	echo showdevice($logrow['id'], $device, 9);
}
	