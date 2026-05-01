<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = getgpc('op');
if(!in_array($op, ['basic', 'trade', 'team', 'trend', 'modworks', 'memberlist', 'forumstat', 'trend'])) {
	$op = 'basic';
}
if(!$_G['group']['allowstatdata'] && $op != 'trend') {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
}

require_once childfile('function');

$navtitle = lang('core', 'title_stats_'.$op).' - '.lang('core', 'title_stats');

loadcache('statvars');

$file = childfile($op);
if(!file_exists($file)) {
	showmessage('undefined_action');
}
require_once $file;