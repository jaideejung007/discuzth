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

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td23"', 'class="td23"', 'class="td23"', '', 'class="td23"'], [
	cplang('username'),
	cplang('usergroup'),
	cplang('time'),
	cplang('logs_rating_username'),
	cplang('logs_rating_rating'),
	cplang('subject'),
	cplang('reason'),
	cplang('logs_device'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = "<a href=\"home.php?mod=space&username=".rawurlencode($data['operator_username'])."\" target=\"_blank\">".$data['operator_username'].'</a>';
	$log[3] = $usergroup[$data['operator_adminid']];
	if($data['member_username'] == $_G['member']['username']) {
		$data['member_username'] = '<b>'.$data['member_username'].'</b>';
	}
	$log[4] = "<a href=\"home.php?mod=space&username=".rawurlencode($data['member_username'])."\" target=\"_blank\">".$data['member_username'].'</a>';
	$log[6] = $_G['setting']['extcredits'][$data['extcredits']]['title'].' '.($data['diff'] < 0 ? '<b>'.$data['diff'].'</b>' : '+'.$data['diff']).' '.$_G['setting']['extcredits'][$data['extcredits']]['unit'];
	$log[7] = $data['tid'] ? "<a href=\"./forum.php?mod=viewthread&tid=".$data['tid']."\" target=\"_blank\" title=\"".$data['subject']."\">".cutstr($data['subject'], 40).'</a>' : "<i>{$lang['logs_rating_manual']}</i>";

	showtablerow('', ['class="bold"'], [
		$log[2],
		$log[3],
		$log[1],
		$log[4],
		(trim($data['d']) == 'D' ? $lang['logs_rating_delete'] : '').$log[6],
		$log[7],
		$data['reason'],
		$_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-',
	]);
	echo showdevice($logrow['id'], $device, 8);
}
	