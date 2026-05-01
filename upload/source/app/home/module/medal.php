<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['medalstatus']) {
	showmessage('medal_status_off');
}

loadcache('medals');

if(!$_G['uid'] && $_GET['action']) {
	showmessage('not_loggedin', NULL, [], ['login' => 1]);
}

$_G['mnid'] = 'mn_common';
$medallist = $medallogs = [];
$tpp = 10;
$page = max(1, intval($_GET['page']));
$start_limit = ($page - 1) * $tpp;

$_GET['action'] = empty($_GET['action']) ? 'list' : $_GET['action'];

$file = childfile($_GET['action']);
if(!file_exists($file)) {
	showmessage('undefined_action');
}

require_once $file;

$_GET['action'] == 'list' && $_GET['action'] = '';

$navtitle = lang('core', 'title_medals_list');

include template('home/space_medal');

