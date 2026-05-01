<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$extcreditname = 'extcredits'.$_G['setting']['creditstransextra'][1];
$loglist = [];
$logs = table_common_credit_log::t()->fetch_all_by_uid_operation_relatedid(0, 'BTC', $_G['tid']);
$luids = [];
foreach($logs as $log) {
	$luids[$log['uid']] = $log['uid'];
}
$members = table_common_member::t()->fetch_all($luids);
foreach($logs as $log) {
	$log['username'] = $members[$log['uid']]['username'];
	$log['dateline'] = dgmdate($log['dateline'], 'u');
	$log[$extcreditname] = abs($log[$extcreditname]);
	$loglist[] = $log;
}
include template('forum/pay_view');
	