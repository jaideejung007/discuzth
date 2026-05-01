<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['tid']) {
	showmessage('undefined_action');
}
$modactioncode = lang('forum/modaction');
$loglist = [];

foreach(table_forum_threadmod::t()->fetch_all_by_tid($_G['tid']) as $log) {
	$log['dateline'] = dgmdate($log['dateline'], 'u');
	$log['expiration'] = !empty($log['expiration']) ? dgmdate($log['expiration'], 'dt') : '';
	$log['status'] = empty($log['status']) ? 'style="text-decoration: line-through" disabled' : '';
	if(!$modactioncode[$log['action']] && preg_match('/S(\d\d)/', $log['action'], $a) || $log['action'] == 'SPA') {
		loadcache('stamps');
		if($log['action'] == 'SPA') {
			$log['action'] = 'SPA'.$log['stamp'];
			$stampid = $log['stamp'];
		} else {
			$stampid = intval($a[1]);
		}
		$modactioncode[$log['action']] = $modactioncode['SPA'].' '.$_G['cache']['stamps'][$stampid]['text'];
	} elseif(preg_match('/L(\d\d)/', $log['action'], $a)) {
		loadcache('stamps');
		$modactioncode[$log['action']] = $modactioncode['SLA'].' '.$_G['cache']['stamps'][intval($a[1])]['text'];
	}
	if($log['magicid']) {
		loadcache('magics');
		$log['magicname'] = $_G['cache']['magics'][$log['magicid']]['name'];
	}
	$loglist[] = $log;
}

if(empty($loglist)) {
	showmessage('threadmod_nonexistence');
}

$reasons_public = $_G['setting']['modreasons_public'];

include template('forum/viewthread_mod');
	