<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

foreach(['pid', 'ptid', 'authorid', 'ordertype', 'postno'] as $k) {
	$$k = !empty($_GET[$k]) ? intval($_GET[$k]) : 0;
}

if(empty($_GET['goto']) && $ptid) {
	$_GET['goto'] = 'findpost';
}

if($_GET['goto'] == 'findpost') {
	require_once childfile('findpost');
}

if(empty($_G['thread'])) {
	showmessage('thread_nonexistence');
}

if($_GET['goto'] == 'lastpost') {
	require_once childfile('lastpost');
} elseif($_GET['goto'] == 'nextnewset' || $_GET['goto'] == 'nextoldset') {
	require_once childfile('next');
} else {
	showmessage('undefined_action', NULL);
}

