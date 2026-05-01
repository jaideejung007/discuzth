<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('forum_nopermission', NULL, [$_G['group']['grouptitle']], ['login' => 1]);
}

$file = childfile($_GET['action']);
if(file_exists($file)) {
	require_once $file;
}