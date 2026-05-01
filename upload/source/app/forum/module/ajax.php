<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
const NOROBOT = true;

if(!in_array($_GET['action'], ['checkusername', 'checkemail', 'checkinvitecode', 'checkuserexists', 'quickclear']) && !$_G['setting']['forumstatus']) {
	showmessage('forum_status_off');
}

$file = childfile($_GET['action']);
if(file_exists($file)) {
	require_once $file;
}

showmessage('succeed', '', [], ['handle' => false]);

