<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_G['disabledwidthauto'] = 0;

require_once libfile('function/spacecp');

if(!$_G['setting']['taskstatus']) {
	showmessage('task_close');
}

require_once libfile('class/task');
$tasklib = &task::instance();
$tasklib->update_available();

$_G['mnid'] = 'mn_common';
$id = intval($_GET['id']);
$do = empty($_GET['do']) ? '' : $_GET['do'];

if(empty($_G['uid'])) {
	showmessage('to_login', null, [], ['showmsg' => true, 'login' => 1]);
}

$navtitle = lang('core', 'title_task');

$do = empty($do) ? 'list' : $do;

$file = childfile($do);
if(!file_exists($file)) {
	showmessage('undefined_action');
}

if($do == 'list') {
	$do = '';
} elseif(empty($id)) {
	showmessage('undefined_action');
}

require_once $file;

include template('home/space_task');

function cleartaskstatus() {
	global $_G;
	if(!table_common_mytask::t()->count_mytask($_G['uid'], false, 0)) {
		dsetcookie('taskdoing_'.$_G['uid']);
	}
}

