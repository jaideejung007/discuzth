<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showformheader('logs&operation=error', null, null, 'post');
showtableheader('', 'fixpadding');

//search[field]=data&search[key]=backtraceid
$staticurl = STATICURL;
print <<<SEARCH
		<input type="hidden" name="search[field]" value="data">
		<input type="hidden" name="search[key]" value="hash">
		<tr class="hover">
			<td class="td23">BackTraceID: </td><td width="160"><input type="text" class="txt" style="width:200px" name="search[hash]" value="{$_GET['search'][$_GET['search']['key']]}" /></td>
			<td class="td23"><input type="submit" name="crimesearch" value="{$lang['search']}" class="btn" /></td><td></td>
		</tr>
SEARCH;
showformfooter();

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td23" style="box-sizing: unset;"', 'class="td24" style="box-sizing: unset;"', 'class="td23" style="box-sizing: unset;"'], [
	'ID',
	cplang('time'),
	cplang('logs_device'),
	cplang('message'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[0] = $logrow['id'];
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';

	showtablerow('', ['class="bold" style="box-sizing: unset;"', 'style="box-sizing: unset;"'], [
		$log[0],
		$log[1],
		$log[2],
		str_replace(' -> ', '<br>', $data['message']).'<br>hash:'.$data['hash'].'<br>clientip:'.$data['clientip'].'<br>'.$data['user'].'<br>'.$data['uri']
	]);
	echo showdevice($logrow['id'], $device, 4);
}
	