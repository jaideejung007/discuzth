<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['formhash'] != FORMHASH) {
	showmessage('undefined_action', NULL);
}
$seccodecheck = true;
if(submitcheck('hiderecoversubmit')) {
	table_forum_threadhidelog::t()->delete_by_tid($_GET['tid']);
	showmessage('thread_hiderecover_success', dreferer());
} else {
	include template('forum/hiderecover');
}
	