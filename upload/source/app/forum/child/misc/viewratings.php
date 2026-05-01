<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_GET['pid']) {
	showmessage('undefined_action');
}
$loglist = $logcount = [];

$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid']);
if($post['invisible'] != 0) {
	$post = [];
}
if($post) {
	$loglist = table_forum_ratelog::t()->fetch_all_by_pid($_GET['pid']);
}
if(empty($post) || empty($loglist)) {
	showmessage('thread_rate_log_nonexistence');
}
if($post['tid'] != $thread['tid']) {
	showmessage('targetpost_donotbelongto_thisthread');
}
if($_G['setting']['bannedmessages']) {
	$postmember = getuserbyuid($post['authorid']);
	$post['groupid'] = $postmember['groupid'];
}

foreach($loglist as $k => $log) {
	$logcount[$log['extcredits']] += $log['score'];
	$log['dateline'] = dgmdate($log['dateline'], 'u');
	$log['score'] = $log['score'] > 0 ? '+'.$log['score'] : $log['score'];
	$log['reason'] = dhtmlspecialchars($log['reason']);
	$loglist[$k] = $log;
}

include template('forum/rate_view');
	